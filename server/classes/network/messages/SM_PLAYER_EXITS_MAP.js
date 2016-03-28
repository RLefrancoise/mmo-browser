function SM_PLAYER_EXITS_MAP(user) {
	this.user = user;
}

SM_PLAYER_EXITS_MAP.prototype.toObject = function() {
	var obj = {
		id: this.user.sessionId
	};
	
	return obj;
}

SM_PLAYER_EXITS_MAP.prototype.emit = function(socket) {
	socket.emit('player_exits_map', this.toObject());
}

SM_PLAYER_EXITS_MAP.prototype.broadcast = function(socket, room) {
	if(room) {
		socket.broadcast.to(room).emit('player_exits_map', this.toObject());
	} else {
		socket.broadcast.emit('player_exits_map', this.toObject());
	}
}

module.exports = SM_PLAYER_EXITS_MAP;
exports.toObject = SM_PLAYER_EXITS_MAP.toObject;
exports.emit = SM_PLAYER_EXITS_MAP.emit;
exports.broadcast = SM_PLAYER_EXITS_MAP.broadcast;
