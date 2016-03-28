var MapEntity = Class.create({

	initialize: function(id, charset, x, y, direction) {
		this.id = id;
		this.x = x; // (en cases)
		this.y = y; // (en cases)
		this.xOffset = 0;
		this.yOffset = 0;
		this.nextX = this.x;
		this.nextY = this.y;

		this.direction = direction;
		this.animationState = -1;

		// Chargement de l'image dans l'attribut image
		this.image = new Image();
		this.image.referenceDuPerso = this;
		this.image.onload = function() {
			if(!this.complete)
				throw "Erreur de chargement du sprite nommé \"" + charset + "\".";

			// Taille du personnage
			this.referenceDuPerso.largeur = this.width / 4;
			this.referenceDuPerso.hauteur = (this.height == 384) ? this.height / 8 : this.height / 4;
		}
		this.image.src = "public/sprites/" + charset;
	},

	//setPosition
	setPosition: function(x, y) {
		this.nextX = x;
		this.nextY = y;
		this.x = x;
		this.y = y;
		this.xOffset = 0;
		this.yOffset = 0;
		this.animationState = -1;
	},

	//getPixelPosition
	getPixelPosition: function() {
		var x = (this.x * 32) + this.xOffset;
		var y = (this.y * 32) + this.yOffset;

		return {
			x: x,
			y: y
		}
	},

	//getPositionTile
	getPositionTile: function() {
		return { x: this.nextX, y: this.nextY }
	},

	//draw
	draw: function(context, xScroll, yScroll) {

		var frame = 0; // Numéro de l'image à prendre pour l'animation
		this.xOffset = 0, this.yOffset = 0;

		//var decalageX = 0, decalageY = 0; // Décalage à appliquer à la position du personnage
		if(this.animationState >= MapEntity.MOVE_TIME) {
			// Si le déplacement a atteint ou dépassé le temps nécessaire pour s'effectuer, on le termine
			this.animationState = -1;
			this.x = this.nextX;
			this.y = this.nextY;

			//was attacking ?
			if(this.direction > MapEntity.DIRECTION.UP) {
				this.direction -= 4;
			}
		} else if(this.animationState >= 0) {
			// On calcule l'image (frame) de l'animation à afficher
			frame = Math.floor(this.animationState / MapEntity.ANIMATION_TIME);
			if(frame > 3) {
				frame %= 4;
			}

			// Nombre de pixels restant à parcourir entre les deux cases
			var pixelsAParcourir = (32 * (this.animationState / MapEntity.MOVE_TIME));

			// À partir de ce nombre, on définit le décalage en x et y.
			// NOTE : Si vous connaissez une manière plus élégante que ces quatre conditions, je suis preneur
			if(this.direction == MapEntity.DIRECTION.UP) {
				this.yOffset = -pixelsAParcourir;
			} else if(this.direction == MapEntity.DIRECTION.DOWN) {
				this.yOffset = pixelsAParcourir;
			} else if(this.direction == MapEntity.DIRECTION.LEFT) {
				this.xOffset = -pixelsAParcourir;
			} else if(this.direction == MapEntity.DIRECTION.RIGHT) {
				this.xOffset = pixelsAParcourir;
			}

			this.animationState++;
		}

		/*
		 * Si aucune des deux conditions n'est vraie, c'est qu'on est immobile,
		 * donc il nous suffit de garder les valeurs 0 pour les variables
		 * frame, xOffset et decalageY
		 */

		var posX = this.x * 32;
		var posY = this.y * 32;

		context.drawImage(
			this.image,
			this.largeur * frame, this.direction * this.hauteur, // Point d'origine du rectangle source à prendre dans notre image
			this.largeur, this.hauteur, // Taille du rectangle source (c'est la taille du personnage)
			posX - (this.largeur / 2) + 16 + this.xOffset - xScroll, posY - this.hauteur + 24 + this.yOffset - yScroll, // Point de destination (dépend de la taille du personnage)
			this.largeur, this.hauteur // Taille du rectangle destination (c'est la taille du personnage)
		);
	},

	//getCoordonneesAdjacentes
	getCoordonneesAdjacentes: function(direction) {
		var coord = {'x' : this.x, 'y' : this.y};
		switch(direction) {
			case MapEntity.DIRECTION.DOWN :
				coord.y++;
				break;
			case MapEntity.DIRECTION.LEFT :
				coord.x--;
				break;
			case MapEntity.DIRECTION.RIGHT :
				coord.x++;
				break;
			case MapEntity.DIRECTION.UP :
				coord.y--;
				break;
		}
		return coord;
	},

	attack : function() {
		if(this.animationState >= 0) {
			return false;
		}

		switch(this.direction) {
			case MapEntity.DIRECTION.DOWN:
				this.direction = MapEntity.DIRECTION.ATTACK_DOWN;
				break;
			case MapEntity.DIRECTION.LEFT:
				this.direction = MapEntity.DIRECTION.ATTACK_LEFT;
				break;
			case MapEntity.DIRECTION.RIGHT:
				this.direction = MapEntity.DIRECTION.ATTACK_RIGHT;
				break;
			case MapEntity.DIRECTION.UP:
				this.direction = MapEntity.DIRECTION.ATTACK_UP;
				break;
		}

		this.animationState = 1;

		return true;
	},

	//deplacer
	deplacer: function(direction, map) {
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

		return true;
	}
});

MapEntity.DIRECTION = {
	"DOWN"    : 0,
	"LEFT" : 1,
	"RIGHT" : 2,
	"UP"   : 3,
	"ATTACK_DOWN": 4,
	"ATTACK_LEFT": 5,
	"ATTACK_RIGHT": 6,
	"ATTACK_UP": 7
};

MapEntity.ANIMATION_TIME = 4;
MapEntity.MOVE_TIME = 7;
