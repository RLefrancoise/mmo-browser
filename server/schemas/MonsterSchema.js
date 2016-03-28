var mongoose = require('mongoose');
var Schema = mongoose.Schema;

module.exports = function() {

	var MonsterSchema = new Schema({
		/* Id (primary key) */
		id: 
			{
				type: Number,
				unique: true,
				required: true
			},
		name:
			{
				type: String,
				trim:true,
				required: true
			},
		level:
			{
				type: Number,
				default: 1
			},
		exp:
			{
				type: Number,
				default: 0
			},
		hp:
			{
				type: Number,
				default: 1
			},
		max_hp:
			{
				type: Number,
				default: 1
			},
		fp:
			{
				type: Number,
				default: 0
			},
		max_fp:
			{
				type: Number,
				default: 0
			}
	});
	
	mongoose.model('Monster', MonsterSchema);
}