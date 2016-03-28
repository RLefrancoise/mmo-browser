var SERVER_MESSAGES = require('../messages/server_messages');

function userSelfSpawn(user, server, socket, callback) {

	//peer joins the room of the map he is in
	server.helpers.User.userJoinRoom(user, user.playerData.mapData.map, server, socket, function() {
		//send to peer its spawn data
		var mapData = server.serverData.Maps[user.playerData.mapData.map];
		new SERVER_MESSAGES.SM_SELF_SPAWN(user, mapData).emit(socket);
	
		//broadcast to others peers (on the same map) the user position etc...
		new SERVER_MESSAGES.SM_SPAWN_PLAYER(user).broadcast(socket, user.playerData.mapData.map);
		
		//find all the players on the same map as the user and send him their data
		server.dbmodels.User.find({ 'playerData.mapData.map': user.playerData.mapData.map, isLoggedIn: true }, function(err, users) {
			if(err) throw err;
			
			for(var i = 0 ; i < users.length ; i++) {
				if(users[i].login == user.login) continue;
				
				new SERVER_MESSAGES.SM_SPAWN_PLAYER(users[i]).emit(socket);
			}
		});
		
		if(callback) callback();
	});
}

exports.userSelfSpawn = userSelfSpawn;

exports.warpUserToMap = function(user, mapName, x, y, direction, server, socket, callback) {

	if(!user || !mapName || (x === undefined) || (y === undefined) || (direction === undefined) || !server || !socket) {
		throw new Error("[" + __dirname + "/MapHelper.js] warpUserToMap: invalid parameter");
	}
	
	//peer first exits the map he was in
	userExitsMap(user, user.playerData.mapData.map, server, socket, function() {
	
		//warp user to the new map
		user.playerData.mapData.map = mapName;
		user.playerData.mapData.position.x = x;
		user.playerData.mapData.position.y = y;
		user.playerData.mapData.direction = direction;
		
		user.save(function(err) {
			if(err) throw err;
			
			userSelfSpawn(user, server, socket, callback);
		})
	});
};

function userExitsMap(user, mapName, server, socket, callback) {
	if(!user || !mapName || !server || !socket) {
		throw new Error("[" + __dirname + "/MapHelper.js] userExitsMap: invalid parameter");
	}
	
	//first, inform others players on the map that the user leaves it
	new SERVER_MESSAGES.SM_PLAYER_EXITS_MAP(user).broadcast(socket, user.playerData.mapData.map);
	
	//peer leave the room of the map he was in
	server.helpers.User.userLeaveRoom(user, user.playerData.mapData.map, server, socket, function() {
		callback();
	});
}

exports.userExitsMap = userExitsMap;