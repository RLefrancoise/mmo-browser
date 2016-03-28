function SM_PLAYER_DISCONNECT(user) {
	this.user = user;
}

SM_PLAYER_DISCONNECT.prototype.toObject = function() {
	var obj = {
		id: this.user.sessionId
	};
	
	return obj;
}

module.exports = SM_PLAYER_DISCONNECT;
exports.toObject = SM_PLAYER_DISCONNECT.toObject;