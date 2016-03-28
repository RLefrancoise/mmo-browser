var mongoose = require('mongoose');
var playerDataSchema = require('../schemas/PlayerDataSchema');

var PlayerDataModel = mongoose.model('playerData', playerDataSchema);

exports.PlayerDataModel = PlayerDataModel;