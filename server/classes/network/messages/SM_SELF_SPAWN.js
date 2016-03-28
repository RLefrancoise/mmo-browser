function SM_SELF_SPAWN(user, mapData) {
	this.user = user;
	this.mapData = mapData;
}

SM_SELF_SPAWN.prototype.toObject = function() {
	var obj = {
		playerData: {
			id: this.user.sessionId,
			name: this.user.login,
			mapData: this.user.playerData.mapData,
			hp: this.user.playerData.statsData.hp,
			max_hp: this.user.playerData.statsData.max_hp,
			fp: this.user.playerData.statsData.fp,
			max_fp: this.user.playerData.statsData.max_fp,
			admin: (this.user.authorityLevel >= 5)
		},
		mapData: this.mapData.rawData	
	};
	
	return obj;
}

SM_SELF_SPAWN.prototype.emit = function(socket) {
	socket.emit('self_spawn', this.toObject());
}

module.exports = SM_SELF_SPAWN;
exports.toObject = SM_SELF_SPAWN.toObject;
exports.emit = SM_SELF_SPAWN.emit;