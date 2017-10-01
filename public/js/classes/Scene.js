var HUD = Class.create({
    initialize: function(scene) {
        this.scene = scene;
        this.image = new Image();
        this.image.src = "public/sprites/yuki.png";
    },

    _drawPlayerInfo: function(ctx) {
        //draw face
        ctx.beginPath();
        ctx.rect(5, 5, this.image.width / 4, this.image.height / 16);
        ctx.fillStyle = '#000000';
        ctx.fill();
        ctx.strokeStyle = "#888888";
        ctx.lineWidth = 1;
        ctx.stroke();

        ctx.drawImage(this.image, 0, 0, this.image.width / 4, this.image.height / 16, 5 , 5, this.image.width / 4, this.image.height / 16);

        //draw life bar
        var barWidth = 50, barHeight = this.image.height / 32;
        var grd = ctx.createLinearGradient(5 + this.image.width / 4 + 5, 5, 5 + this.image.width / 4 + 5 + barWidth, 5);


        grd.addColorStop(0, '#880000');
        grd.addColorStop(1, '#FF0000');
        ctx.fillStyle = grd;
        ctx.fillRect(5 + this.image.width / 4 + 5, 5, barWidth, barHeight);

        ctx.beginPath();
        ctx.rect(5 + this.image.width / 4 + 5, 5, barWidth / 2, barHeight);
        grd.addColorStop(0, '#008800');
        grd.addColorStop(1, '#00FF00');
        ctx.fillStyle = grd;
        ctx.fill();

        ctx.beginPath();
        ctx.rect(5 + this.image.width / 4 + 5, 5 + barHeight, barWidth, barHeight);
        ctx.strokeStyle = 'black';
        ctx.lineWidth = 1;
        ctx.stroke();

        //draw mana bar
        var barWidth = 50, barHeight = this.image.height / 32;
        var grd = ctx.createLinearGradient(5 + this.image.width / 4 + 5, 5 + barHeight, 5 + this.image.width / 4 + 5 + barWidth, 5 + barHeight);

        ctx.beginPath();
        grd.addColorStop(0, '#880000');
        grd.addColorStop(1, '#FF0000');
        ctx.fillStyle = grd;
        ctx.fillRect(5 + this.image.width / 4 + 5, 5 + barHeight, barWidth, barHeight);


        ctx.beginPath();
        ctx.rect(5 + this.image.width / 4 + 5, 5 + barHeight, barWidth / 3, barHeight);
        grd.addColorStop(0, '#000088');
        grd.addColorStop(1, '#0000FF');
        ctx.fillStyle = grd;
        ctx.fill();

        ctx.rect(5 + this.image.width / 4 + 5, 5 + barHeight, barWidth, barHeight);
        ctx.strokeStyle = 'black';
        ctx.lineWidth = 1;
        ctx.stroke();
    },
    draw: function(ctx) {
        this._drawPlayerInfo(ctx);
    }
});

