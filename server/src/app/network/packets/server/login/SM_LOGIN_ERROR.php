<?php

namespace App\Network\Packets\Server\Login;
use LogicException;
use App\Network\Packets\Packet;
use App\Server;
use WebSocketUser;

class SM_LOGIN_ERROR extends Packet {

    const TYPE = 'sm_login_error';

    public function getError() : string {
        return $this->data['error'];
    }

    public function getType() {
        return SM_LOGIN_ERROR::TYPE;
    }

    public function doAction(Server $server, WebSocketUser $user) {
        parent::doAction($server, $user);
    }
}
