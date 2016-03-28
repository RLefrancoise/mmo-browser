function Tileset2(data) {
	var self = this;
	
	this.name = data.name;
	this.tileWidth = data.tilewidth;
	this.tileHeight = data.tileheight;
	this.tileproperties = data.tileproperties;
	this.firstgid = data.firstgid;
	
	this.image = new Image();
	this.image.onload = function() {
		if(!this.complete) 
			throw new Error("Erreur de chargement du tileset nommé \"" + data.image + "\".");
			
		// Largeur du tileset en tiles
		self.width = this.width / 32;
	}
	this.image.src = "tilesets/" + data.image;
}

Tileset2.prototype.getNumberOfTiles = function() {
	return (this.image.width / this.tileWidth) * (this.image.height / this.tileHeight);
}

Tileset2.prototype.getTileProperties = function(tile) {
	tile = (tile - this.firstgid);
	
	if(this.tileproperties === undefined) return {}
	
	if(tile in this.tileproperties) {
		return this.tileproperties[tile];
	} else {
		return {};
	}
}

// Méthode de dessin du tile numéro "numero" dans le contexte 2D "context" aux coordonnées x et y
Tileset2.prototype.dessinerTile = function(numero, context, xDestination, yDestination) {
	numero = (numero - this.firstgid + 1);
	var xSourceEnTiles = numero % this.width;
	if(xSourceEnTiles == 0) xSourceEnTiles = this.width;
	var ySourceEnTiles = Math.ceil(numero / this.width);
	
	var xSource = (xSourceEnTiles - 1) * 32;
	var ySource = (ySourceEnTiles - 1) * 32;
	
	context.drawImage(this.image, xSource, ySource, this.tileWidth, this.tileHeight, xDestination, yDestination, this.tileWidth, this.tileHeight);
}

function Layer(data) {
	this.name = data.name;
	this.type = data.type;
	
	switch(this.type) {
		case 'tilelayer':
			this.data = data.data;
			break;
		case 'imagelayer':
		{
			this.image = new Image();
			this.image.onload = function() {
				if(!this.complete)
					throw new Error("Erreur de chargement du panorama " + data.image);
			};
			this.image.src = "panoramas/" + data.image;
		}
		break;
	}
	
	this.width = data.width;
	this.height = data.height;
	this.visible = data.visible;
	this.x = data.x;
	this.y = data.y;
}

function Map2(path, windowWidth, windowHeight) {
	var self = this;
	
	this.debug = false;
	this.windowWidth = windowWidth;
	this.windowHeight = windowHeight;
	this.characters = new Array();
	this.player = undefined;
	this.tilesets = [];
	this.layers = [];
	
	var req = $.getJSON("maps/" + path + ".json", function(data){
		
		//console.dir(data);
		
		self.properties = data.properties;
		self.width = data.width;
		self.height = data.height;
		self.tileWidth = data.tilewidth;
		self.tileHeight = data.tileheight;
		
		for(var i = 0; i < data.tilesets.length ; i++) {
			self.tilesets.push(new Tileset2(data.tilesets[i]));
		}
		
		for(var i = 0 ; i < data.layers.length ; i++) {
			self.layers.push(new Layer(data.layers[i]));
		}
		
		self.layers.sort(function(a, b) {
			if(parseInt(a.name) < parseInt(b.name)) return -1;
			if(parseInt(a.name) > parseInt(b.name)) return 1;
			else return 0;
		});
		
		console.dir(self.layers);
		
		self.ready = true;
	});
	
	req.fail = function(jqXHR, status) {
		throw new Error("Impossible de charger la carte nommée \"" + nom + "\" (code HTTP : " + status + ").");
	}
}

Map2.prototype.isReady = function() {
	return this.ready;
}

Map2.prototype.getName = function() {
	return this.properties.name;
}

Map2.prototype.isWalkable = function(x, y) {
	var walkable = false;
	
	for(var l = 0 ; l < this.layers.length ; l++) {
		var layer = this.layers[l];
		
		if(!layer.visible) continue;
		
		switch(layer.type) {
			case 'tilelayer':
			{
				var tiles = layer.data;
				
				var tile = tiles[y * this.getWidth() + x];
				if(tile == 0) continue;
				
				var tilesetData = this.getTilesetOfTile(tile);
				if(!tilesetData) continue;
				
				var tilesetIndex = tilesetData.index;
				//var totalTiles = (tilesetIndex > 0 ? this.tilesets[tilesetIndex].firstgid + 1 : 1);
				
				if(tilesetData.tileset.getTileProperties(tile /*- totalTiles*/).passable === undefined || tilesetData.tileset.getTileProperties(tile /*- totalTiles*/).passable != "0") {
					walkable = true;
				} else {
					walkable = false;
				}
			}
			break;
		}
	}
	
	return walkable;
}

