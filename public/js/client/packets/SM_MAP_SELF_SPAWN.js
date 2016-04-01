screen.scene.self_spawn(this.data);
for(var i = 0 ; i < this.data.players.length ; i++) {
    screen.scene.spawn_player(this.data.players[i]);
}
