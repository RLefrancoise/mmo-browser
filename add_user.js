// Inclusion de Mongoose
var mongoose = require('mongoose');
var fs = require('fs');

// On se connecte à la base de données
// N'oubliez pas de lancer ~/mongodb/bin/mongod dans un terminal !
mongoose.connect('mongodb://localhost/GSL', function(err) {
  if (err) { throw err; }
});
 
require('./server/schemas/UserSchema')();

var User = mongoose.model('User');

fs.readFile(__dirname + '/' + process.argv[2], 'utf8', function(err, data) {
	if(err) throw err;
	
	data = JSON.parse(data);
	
	console.dir(data);
	
	var user = new User(data);

	user.save(function(err) {
		if(err) throw err;
		
		console.log('User created');
		done();
	});
});

function done() {
	mongoose.connection.close();
}