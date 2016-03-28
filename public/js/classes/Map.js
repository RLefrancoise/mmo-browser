function Map(path, width, height) {
	
	var self = this;
	this.name = '';
	this.path = path;
	this.width = width;
	this.height = height;
	this.characters = new Array();
	this.player = undefined;
	this.ready = false;
	
	var req = $.getJSON("maps/" + path + ".json", function(data){
		self.name = data.name;
		self.tileset = new Tileset(data.tileset);
		self.terrain = data.terrain;
		self.ready = true;
	});
	
	req.fail = function(jqXHR, status) {
		throw new Error("Impossible de charger la carte nommée \"" + nom + "\" (code HTTP : " + status + ").");
	}
}

Map.prototype.isReady = function() {
	return this.ready;
}

// Pour récupérer la taille (en tiles) de la carte
Map.prototype.getHeight = function() {
	return this.terrain.length;
}
Map.prototype.getWidth = function() {
	return this.terrain[0].length;
}

Map.prototype.draw = function(context) {
	for(var i = 0, l = this.terrain.length ; i < l ; i++) {
		var ligne = this.terrain[i];
		var y = i * 32;
		for(var j = 0, k = ligne.length ; j < k ; j++) {
			this.tileset.dessinerTile(ligne[j], context, j * 32, y);
		}
	}
	
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
Map.prototype.addCharacter = function(c) {
	this.characters.push(c);
}

Map.prototype.removeCharacter = function(id) {
	for(var i = 0 ; i < this.characters.length ; i++) {
		if(this.characters[i].playerData.id == id) {
			this.characters.splice(i, 1);
			return true;
		}
	}
	
	return false;
}

Map.prototype.characterIsOnMap = function(id) {
	for(var i = 0 ; i < this.characters.length ; i++) {
		if(this.characters[i].playerData.id == id) return true;
	}
	
	return false;
}

Map.prototype.getCharacter = function(id) {
	for(var i = 0 ; i < this.characters.length ; i++) {
		if(this.characters[i].playerData.id == id) return this.characters[i];
	}
	
	return false;
}

Map.prototype.setPlayer = function(p) {
	this.player = p;
}