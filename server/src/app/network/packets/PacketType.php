<?php

namespace App\Network\Packets;

use App\Network\Packets\Client\Chat\CM_CHAT_MESSAGE;
use App\Network\Packets\Client\Login\CM_LOGIN_REQUEST;

use App\Network\Packets\Client\Map\CM_MAP_PLAYER_MOVE;
use App\Network\Packets\Server\Chat\SM_CHAT_MESSAGE;
use App\Network\Packets\Server\Login\SM_LOGIN_ERROR;
use App\Network\Packets\Server\Map\SM_MAP_SELF_SPAWN;
use App\Network\Packets\Server\Map\SM_MAP_PLAYER_MOVE;
use App\Network\Packets\Server\Map\SM_MAP_SPAWN_PLAYER;
use App\Network\Packets\Server\Map\SM_MAP_PLAYER_EXITS_MAP;
use App\Network\Packets\Server\Utils\SM_SERVER_MESSAGE;

class PacketType
{
    const PACKETTYPE_LOGIN = 'login';
    const PACKETTYPE_CHAT = 'chat';
    const PACKETTYPE_MAP = 'map';
    const PACKETTYPE_UTILS = 'utils';

    public static $PACKETS = array(
        //LOGIN

        //-- client packets --
        CM_LOGIN_REQUEST::TYPE     =>  'App\Network\Packets\Client\Login\CM_LOGIN_REQUEST',

        //-- server packets --
        SM_LOGIN_ERROR::TYPE       =>  'App\Network\Packets\Server\Login\SM_LOGIN_ERROR',

        //CHAT

        //-- client packets --
        CM_CHAT_MESSAGE::TYPE       =>  'App\Network\Packets\Client\Chat\CM_CHAT_MESSAGE',

        //-- server packets --
        SM_CHAT_MESSAGE::TYPE       =>  'App\Network\Packets\Server\Chat\SM_CHAT_MESSAGE',

        //MAP

        //-- client packets --
        CM_MAP_PLAYER_MOVE::TYPE    =>  'App\Network\Packets\Client\Map\CM_MAP_PLAYER_MOVE',
        //-- server packets --
        SM_MAP_SELF_SPAWN::TYPE             =>  'App\Network\Packets\Server\Map\SM_MAP_SELF_SPAWN',
        SM_MAP_PLAYER_MOVE::TYPE            =>  'App\Network\Packets\Server\Map\SM_MAP_PLAYER_MOVE',
        SM_MAP_SPAWN_PLAYER::TYPE           =>  'App\Network\Packets\Server\Map\SM_MAP_SPAWN_PLAYER',
        SM_MAP_PLAYER_EXITS_MAP::TYPE      =>  'App\Network\Packets\Server\Map\SM_MAP_PLAYER_EXITS_MAP',

        //UTILS

        //-- server packets --
        SM_SERVER_MESSAGE::TYPE             =>  'App\Network\Packets\Server\Utils\SM_SERVER_MESSAGE',
    );

    private function __construct()
    {
    }
}
