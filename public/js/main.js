var map = new Map("map1");
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
	}));

window.onload = function() {
	var canvas = document.getElementById('canvas');
	var ctx = canvas.getContext('2d');
	ctx.font = 'normal 10pt Arial';
	
	// Gestion du clavier
	window.addEventListener('keyup', function(event) { Key.onKeyup(event); }, false);
	window.addEventListener('keydown', function(event) { Key.onKeydown(event); }, false);
	
	//rendering loop
	setInterval(function() {
		map.draw(ctx);
	}, 40);
	
	//update loop
	setInterval(function() {
	
		//player realtime events
		if(Key.isDown(Key.UP) || Key.isDown(Key.Z)) player.deplacer(DIRECTION.UP, map);
		if(Key.isDown(Key.DOWN) || Key.isDown(Key.S)) player.deplacer(DIRECTION.DOWN, map);
		if(Key.isDown(Key.LEFT) || Key.isDown(Key.Q)) player.deplacer(DIRECTION.LEFT, map);
		if(Key.isDown(Key.RIGHT) || Key.isDown(Key.D)) player.deplacer(DIRECTION.RIGHT, map);
		
		
	}, 20);
	
	window.onkeydown = function(event) {
		var e = event || window.event;
		var key = e.which || e.keyCode;
		
		switch(key) {
			case Key.Space:
				player.displayData = !player.displayData;
				break;
			default : 
				//alert(key);
				// Si la touche ne nous sert pas, nous n'avons aucune raison de bloquer son comportement normal.
				return true;
		}

		return false;
	}
}