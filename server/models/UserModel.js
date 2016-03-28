var mongoose = require('mongoose');
var userSchema = require('../schemas/UserSchema');

var UserModel = mongoose.model('user', userSchema);

exports.UserModel = UserModel;