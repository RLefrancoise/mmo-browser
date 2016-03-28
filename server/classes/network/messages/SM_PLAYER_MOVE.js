function SM_PLAYER_MOVE(user) {
	this.user = user;
}

SM_PLAYER_MOVE.prototype.toObject = function() {
	var obj = {
		id: this.user.sessionId,
		x: this.user.playerData.mapData.position.x,
		y: this.user.playerData.mapData.position.y,
		direction: this.user.playerData.mapData.direction
	};
	
	return obj;
}

SM_PLAYER_MOVE.prototype.emit = function(socket) {
	socket.emit('player_move', this.toObject());
}

SM_PLAYER_MOVE.prototype.broadcast = function(socket, room) {
	if(room) {
		socket.broadcast.to(room).emit('player_move', this.toObject());
	} else {
		socket.broadcast.emit('player_move', this.toObject());
	}
}

module.exports = SM_PLAYER_MOVE;
exports.toObject = SM_PLAYER_MOVE.toObject;
exports.emit = SM_PLAYER_MOVE.emit;
exports.broadcast = SM_PLAYER_MOVE.broadcast;
