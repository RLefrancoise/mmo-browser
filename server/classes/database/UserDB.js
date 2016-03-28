var UserModel = require('../../models/UserModel');

function UserDB() {
}

UserDB.prototype.createUser = function(l, p) {
	var user = new UserModel({ login: l, password: p});
	
	user.save(function(err) {
		if(err) {
			throw err;
		}
		console.log('User "' + l + '" has been created.');
	});
}

exports.UserDB = UserDB;
exports.createUserDB = function() {
	return new UserDB();
}

exports.createUser = UserDB.createUser;