var Character = Class.create(MapEntity, {

	// constructor
	initialize: function($super, playerData) {

		//$super(playerData.id, playerData.mapData.charset, playerData.mapData.position.x, playerData.mapData.position.y, playerData.mapData.direction);
		$super(playerData.id, playerData.charset, playerData.position.x, playerData.position.y, playerData.position.direction);

		//player data
		this.playerData = playerData;
		this.displayData = true;
	},

	//draw
	draw: function($super, context, xScroll, yScroll) {

		$super(context, xScroll, yScroll);

		var posX = this.x * 32;
		var posY = this.y * 32;

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
});
