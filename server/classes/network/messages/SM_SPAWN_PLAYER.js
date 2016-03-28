function SM_SPAWN_PLAYER(user) {
	this.user = user;
}

SM_SPAWN_PLAYER.prototype.toObject = function() {
	var obj = {
		id: this.user.sessionId,
		name: this.user.login,
		mapData: this.user.playerData.mapData,
		hp: this.user.playerData.statsData.hp,
		max_hp: this.user.playerData.statsData.max_hp,
		fp: this.user.playerData.statsData.fp,
		max_fp: this.user.playerData.statsData.max_fp,
		admin: (this.user.authorityLevel >= 5)
	};
	
	return obj;
}

SM_SPAWN_PLAYER.prototype.emit = function(socket) {
	socket.emit('spawn_player', this.toObject());
}

SM_SPAWN_PLAYER.prototype.broadcast = function(socket, room) {
	if(room) {
		socket.broadcast.to(room).emit('spawn_player', this.toObject());
	} else {
		socket.broadcast.emit('spawn_player', this.toObject());
	}
}

module.exports = SM_SPAWN_PLAYER;
exports.toObject = SM_SPAWN_PLAYER.toObject;
exports.emit = SM_SPAWN_PLAYER.emit;
exports.broadcast = SM_SPAWN_PLAYER.broadcast;