var Scene = Class.create({
    initialize: function() {
        var self = this;

        this.map = null;
        this.player = null;
        this.canvas = null;
        this.hud = new HUD(this);

        this.canUpdate = true;
        this.playerIsSpawned = false;

        this.canvas = document.getElementById('canvas');
    	this.canvas.receiveInput = true; // custom value to handle input or not
        this.canvas.tabIndex = 1000; //to use key events

    	var ctx = this.canvas.getContext('2d');
    	ctx.font = 'normal 10pt Arial';

    	// Gestion du clavier
    	this.canvas.addEventListener('keyup', function(event) { Key.onKeyup(event); }, false);
    	this.canvas.addEventListener('keydown', function(event) { Key.onKeydown(event); }, false);

    	//rendering loop (25 fps)
    	setInterval(function() {
    		self.update();

    		ctx.clearRect(0,0, this.canvas.width, this.canvas.height);



    		if(self.map && self.map.isReady()) {
    			self.map.draw(ctx);

    			ctx.font = 'bold 14pt Arial';
    			ctx.fillStyle = '#ffffff';
    			ctx.textAlign = 'center';
    			ctx.fillText(self.map.getName(), self.canvas.width - ctx.measureText(self.map.getName()).width - 5, 25);

    			if(self.map.debug) {
    				ctx.textAlign = 'left';
    				//player position
    				if(self.player) {
    					ctx.fillText('Player TX : ' + self.player.getPositionTile().x + ' TY : ' + self.player.getPositionTile().y + ' X: ' + self.player.getPixelPosition().x + ' Y: ' + self.player.getPixelPosition().y, 5, 45);
    					ctx.fillText('xOffset : ' + self.player.xOffset + ' yOffset : ' + self.player.yOffset, 5, 65);
    				}

    				//map scroll
    				ctx.fillText('xScroll : ' + self.map.xScroll + ' yScroll : ' + self.map.yScroll, 5, 25);
    			}
    		}

            self.hud.draw(ctx);
            //if(self.map) self.hud.draw(ctx);
    	}, 40);

        this.canvas.onkeydown = function(event) {
    		var e = event || window.event;
    		var key = e.which || e.keyCode;

    		switch(key) {
    			case Key.X:
    				if(self.canvas.receiveInput && self.player) self.player.attack();
    				break;
    			case Key.Space:
    				if(self.canvas.receiveInput && self.player) self.player.displayData = !self.player.displayData;
    				else return true;
    				break;
    			case Key.BackSpace:
    				if(self.canvas.receiveInput && self.map) self.map.debug = !self.map.debug;
    				else return true;
    				break;
    			default :
    				//alert(key);
    				// Si la touche ne nous sert pas, nous n'avons aucune raison de bloquer son comportement normal.
    				return true;
    		}

    		return false;
    	}
    },

    update: function() {
        var self = this;

    	if(!self.canvas.receiveInput) return;

    	//player realtime events
    	if(self.player && self.map && self.map.isReady())
    	{
            var move_player = false;

    		//player movements
    		if(Key.isDown(Key.Up) || Key.isDown(Key.Z)) {
    			if(self.player.deplacer(MapEntity.DIRECTION.UP, self.map))
                    move_player = true;
    		}
    		if(Key.isDown(Key.Down) || Key.isDown(Key.S)) {
    			if(self.player.deplacer(MapEntity.DIRECTION.DOWN, self.map))
                    move_player = true;
    		}
    		if(Key.isDown(Key.Left) || Key.isDown(Key.Q)) {
    			if(self.player.deplacer(MapEntity.DIRECTION.LEFT, self.map))
                    move_player = true;
    		}
    		if(Key.isDown(Key.Right) || Key.isDown(Key.D)) {
    			if(self.player.deplacer(MapEntity.DIRECTION.RIGHT, self.map))
                    move_player = true;
    		}

            if(move_player === true) {
                (new CM_MAP_PLAYER_MOVE({
                    x: self.player.getPositionTile().x,
                    y: self.player.getPositionTile().y,
                    direction: self.player.direction,
                }, socket)).send();
            }
    	}
    },

    destroy: function() {
        var self = this;

        self.map = null;
        self.player = null;
    },

    self_spawn: function(data) {
        var self = this;

    	self.playerIsSpawned = false;

    	//load map
    	self.map = new Map3(data.mapdata, self.canvas.width, self.canvas.height);

    	//spawn monster for test
    	/*var m = new Monster({
    		id: 'Monster01',
    		name: 'AquaDino',
    		level: 1,
    		hp: 50,
    		max_hp: 60,
    		fp: 0,
    		max_fp: 0,
    		mapData: {
    			charset: "monster2.png",
    			direction: MapEntity.DIRECTION.DOWN,
    			position: {
    				x: 8,
    				y: 5
    			}
    		}
    	});

    	self.map.addEntity(m);
    	self.map.addEntity(new Monster({
    		id: 'Monster02',
    		name: 'Lich',
    		level: 40,
    		hp: 3000,
    		max_hp: 4000,
    		fp: 1000,
    		max_fp: 1200,
    		mapData: {
    			charset: "undead4.png",
    			direction: MapEntity.DIRECTION.DOWN,
    			position: {
    				x: 6,
    				y: 3
    			}
    		}
    	}));*/


    	//spawn self
    	if(!self.player) self.player = self.spawn_player(data.playerdata);
    	else {
            self.player.setPosition(data.playerdata.position.x, data.playerdata.position.y);
            self.player.direction = data.playerdata.position.direction;

    		self.map.addEntity(self.player);
    	}

    	self.map.setPlayer(self.player);
    	self.playerIsSpawned = true;
    },

    spawn_player: function(data) {
        var self = this;

    	if(!self.map.entityIsOnMap(data.id)) {
    		var c = new Character(data);
    		self.map.addEntity(c);

    		return c;
    	}

    	return false;
    }
});
