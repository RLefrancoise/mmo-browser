var ent = require('ent');
var crypto = require('crypto');
var SERVER_MESSAGES = require('../messages/server_messages');

exports.userJoinRoom = function(user, room, server, socket, callback) {
	
	socket.join(room, function(err) {
		if(err) throw err;
		
		server.log(user.login + ' joins room "' + room + '"');
		if(callback) callback();
	});
}

exports.userLeaveRoom = function(user, room, server, socket, callback) {
	socket.leave(room, function(err) {
		if(err) throw err;
		
		server.log(user.login + ' leaves room "' + room + '"');
		if(callback) callback();
	})
}

exports.loginUser = function(login, password, server, socket) {
			
	if(!login || !password || !server || !socket) {
		throw new Error("[" + __dirname + "/UserHelper.js] loginUser: invalid parameter");
	}
	
	//ignore if already logged in (should not happen)
	server.dbmodels.User.isLoggedIn(socket.id, function(user) {
		if(user) {
			return;
		}
		
		//from here, we are sure user is not logged in
		login = ent.encode(login);
		password = crypto.createHash('md5').update(password).digest('hex');
		
		//if login already connected, send error msg
		server.loginAlreadyConnected(login, function(user) {
			if(user) {
				socket.emit('alertMsg', 'This login is already connected.');
				return;
			}
			
			//from here, we are sure login is not already connected
			
			//try to find user data associated with this login and password
			server.dbmodels.User.findOne({login: login, password: password}, function(err, user) {
				if(err) throw err;
				
				//if no used was found, login or password are probably wrong, send error and return
				if(!user) {
					socket.emit('alertMsg', 'Wrong login or password!');
					return;
				}
			
				//else, update user session id and proceed
				user.sessionId = socket.id;
				user.isLoggedIn = true;
				user.lastOnlineTime = Date.now();
				user.save(function(err) {
					if(err) throw err;
					
					server.log("Client " + login + " has logged in (sessionId : " + socket.id + ")");
					
					//spawn user
					server.helpers.Map.userSelfSpawn(user, server, socket, function() {
						var d = new Date();
						new SERVER_MESSAGES.SM_SERVER_MESSAGE('', 'Welcome to Golden Eternity. Server time : ' + d.toLocaleDateString() + ' ' + d.toLocaleTimeString(), 'rgb(255,128,0)').emit(socket);
					});
					
				});
			});
		});
	});

};

exports.logoutUser = function(server, socket, callback) {
	//ignore if not logged in
	server.dbmodels.User.isLoggedIn(socket.id, function(user) {
		if(!user) {
			return;
		}
		
		//disconnect user
		user.disconnect(function() {
			server.log("Client " + user.login + " has disconnected. (SessionId : " + socket.id + ")");
			
			//remove him from map (players on the same map as him are noticed)
			server.helpers.Map.userExitsMap(user, user.playerData.mapData.map, server, socket, function() {
				if(callback) callback();
			});
		});
	});
}