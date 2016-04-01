var x = this.data.position.x;
var y = this.data.position.y;
var dir = this.data.position.direction;

var c = screen.scene.map.getEntity(this.data.id);

if(c) {
    //check x coordinate, warp player if position is too far than the current position, else move player normally
    if(Math.abs(x - c.x) > 1) {
        c.x = x;
        c.direction = dir;
    } else {
        if(x < c.x) {
            c.deplacer(MapEntity.DIRECTION.LEFT, screen.scene.map);
        }
        if(x > c.x) {
            c.deplacer(MapEntity.DIRECTION.RIGHT, screen.scene.map);
        }
    }

    //check y coordinate, warp player if position is too far than the current position, else move player normally
    if(Math.abs(y - c.y) > 1) {
        c.y = y;
        c.direction = dir;
    } else {
        if(y < c.y) {
            c.deplacer(MapEntity.DIRECTION.UP, screen.scene.map);
        }
        if(y > c.y) {
            c.deplacer(MapEntity.DIRECTION.DOWN, screen.scene.map);
        }
    }
}
else writeMessage("Failed to get character with id: " + this.data.id, 'rgb(255,0,0)');
