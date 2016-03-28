var MESSAGE_TYPE = {
	"NOTICE": 	'rgb(255,255,255)',
	"ERROR":	'rgb(255,0,0)'
}

var SERVER_MESSAGES = require('./server/classes/network/messages/server_messages');

var mongoose = require('mongoose');
var crypto = require('crypto');
var ent = require('ent');
var readLine = require('readline');

if (process.platform === "win32"){
    var rl = readLine.createInterface ({
        input: process.stdin,
        output: process.stdout
    });

    rl.on ("SIGINT", function (){
        process.emit ("SIGINT");
    });

}



function GameServer(ip, port) {

	var self = this;
	
	this.express = require("express")
  , this.app = this.express()
  , this.http = require("http").createServer(this.app)
  , this.bodyParser = require("body-parser")
  , this.io = require("socket.io").listen(this.http)
  , this._ = require("underscore")
  , this.logger = require("logger").createLogger()
  , this.db = mongoose.connect('mongodb://localhost/GSL').connection;
  
  //Server's IP address
	this.app.set("ipaddr", ip);

	//Server's port number 
	this.app.set("port", port);

	//Specify the views folder
	this.app.set("views", __dirname + "/views");

	//Specify where the static content is
	this.app.use(this.express.static("public", __dirname + "/public"));

	//Tells server to support JSON requests
	this.app.use(this.bodyParser.json());
	
	/*
	* GAME LOGIC
	*
	*/
	//this.peers = []; //contains User model instances
	
	
	
	/*
	* DATABASE
	*/
	this.db.on('error', console.error.bind(console, 'connection error:'));
	this.db.once('open', function() {
		console.log('Connected to database.');
		
		try {
		
			//create server data (maps, ...)
			self.serverData = {
				Maps : require('./server/classes/data/MapData').loadMapsData()
			}
			
			//create an object to access database models we may need
			require('./server/schemas/UserSchema')();
			require('./server/schemas/MonsterSchema')();
			
			self.dbmodels = {
				User: mongoose.model('User'),
				Monster: mongoose.model('Monster')
			}
			
			//create helpers
			self.helpers = {
				User: require('./server/classes/network/helpers/UserHelper'),
				Map: require('./server/classes/network/helpers/MapHelper')
			}
			
			//init various things
			self.initRouting();
			self.initNetworkProtocol();
			
			//finally, start the server once everything is set
		
			self.start();
			
		} catch(err) {
		
			switch(err.name) {
				case 'SyntaxError':
				case 'ReferenceError':
				case 'TypeError':
					server.log(err);
					//disconnect all players, server will definately crash if it gets here
					this.close();
					break;
				default:
					server.log(err);
					break;
			}
		}
	});
}

/*
* Init server routing
*/
GameServer.prototype.initRouting = function() {
	//Handle route "GET /", as in "http://localhost:8080/"
	this.app.get("/", function(request, response) {

	  //Render the view
	  response.render("map.ejs", { mapName: 'map1'});

	});
}

/*
* Network
*
*/
/*GameServer.prototype.broadcastToAll = function(messageType, data) {
	var self = this;
	var peersIds = Object.keys(self.peers);
	
	for(var id in peersIds) {
		self.peers[id].socket.emit(messageType, data);
	}
}

GameServer.prototype.broadcastToOthers = function(emitter, messageType, data) {
	var self = this;
	var peersIds = Object.keys(self.peers);
	
	for(var id = 0 ; id < peersIds.length ; id++) {
		if(peersIds[id] == emitter.id) continue;
		self.peers[ peersIds[id] ].socket.emit(messageType, data);
	}
}*/

GameServer.prototype.isLoggedIn = function(sessionId, callback) {
	this.dbmodels.User.findOne({sessionId: sessionId, isLoggedIn: true}, function(err, user) {
		if(err) throw err;
		
		callback(user);
	});
	
	/*for(var i = 0 ; i < this.peers.length ;  i++) {
		if(this.peers[i].sessionId == sessionId) return true;
	}
	
	return false;*/
}

GameServer.prototype.loginAlreadyConnected = function(login, callback) {
	this.dbmodels.User.findOne({login: login, isLoggedIn: true}, function(err, user) {
		if(err) throw err;
		
		callback(user);
	});
	/*for(var p in this.peers) {
		if(p.login == login) return true;
	}
	
	return false;*/
}

