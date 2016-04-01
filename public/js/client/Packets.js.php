<?php
use App\Network\Packets\PacketType;

header("Content-type: application/x-javascript");

require_once (__DIR__ . '/../../../server/vendor/autoload.php');
require_once (__DIR__ . '/../../../server/bootstrap.php');

use App\Autoloader;

Autoloader::register();

?>

var Packet = Class.create({

    initialize: function(data, socket) {
        this.data = data;
        this.socket = socket;
    },

    send: function() {
        this.data.type = this.get_type();
        this.socket.send(JSON.stringify(this.data));
    },

    doAction: function() {}
});

<?php


$packets_list = PacketType::$PACKETS;

$packets = array();
//foreach($tmp as $packets_list) {
    foreach($packets_list as $type => $packet) {
        $packets[] = array(
            'type'  =>  $type,
            'name'  =>  $packet,
        );
    }
//}

?>
Packet.createFromType = function(data, socket) {
    switch(data.type) {
<?php
    foreach($packets as $packet) {
        $packet_class = $packet['name'];
        $json_array = $packet_class::toJSONArray();
?>
        case '<?=$packet['type']?>':
            return new <?=$json_array['name']?>(data, socket);
            break;
<?php
    }
?>
    }
}
<?php

foreach($packets as $packet) {
    $packet_class = $packet['name'];
    $json_array = $packet_class::toJSONArray();
?>
/* <?=$json_array['name']?> */
var <?=$json_array['name']?> = Class.create(Packet, {
<?php
    foreach($json_array['functions'] as $fnc_type => $fnc_list) {

        foreach($fnc_list as $fnc_name) {
            switch($fnc_type) {
                case 'get':
?>

    get_<?=$fnc_name?>: function() { return this.data['<?=$fnc_name?>'] },
<?php
                    break;
                case 'set':
?>

    set_<?=$fnc_name?>: function(<?=$fnc_name?>) { this.data['<?=$fnc_name?>'] = <?=$fnc_name?>; },
<?php
                    break;
            }
        }
    }

    if(file_exists(__DIR__ . '/packets/' . $json_array['name'] . '.js')) {
?>
    doAction: function() {
<?php
        include __DIR__ . '/packets/' . $json_array['name'] . '.js';
?>
    }
<?php
    }
?>
});

<?=$json_array['name']?>.prototype.get_type = function() {
    return '<?=$packet['type']?>';
}
<?php
    echo PHP_EOL;
}
