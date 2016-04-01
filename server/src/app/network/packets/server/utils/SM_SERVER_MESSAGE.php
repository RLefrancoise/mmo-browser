<?php

namespace App\Network\Packets\Server\Utils;
use LogicException;
use App\Server;
use WebSocketUser;
use App\Network\Packets\Packet;
use Account;
use App\Network\Packets\Server\ServerPacket;

class SM_SERVER_MESSAGE extends ServerPacket {

    const TYPE = 'sm_server_message';

    public static $dataStructure = array(
        'get'  =>  array(
            'msg',
            'color',
        ),
        'set' =>  array(
            'msg',
            'color',
        ),
    );

    public function __construct($data) {
        parent::__construct($data);
    }

    public function getMessage() : string {
        return $this->data['msg'];
    }

    public function setMessage($msg) {
        $this->data['msg'] = $msg;
    }

    public function getColor() {
        return $this->data['color'];
    }

    public function setColor($color) {
        $this->data['color'] = $color;
    }

    public function getType() {
        return SM_SERVER_MESSAGE::TYPE;
    }

    public function doAction(Server $server, WebSocketUser $user) {
        parent::doAction($server, $user);

        $this->send($server, $user);
        //$this->broadcast($server, $user, Packet::BROADCAST_SAME_LOCATION);
    }
}
