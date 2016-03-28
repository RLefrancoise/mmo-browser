var Monster = Class.create(MapEntity, {

	initialize: function($super, data) {
		
		$super(data.id, data.mapData.charset, data.mapData.position.x, data.mapData.position.y, data.mapData.direction);
		
		this.data = data;
		
		console.dir(this);
	},
	
	//draw
	draw: function($super, context, xScroll, yScroll) {
	
		$super(context, xScroll, yScroll);
		
		var posX = this.x * 32;
		var posY = this.y * 32;
		
		//draw name
		context.font = 'normal 10pt Arial';
		context.fillStyle = "#ffffff";
		context.textAlign = 'center';
		var text = 'Nv' + this.data.level + ' ' + this.data.name;
		var textWidth = context.measureText(text).width;
		context.fillText(text, posX + 16 + this.xOffset - xScroll, posY + 27 + this.yOffset - yScroll);
		
		//hp
		context.fillStyle = "#ff0000";
		context.fillRect(posX + this.xOffset - xScroll, posY - this.hauteur + 32 + this.yOffset - yScroll - 5, 32, 3);
		context.fillStyle = "#0be110";
		context.fillRect(posX + this.xOffset - xScroll, posY - this.hauteur + 32 + this.yOffset - yScroll - 5, 32 * this.data.hp / this.data.max_hp, 3);

		//fp
		if(this.data.max_fp > 0) {
			context.fillStyle = "#0054ff";
			context.fillRect(posX + this.xOffset - xScroll, posY - this.hauteur + 32 + 5 + this.yOffset - yScroll - 5, 32, 3);
			context.fillStyle = "#00c6ff";
			context.fillRect(posX + this.xOffset - xScroll, posY - this.hauteur + 32 + 5 + this.yOffset - yScroll - 5, 32 * this.data.fp / this.data.max_fp, 3);
		}
	}
	
	
});