// Pour récupérer la taille (en tiles) de la carte
Map2.prototype.getHeight = function() {
	return this.height;
}

Map2.prototype.getWidth = function() {
	return this.width;
}

Map2.prototype.getTilesetOfTile = function(tile) {
	for(var ts = this.tilesets.length - 1; ts >= 0 ; ts--) {
		if(tile >= this.tilesets[ts].firstgid) {
			return { tileset: this.tilesets[ts], index: ts }
		}
	}
	
	return false;
}

Map2.prototype.draw = function(context) {
	if(!this.isReady()) return;
	
	for(var l = 0 ; l < this.layers.length ; l++) {
		var layer = this.layers[l];
		
		if(!layer.visible) continue;
		
		switch(layer.type) {
			case 'imagelayer':
			{
				//context.drawImage(layer.image, layer.x, layer.y);
				var pat = context.createPattern(layer.image, "repeat");
				context.rect(layer.x, layer.y, layer.width * this.tileWidth, layer.height * this.tileHeight);
				context.fillStyle = pat;
				context.fill();
			}
			break;
			
			case 'tilelayer':
			{
				var tiles = layer.data;
				
				for(var t = 0 ; t < tiles.length ; t++) {
					if(tiles[t] == 0) continue;
					
					var tile = tiles[t];
					
					var tileset = this.tilesets[0];
					
					//find which tileset to use according to tile number
					tileset = this.getTilesetOfTile(tile).tileset;
					
					var x = (t% this.getWidth());
					//var y = (Math.ceil((t + 1) / this.getWidth()) - 1) * this.tileHeight;
					var y = ((t - x) / this.getWidth());
					tileset.dessinerTile(tile, context, x * this.tileWidth, y * this.tileHeight);
				}
			}
			break;
		}
	}
	
	if(this.debug) {
		for(var x = 0 ; x < this.getWidth() ; x++) {
			for(var y = 0 ; y < this.getHeight() ; y++) {
				//debug, draw red tile if not walkable
				if(!this.isWalkable(x, y)) {
					context.fillStyle = "#FF0000";
					context.globalAlpha = 0.5;
					context.fillRect(x * this.tileWidth, y * this.tileHeight, this.tileWidth, this.tileHeight);
					context.globalAlpha = 1;
				}
			}
		}
	}
	
	/*for(var i = 0, l = this.terrain.length ; i < l ; i++) {
		var ligne = this.terrain[i];
		var y = i * 32;
		for(var j = 0, k = ligne.length ; j < k ; j++) {
			this.tileset.dessinerTile(ligne[j], context, j * 32, y);
		}
	}*/
	
	var self = this;
	
	// Dessin des personnages
	this.characters.sort(function(a,b) {
		if(a.y > b.y) return 1;
		if(a.y < b.y) return -1;
		if(a.y == b.y) {
			//make sure player is always on top
			if(a != self.player && b != self.player)
				return 0;
			if(a == self.player)
				return 1;
			if(b == self.player)
				return -1;
		}
	});
	
	for(var i = 0, l = this.characters.length ; i < l ; i++) {
		//if(this.characters[i] == this.player) continue;
		
		this.characters[i].draw(context);
	}
	
	// player always on top
	//if(this.player != undefined)
	//	this.player.draw(context);
}

// Pour ajouter un personnage
Map2.prototype.addCharacter = function(c) {
	this.characters.push(c);
}

Map2.prototype.removeCharacter = function(id) {
	for(var i = 0 ; i < this.characters.length ; i++) {
		if(this.characters[i].playerData.id == id) {
			this.characters.splice(i, 1);
			return true;
		}
	}
	
	return false;
}

Map2.prototype.characterIsOnMap = function(id) {
	for(var i = 0 ; i < this.characters.length ; i++) {
		if(this.characters[i].playerData.id == id) return true;
	}
	
	return false;
}

Map2.prototype.getCharacter = function(id) {
	for(var i = 0 ; i < this.characters.length ; i++) {
		if(this.characters[i].playerData.id == id) return this.characters[i];
	}
	
	return false;
}

Map2.prototype.setPlayer = function(p) {
	this.player = p;
}