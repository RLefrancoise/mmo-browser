<?php

namespace App\Network\Packets\Server\Chat;

use App\Server;
use WebSocketUser;
use App\Network\Packets\Packet;
use App\Network\Packets\Server\Chat\ServerChatPacket;

class SM_CHAT_MESSAGE extends ServerChatPacket
{
    const TYPE = 'sm_chat_message';

    public static $dataStructure = array(
        'get'  =>  array(
            'username',
            'msg',
            'color',
        ),
        'set' =>  array(
            'username',
            'msg',
            'color',
        ),
    );

    public function __construct($data)
    {
        parent::__construct($data);
    }

    public function getUserName()
    {
        return $this->data['username'];
    }

    public function setUserName($userName)
    {
        $this->data['username'] = $userName;
    }

    public function getMessage() : string
    {
        return $this->data['msg'];
    }

    public function setMessage($msg)
    {
        $this->data['msg'] = $msg;
    }

    public function getColor()
    {
        return $this->data['color'];
    }

    public function setColor($color)
    {
        $this->data['color'] = $color;
    }

    public function getType()
    {
        return SM_CHAT_MESSAGE::TYPE;
    }

    public function doAction(Server $server, WebSocketUser $user)
    {
        parent::doAction($server, $user);
        $this->broadcast($server, $user, Packet::BROADCAST_SAME_LOCATION);
    }
}