/*GameServer.prototype.getRemotePeer = function(sessionId) {
	for(var i = 0 ; i < this.peers.length ; i++) {
		if(this.peers[i].sessionId == sessionId) return this.peers[i];
	}
	
	console.log("Failed to get remote peer with sessionId: " + socket.id);
	return false;
}

GameServer.prototype.removePeer = function(sessionId) {
	for(var i = 0 ; i < this.peers.length ; i++) {
		if(this.peers[i].sessionId == sessionId) {
			this.peers.splice(i, 1);
		}
	}
}*/

GameServer.prototype.initNetworkProtocol = function() {

	var self = this;
	
	try {

		//a peer connects
		this.io.sockets.on('connection', function(socket) {
		
			if(!socket) return;
			
			//update client lastOnlineTime (needed to disconnect users who timed out)
			socket.on('anything', function(data) {
				console.log(socket.id + ' sent a packet.');
				self.dbmodels.User.findOne({sessionId: socket.id, isLoggedIn: true}, function(err, user) {
					if(err) throw err;
					if(!user) return;
					
					user.lastOnlineTime = Data.now();
					user.save(function(err) {
						if(err) throw err;
					});
				});
			});
			
			//chatbox message
			socket.on('chatbox_msg', function(msg) {
				msg = ent.encode(msg);
				
				//ignore if not logged in
				server.dbmodels.User.isLoggedIn(socket.id, function(user) {
					if(!user) {
						return;
					}
					
					//socket.broadcast.to(user.playerData.mapData.map).emit('server_msg', new SERVER_MESSAGES.SM_SERVER_MESSAGE(user.login, msg).toObject());
					var packet = new SERVER_MESSAGES.SM_SERVER_MESSAGE(user.login, msg);
					packet.broadcast(socket, user.playerData.mapData.map);
					packet.emit(socket);
					
					//socket.emit('server_msg', new SERVER_MESSAGES.SM_SERVER_MESSAGE(user.login, msg).toObject());
				});
			});
			
			
			//client is trying to login
			socket.on('login', function(login, password) {
				try{
					self.helpers.User.loginUser(login, password, self, socket);
				} catch(e) {
					self.log(e.message);
				}
				
			});
			
			//client logout
			socket.on('logout', function() {
				try{
					self.helpers.User.logoutUser(self, socket);
					socket.emit('logout_success');
				} catch(e) {
					self.log(e);
				}
			});
			
			//client disconnects
			socket.on('disconnect', function() {
				try{
					self.helpers.User.logoutUser(self, socket);
				} catch(e) {
					self.log(e);
				}
			});
			
			//player moves
			socket.on('player_move', function(x, y, dir) {
				//ignore if not logged in (should not happen)
				self.dbmodels.User.isLoggedIn(socket.id, function(user) {
					if(!user) {
						return;
					}
					
					//update position in database
					user.playerData.mapData.position.x = x;
					user.playerData.mapData.position.y = y;
					user.playerData.mapData.direction = dir;
					user.save(function(err) {
						if(err) throw err;
						
						//broadcast to all others players (on the same map)
						new SERVER_MESSAGES.SM_PLAYER_MOVE(user).broadcast(socket, user.playerData.mapData.map);
						
						//check for any event that could start if player moves on them
						var events = self.serverData.Maps[user.playerData.mapData.map].getEvents(x, y);
						
						if(events) {
							for(var i = events.length - 1 ; i >=  0 ; i--) {
								var ev = events[i];
								
								if(ev.trigger == "player_contact") {
									if(ev.direction === undefined || (ev.direction == dir)) {
										ev.execute(user, server, socket);
									}
								}
							}
						}
					});
				});
			});
			
		});
	
	} catch(e) {
		console.log(e.message);
	}
}


/*
* Start the server.
*/
GameServer.prototype.start = function() {
	var ip = this.app.get("ipaddr"), port = this.app.get("port");
	
	this.http.listen(port, ip, function() {
	  console.log("Server up and running. Go to http://" + ip + ":" + port);
	});
}

GameServer.prototype.close = function() {
	//disconnect all players
	if(this.dbmodels.User) {
		this.dbmodels.User.disconnectAll(function() {
			mongoose.connection.close();
			console.log('Server closed.');
			process.exit();
		});	
	} else {
		console.log('Server closed.');
		process.exit();
	}
}

GameServer.prototype.log = function(msg) {
	console.log(msg);
}

var server = new GameServer("0.0.0.0", 8080);

process.on ("SIGINT", function(){
  //graceful shutdown
  server.close();
});