function SM_SERVER_MESSAGE(userName, msg, color) {
	this.userName = userName;
	this.msg = msg;
	this.color = color;
}

SM_SERVER_MESSAGE.prototype.toObject = function() {
	var obj = {
		userName: this.userName,
		msg: this.msg,
		color: this.color
	};
	
	return obj;
}

SM_SERVER_MESSAGE.prototype.emit = function(socket) {
	socket.emit('server_msg', this.toObject());
}

SM_SERVER_MESSAGE.prototype.broadcast = function(socket, room) {
	if(room) {
		socket.broadcast.to(room).emit('server_msg', this.toObject());
	} else {
		socket.broadcast.emit('server_msg', this.toObject());
	}
}

module.exports = SM_SERVER_MESSAGE;
exports.toObject = SM_SERVER_MESSAGE.toObject;
exports.emit = SM_SERVER_MESSAGE.emit;
exports.broadcast = SM_SERVER_MESSAGE.broadcast;
