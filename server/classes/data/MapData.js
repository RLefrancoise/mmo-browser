var jf = require('jsonfile');
var fs = require('fs');

function MapEvent(mapRef, data) {
	this.mapRef = mapRef;
	this.type = data.type; //event type (warp, script, ...)
	this.trigger = data.trigger; //event trigger (keypress, player_contact, auto, ...)
	if(data.conditions) this.conditions = data.conditions; //event existence conditions (switch activated, ...)
	
	
	switch(this.type) {
		case this.EventType.WARP:
			this._initFromWarpType(data);
			break;
		case this.EventType.SCRIPT:
			this._initFromScriptType(data);
			break;
	}
}

MapEvent.prototype.execute = function(user, server, socket) {
	switch(this.type) {
		case this.EventType.WARP:
			server.helpers.Map.warpUserToMap(user, this.warpMap, this.warpX, this.warpY, (this.warpDirection !== undefined ? this.warpDirection : user.playerData.mapData.direction), server, socket);
			break;
		case this.EventType.SCRIPT:
			break;
	}
}

MapEvent.prototype.EventType = {
	"WARP" : "warp",
	"SCRIPT" : "script"
}

MapEvent.prototype.EventTrigger = {
	"KEYPRESS": "keypress",
	"PLAYER_CONTACT": "player_contact",
	"AUTO": "auto"
}

MapEvent.prototype._initFromWarpType = function(ev) {
	if(ev.direction) this.direction = ev.direction;
	if(ev.data) {
		this.warpMap = ev.data.map;
		this.warpX = ev.data.x;
		this.warpY = ev.data.y;
		this.warpDirection = ev.data.direction;
	}
}

MapEvent.prototype._initFromScriptType = function(ev) {

}

exports.MapEvent = MapEvent;

function MapEventTile(mapRef, evData) {
	this.mapName = mapRef.path;
	this.name = evData.name;
	this.eventPages = [];
	
	//load event data from specified file
	if(evData.properties.file) {
		var data = jf.readFileSync("server/data/maps/" + this.mapName + "/events/" + evData.properties.file + ".json");
		if(!data) {
			throw new Error("[" + __dirname + "/MapData.js] MapEventTile: can't load " + "server/data/maps/" + this.mapName + "/events/" + evData.properties.file + ".json");
		}
		
		var pages = data.events;
		
		for(var p = 0 ; p < pages.length ; p++) {
			var ev = pages[p];
			
			//check if required fields are specified
			if(!ev.type) continue;
			if(!ev.trigger) continue;
			
			this.eventPages.push(new MapEvent(mapRef, ev));
		}
	}
}


 


function TilesetData(data) {
	var self = this;
	
	this.name = data.name;
	this.tileWidth = data.tilewidth;
	this.tileHeight = data.tileheight;
	this.tileproperties = data.tileproperties;
	this.firstgid = data.firstgid;
}

function TileData(number, tileset, x, y) {
	
	this.tileset = tileset;
	this.number = (number - this.tileset.firstgid + 1);
	this.x = x;
	this.y = y;
}

TileData.prototype.getProperties = function() {
	
	if(this.tileset.tileproperties === undefined) return {}
	
	if( (this.number - 1) in this.tileset.tileproperties) {
		return this.tileset.tileproperties[this.number - 1];
	} else {
		return {};
	}
}



function MapData(path) {
	var self = this;
	
	this.path = path;
	this.debug = false;
	
	this.tilesets = [];
	this.grid = [];
	
	var data = jf.readFileSync("server/data/maps/" + path + "/" + path + ".json");
	this.rawData = data;
	
	self.properties = data.properties;
	self.width = data.width;
	self.height = data.height;
	self.tileWidth = data.tilewidth;
	self.tileHeight = data.tileheight;
	
	//tilesets
	for(var i = 0; i < data.tilesets.length ; i++) {
		self.tilesets.push(new TilesetData(data.tilesets[i]));
	}
	
	//build grid
	for(var y = 0 ; y < self.height ; y++) {
		self.grid.push([]);
		for(var x = 0 ; x < self.width ; x++) {
			self.grid[y].push([]);
		}
	}
	
	for(var t = 0 ; t < self.width * self.height ; t++) {
		//iterate through each tile layer
		for(var l = 0 ; l < data.layers.length ; l++) {
			var layer = data.layers[l];
			
			if(!layer.visible) continue;
			if(layer.type != "tilelayer") continue;
			
			var x = t % self.width;
			var y = (t - x) / self.width;
			
			self.grid[y][x].push( (layer.data[t] != 0) ? new TileData(layer.data[t], self.getTilesetOfTile(layer.data[t]).tileset, x, y) : null );
		}
	}

	//map events
	this.events = [];
	for(var y = 0 ; y < this.height ; y++) {
		this.events.push([]);
		/*for(var x = 0 ; x < this.width ; x++) {
			this.events[y].push([]);
		}*/
	}
	
	for(var l = 0 ; l < data.layers.length ; l++) {
		var layer = data.layers[l];
		
		if(!layer.visible) continue;
		if(layer.type != "objectgroup" || (layer.type == "objectgroup" && layer.name != "events")) continue;
		
		var events = layer.objects;
		
		for(var e = 0 ; e < events.length ; e++) {
			var ev = events[e];
		
			if(ev.type != "event") continue;
			if(!ev.visible) continue;
			
			//get tiles this event in on
			var startx = ev.x / this.tileWidth;
			var starty = ev.y / this.tileHeight;
			var endx = (ev.x + ev.width) / this.tileWidth - 1;
			var endy = (ev.y + ev.height) / this.tileHeight - 1;
			
			for(var x = startx ; x <= endx ; x++) {
				for(var y = starty ; y <= endy ; y++) {
					this.events[y][x] = new MapEventTile(this, ev);
				}
			}
		}
	}
}

MapData.prototype.getEvents = function(x, y) {
	if(this.events[y][x]) {
		return this.events[y][x].eventPages;
	}
	else return false;
}

MapData.prototype.getTilesetOfTile = function(tile) {
	for(var ts = this.tilesets.length - 1; ts >= 0 ; ts--) {
		if(tile >= this.tilesets[ts].firstgid) {
			return { tileset: this.tilesets[ts], index: ts }
		}
	}
	
	return false;
}

MapData.prototype.getName = function() {
	return this.properties.name;
}

MapData.prototype.isWalkable = function(x, y) {
	var walkable = true;
	
	for(var k = 0 ; k < this.grid[y][x].length ; k++) {
		var tile = this.grid[y][x][k];
		
		if(tile === null) continue;
		
		//if tile is not walkable
		if(tile.getProperties().passable && tile.getProperties().passable == "0") {
			return false;
		}
	}

	return walkable;
}

// Pour récupérer la taille (en tiles) de la carte
MapData.prototype.getHeight = function() {
	return this.height;
}

MapData.prototype.getWidth = function() {
	return this.width;
}

exports.MapData = MapData;

exports.loadMapsData = function() {
	var data = {};
	
	console.log("---------------------------------");
	console.log("Loading maps data...");
	
	//read maps dir
	var maps = fs.readdirSync('server/data/maps');
	
	maps.forEach( function(map) {
		if(fs.statSync('server/data/maps/' + map).isDirectory()) {
			data[map] = new MapData(map);
			console.log("\tMap " + map + " loaded.");
		}
	});
	
	console.log("Maps data loaded.");
	console.log("---------------------------------");
	
	return data;
}