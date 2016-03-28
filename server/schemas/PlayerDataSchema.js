var mongoose = require('mongoose');
var Schema = mongoose.Schema;

module.exports = function() {

	var PlayerDataSchema = new Schema({
		/* === Map Data === */
		
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
				type: String,
				default: 'down',
				trim: true
			},
		
		/* Stat Data */
		hp: { type: Number, default: 1 },
		max_hp: { type: Number, default: 1 },
		fp: { type: Number, default: 1 },
		max_fp: { type: Number, default: 1 }
	});

	PlayerDataSchema.statics.createData = function(callback) {
		var PlayerDataModel = this.db.model('PlayerData');
		
		var data = new PlayerDataModel();
		data.save(function(err) {
			if(err) throw err;
			
			callback(data);
		});
	}
	
	mongoose.model('PlayerData', PlayerDataSchema);
}