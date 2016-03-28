var canvas = undefined;

var isSpawned = false;
var map = undefined;
var player = undefined;

var canUpdate = true;



/*var map = new Map("map1");
var player = new Character("yuki.png", 7, 0, DIRECTION.DOWN, {
		name : "Yuki Kazuki",
		admin: true,
		hp : 20,
		max_hp : 50,
		fp : 30,
		max_fp : 50
	});
map.addCharacter(player);
map.setPlayer(player);

map.addCharacter(new Character("vincent.png", 7,7, DIRECTION.DOWN, {
		name : "Vincent Blackwood",
		hp : 45,
		max_hp : 50,
		fp : 10,
		max_fp : 50
	}));

map.addCharacter(new Character("tsukasa.png", 7,9, DIRECTION.DOWN, {
		name : "Tsukasa Watanabe",
		hp : 90,
		max_hp : 100,
		fp : 60,
		max_fp : 100
	}));*/


//window.onload = function() {
function init_scene() {

	canvas = document.getElementById('canvas');
	canvas.receiveInput = true; // custom value to handle input or not

	var ctx = canvas.getContext('2d');
	ctx.font = 'normal 10pt Arial';

	// Gestion du clavier
	window.addEventListener('keyup', function(event) { Key.onKeyup(event); }, false);
	window.addEventListener('keydown', function(event) { Key.onKeydown(event); }, false);

	//rendering loop (25 fps)
	setInterval(function() {
		update();

		ctx.clearRect(0,0, canvas.width, canvas.height);

		if(map && map.isReady()) {
			map.draw(ctx);

			ctx.font = 'bold 14pt Arial';
			ctx.fillStyle = '#ffffff';
			ctx.textAlign = 'center';
			ctx.fillText(map.getName(), canvas.width - ctx.measureText(map.getName()).width - 5, 25);

			if(map.debug) {
				ctx.textAlign = 'left';
				//player position
				if(player) {
					ctx.fillText('Player TX : ' + player.getPositionTile().x + ' TY : ' + player.getPositionTile().y + ' X: ' + player.getPixelPosition().x + ' Y: ' + player.getPixelPosition().y, 5, 45);
					ctx.fillText('xOffset : ' + player.xOffset + ' yOffset : ' + player.yOffset, 5, 65);
				}

				//map scroll
				ctx.fillText('xScroll : ' + map.xScroll + ' yScroll : ' + map.yScroll, 5, 25);


			}
		}
	}, 40);

	//update loop (25 times per sec)
	/*setInterval(function() {
		if(!canvas.receiveInput) return;

		//player realtime events
		if(player && map && map.isReady())
		{
			//player movements
			if(Key.isDown(Key.Up) || Key.isDown(Key.Z)) {
				if(player.deplacer(DIRECTION.UP, map))
					socket.emit('player_move', player.x, player.y, player.direction);
			}
			if(Key.isDown(Key.Down) || Key.isDown(Key.S)) {
				if(player.deplacer(DIRECTION.DOWN, map))
					socket.emit('player_move', player.x, player.y, player.direction);
			}
			if(Key.isDown(Key.Left) || Key.isDown(Key.Q)) {
				if(player.deplacer(DIRECTION.LEFT, map))
					socket.emit('player_move', player.x, player.y, player.direction);
			}
			if(Key.isDown(Key.Right) || Key.isDown(Key.D)) {
				if(player.deplacer(DIRECTION.RIGHT, map))
					socket.emit('player_move', player.x, player.y, player.direction);
			}
		}

	}, 40);*/

	window.onkeydown = function(event) {
		var e = event || window.event;
		var key = e.which || e.keyCode;

		switch(key) {
			case Key.X:
				if(canvas.receiveInput && player) player.attack();
				break;
			case Key.Space:
				if(canvas.receiveInput && player) player.displayData = !player.displayData;
				else return true;
				break;
			case Key.BackSpace:
				if(canvas.receiveInput && map) map.debug = !map.debug;
				else return true;
				break;
			default :
				//alert(key);
				// Si la touche ne nous sert pas, nous n'avons aucune raison de bloquer son comportement normal.
				return true;
		}

		return false;
	}
}

function update() {
	if(!canvas.receiveInput) return;

	//player realtime events
	if(player && map && map.isReady())
	{
        var move_player = false;

		//player movements
		if(Key.isDown(Key.Up) || Key.isDown(Key.Z)) {
			if(player.deplacer(MapEntity.DIRECTION.UP, map))
                move_player = true;
		}
		if(Key.isDown(Key.Down) || Key.isDown(Key.S)) {
			if(player.deplacer(MapEntity.DIRECTION.DOWN, map))
                move_player = true;
		}
		if(Key.isDown(Key.Left) || Key.isDown(Key.Q)) {
			if(player.deplacer(MapEntity.DIRECTION.LEFT, map))
                move_player = true;
		}
		if(Key.isDown(Key.Right) || Key.isDown(Key.D)) {
			if(player.deplacer(MapEntity.DIRECTION.RIGHT, map))
                move_player = true;
		}

        if(move_player === true) {
            (new CM_MAP_PLAYER_MOVE({
                x: player.getPositionTile().x,
                y: player.getPositionTile().y,
                direction: player.direction,
            }, socket)).send();
        }
	}
}

function self_spawn(data) {
	isSpawned = false;

	//load map
	map = new Map3(data.mapdata, canvas.width, canvas.height);
	//map = new Map3(data.mapData, canvas.width, canvas.height);

	//spawn monster for test
	var m = new Monster({
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

	map.addEntity(m);
	map.addEntity(new Monster({
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
	}));


	//spawn self
	if(!player) player = spawn_player(data.playerdata);
	else {
        player.setPosition(data.playerdata.position.x, data.playerdata.position.y);
        player.direction = data.playerdata.position.direction;
		/*player.setPosition(data.playerData.mapData.position.x, data.playerData.mapData.position.y);
		player.direction = data.playerData.mapData.direction;*/

		map.addEntity(player);
	}

	map.setPlayer(player);
	isSpawned = true;


}

function spawn_player(data) {
	if(!map.entityIsOnMap(data.id)) {
		/*var dir = undefined;

		switch(data.mapData.direction.toLowerCase()) {
			case 'up':
				dir = DIRECTION.UP;
				break;
			case 'down':
				dir = DIRECTION.DOWN;
				break;
			case 'left':
				dir = DIRECTION.LEFT;
				break;
			case 'right':
				dir = DIRECTION.RIGHT;
				break;
			default:
				alert('spawn_player : wrong direction !');
				return;
		}

		data.mapData.direction = dir;*/

		var c = new Character(data);
		map.addEntity(c);

		return c;
	}

	return false;
}
