// Inclusion de Mongoose
var mongoose = require('mongoose');
var crypto = require("crypto");

// On se connecte à la base de données
// N'oubliez pas de lancer ~/mongodb/bin/mongod dans un terminal !
mongoose.connect('mongodb://localhost/GSL', function(err) {
  if (err) { throw err; }
});
 
require('./server/schemas/UserSchema')();
//require('./server/schemas/PlayerDataSchema')();

var User = mongoose.model('User');

User.remove({}, function(err) {
	if(err) throw err;
	
	console.log('All users removed.');
	done();
});

function done() {
	mongoose.connection.close();
}