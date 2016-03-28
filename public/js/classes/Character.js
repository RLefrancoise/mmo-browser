var DIRECTION = {
	"DOWN"    : 0,
	"LEFT" : 1,
	"RIGHT" : 2,
	"UP"   : 3
}

var ANIMATION_TIME = 4;
var MOVE_TIME = 15;

function Character(playerData) {

	//player data
	this.playerData = playerData;
	this.displayData = true;

	//map data
	this.x = playerData.mapData.position.x; // (en cases)
	this.y = playerData.mapData.position.y; // (en cases)
	this.xOffset = 0;
	this.yOffset = 0;
	this.nextX = this.x;
	this.nextY = this.y;

	this.direction = playerData.mapData.direction;
	this.animationState = -1;

	// Chargement de l'image dans l'attribut image
	this.image = new Image();
	this.image.referenceDuPerso = this;
	this.image.onload = function() {
		if(!this.complete)
			throw "Erreur de chargement du sprite nommé \"" + playerData.mapData.charset + "\".";

		// Taille du personnage
		this.referenceDuPerso.largeur = this.width / 4;
		this.referenceDuPerso.hauteur = this.height / 4;
	}
	this.image.src = "sprites/" + playerData.mapData.charset;
}

Character.prototype.setPosition = function(x, y) {
	this.nextX = x;
	this.nextY = y;
	this.x = x;
	this.y = y;
	this.xOffset = 0;
	this.yOffset = 0;
	this.animationState = -1;
}

Character.prototype.getPixelPosition = function() {

	var x = (this.x * 32) + this.xOffset;
	var y = (this.y * 32) + this.yOffset;

	return {
		x: x,
		y: y
	}

}

Character.prototype.getPositionTile = function() {
	return { x: this.nextX, y: this.nextY }
}

Character.prototype.draw = function(context, xScroll, yScroll) {
	var frame = 0; // Numéro de l'image à prendre pour l'animation
	this.xOffset = 0, this.yOffset = 0;

	//var decalageX = 0, decalageY = 0; // Décalage à appliquer à la position du personnage
	if(this.animationState >= MOVE_TIME) {
		// Si le déplacement a atteint ou dépassé le temps nécessaire pour s'effectuer, on le termine
		this.animationState = -1;
		this.x = this.nextX;
		this.y = this.nextY;
	} else if(this.animationState >= 0) {
		// On calcule l'image (frame) de l'animation à afficher
		frame = Math.floor(this.animationState / ANIMATION_TIME);
		if(frame > 3) {
			frame %= 4;
		}

		// Nombre de pixels restant à parcourir entre les deux cases
		var pixelsAParcourir = /*32 -*/ (32 * (this.animationState / MOVE_TIME));

		// À partir de ce nombre, on définit le décalage en x et y.
		// NOTE : Si vous connaissez une manière plus élégante que ces quatre conditions, je suis preneur
		if(this.direction == DIRECTION.UP) {
			this.yOffset = -pixelsAParcourir;
		} else if(this.direction == DIRECTION.DOWN) {
			this.yOffset = pixelsAParcourir;
		} else if(this.direction == DIRECTION.LEFT) {
			this.xOffset = -pixelsAParcourir;
		} else if(this.direction == DIRECTION.RIGHT) {
			this.xOffset = pixelsAParcourir;
		}

		this.animationState++;
	}

	/*
	 * Si aucune des deux conditions n'est vraie, c'est qu'on est immobile,
	 * donc il nous suffit de garder les valeurs 0 pour les variables
	 * frame, xOffset et decalageY
	 */

	var posX = this.x * 32 /*(this.animationState == -1) ? this.x * 32 : this.getPixelPosition().x*/;
	var posY = this.y * 32 /*(this.animationState == -1) ? this.y * 32 : this.getPixelPosition().y*/;

	context.drawImage(
		this.image,
		this.largeur * frame, this.direction * this.hauteur, // Point d'origine du rectangle source à prendre dans notre image
		this.largeur, this.hauteur, // Taille du rectangle source (c'est la taille du personnage)
		posX - (this.largeur / 2) + 16 + this.xOffset - xScroll, posY - this.hauteur + 24 + this.yOffset - yScroll, // Point de destination (dépend de la taille du personnage)
		this.largeur, this.hauteur // Taille du rectangle destination (c'est la taille du personnage)
	);

	//draw account data (name, hp, fp, ...)
	if(this.displayData)
	{
		//draw name
		context.font = 'normal 10pt Arial';
		context.fillStyle = "#ffffff";
		context.textAlign = 'center';
		var text = (this.playerData.admin ? "[GM] " : "") + this.playerData.name;
		var textWidth = context.measureText(text).width;
		context.fillText(text, posX - (this.largeur / 2) + 32 + this.xOffset - xScroll, posY + (this.hauteur / 2) + this.yOffset - yScroll);

		//hp
		context.fillStyle = "#ff0000";
		context.fillRect(posX - (this.largeur / 2) + 16 + this.xOffset - xScroll, posY - (this.hauteur / 2) + this.yOffset - yScroll - 5, 32, 3);
		context.fillStyle = "#0be110";
		context.fillRect(posX - (this.largeur / 2) + 16 + this.xOffset - xScroll, posY - (this.hauteur / 2) + this.yOffset - yScroll - 5, 32 * this.playerData.hp / this.playerData.max_hp, 3);

		//fp
		context.fillStyle = "#0054ff";
		context.fillRect(posX - (this.largeur / 2) + 16 + this.xOffset - xScroll, posY - (this.hauteur / 2) + 5 + this.yOffset - yScroll - 5, 32, 3);
		context.fillStyle = "#00c6ff";
		context.fillRect(posX - (this.largeur / 2) + 16 + this.xOffset - xScroll, posY - (this.hauteur / 2) + 5 + this.yOffset - yScroll - 5, 32 * this.playerData.fp / this.playerData.max_fp, 3);

	}

}

Character.prototype.getCoordonneesAdjacentes = function(direction)  {
	var coord = {'x' : this.x, 'y' : this.y};
	switch(direction) {
		case DIRECTION.DOWN :
			coord.y++;
			break;
		case DIRECTION.LEFT :
			coord.x--;
			break;
		case DIRECTION.RIGHT :
			coord.x++;
			break;
		case DIRECTION.UP :
			coord.y--;
			break;
	}
	return coord;
}

Character.prototype.deplacer = function(direction, map) {
	// On ne peut pas se déplacer si un mouvement est déjà en cours !
	if(this.animationState >= 0) {
		return false;
	}

	// On change la direction du personnage
	this.direction = direction;

	// On vérifie que la case demandée est bien située dans la carte
	var prochaineCase = this.getCoordonneesAdjacentes(direction);

	if(prochaineCase.x < 0 || prochaineCase.y < 0 || prochaineCase.x >= map.getWidth() || prochaineCase.y >= map.getHeight() || !map.isWalkable(prochaineCase.x, prochaineCase.y)) {
		// On retourne un booléen indiquant que le déplacement ne s'est pas fait,
		// Ça ne coute pas cher et ca peut toujours servir
		return false;
	}

	// On effectue le déplacement
	this.animationState = 1;
	this.nextX = prochaineCase.x;
	this.nextY = prochaineCase.y;

	//map.move(direction);

	return true;
}
