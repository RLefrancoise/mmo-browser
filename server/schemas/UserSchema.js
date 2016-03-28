var mongoose = require('mongoose');
var Schema = mongoose.Schema;

var pData = {
	/* === Map Data === */
	mapData:
		{
			/* Map Charset */
			charset:
				{
					type: String,
					default: 'default.png',
					trim: true
				},
			/* Map the player is currently in */
			map:
				{
					type: String,
					trim: true
				},
			/* The position on the map */
			position:
				{
					x:
						{
							type: Number,
							default: 0
						},
					y:
						{
							type: Number,
							default: 0
						}
				},
			/* The direction the player is facing */
			direction:
				{
					type: Number,
					default: 0
				}
		},
	
	/* === Stat Data === */
	statsData:
		{
			hp: { type: Number, default: 1 },
			max_hp: { type: Number, default: 1 },
			fp: { type: Number, default: 1 },
			max_fp: { type: Number, default: 1 }
		}
	
}



module.exports = function() {

	var UserSchema = new Schema({
		/* Login (primary key) */
		login: 
			{
				type: String,
				unique: true,
				required: true,
				trim: true
			},
		/* Password (md5) */
		password:
			{
				type: String,
				required: true
			},
		/* Player data of this user */
		playerData: pData,
		/* Is user logged in ?*/
		isLoggedIn:
			{
				type: Boolean,
				default: false
			},
		/* Last time user sent something to the server (used for user timeout) */
		lastOnlineTime: Date,
		sessionId: String,
		/* Authority level of user (normal user, moderator, admin) */
		authorityLevel:
			{
				type: Number,
				default: 0
			}
	});
	
	//find
	/*UserSchema.methods.findPlayerData = function(callback) {
		return this.db.model('PlayerData').findById(this.playerData, callback);
	}*/

	/*UserSchema.methods.findById = function(id, callback) {
		return this.find({_id: id}, callback);
	}*/
	
	UserSchema.statics.findByLogin = function(login, callback) {
		return this.find({login: login}, function(err, users) {
			if(err) throw err;
			
			if(users.length == 0) {
				callback(false);
			} else {
				callback(users[0]);
			}
		});
	}

	//login
	UserSchema.statics.isLoggedIn = function(sessionId, callback) {
		return this.findOne({sessionId: sessionId, isLoggedIn: true}, function(err, user) {
			if(err) throw err;
			
			if(!user) callback(false);
			else callback(user);
		});
	}
	
	UserSchema.statics.isConnected = function(login, callback) {
		return this.findOne({login: login, isLoggedIn: true}, function(err, user) {
			if(err) throw err;
			
			if(!user) callback(false);
			else callback(user);
		});
	}
	
	//disconnect
	UserSchema.methods.disconnect = function(callback) {
		var UserModel = this.db.model('User');
		
		UserModel.findOneAndUpdate({login: this.login, isLoggedIn: true}, {isLoggedIn: false, lastOnlineTime: 0, sessionId: ''}, function(err) {
			if(err) throw err;
			
			callback();
		});
	}
	
	UserSchema.statics.disconnectAll = function(callback) {
		var UserModel = this.db.model('User');
		
		UserModel.update({isLoggedIn: true}, {isLoggedIn: false, lastOnlineTime: 0, sessionId: ''}, function(err) {
			if(err) throw err;
			
			callback();
		});
	}
	
	//create
	UserSchema.statics.createUser = function(data, callback) {	
		var UserModel = this.db.model('User');
		//var PlayerDataModel = this.db.model('PlayerData');
		
		//check that login is not already used
		UserModel.findByLogin(data.login, function(err, users) {
			if(err) throw err;
			
			//if login is already used, call callback with false as an argument to warn that user was not created because it already exists
			if(users.length != 0) {
				callback(users[0], false);
			}
			//else, we can create user
			else {
				var u = new UserModel(data);
				
				u.save(function(err) {
					if(err) throw err;
					
					console.log('User "' + data.login + '" created. (id: ' + u._id + ')');
					
					//call callback with created user as an argument
					callback(u, true);
				});
			}
		});
	}
	
	mongoose.model('User', UserSchema);
}