<?php

namespace App\Network\Packets;
use WebSocketUser;
use LogicException;
use App\Server;
use App\Network\Packets\PacketType;

//require_once(__DIR__ . '/PacketType.php');

abstract class Packet {

    const BROADCAST_SAME_LOCATION   = 1 << 0;
    const BROADCAST_EXLUDE_SELF     = 1 << 1;

    protected $data;
    public static $dataStructure = array(
        'get'  =>  array(
            //'type',
        ),
        'set' =>  array(
        ),
    );

    protected function __construct($data = array()) {
        $data['type'] = $this->getType();

        if(empty($data['type'])) {
            throw new LogicException(__FILE__ . ' ' . __LINE__ . ' - Packet::__construct() : data without a type.');
        }

        $this->data = $data;
    }

    public function getType() {
        $className = explode('\\', get_called_class());
        $className = $className[count($className)-1];
        return strtolower($className);
    }

    public static function create($data) : Packet {
        if(!$data['type']) {
            throw new LogicException(__FILE__ . ' ' . __LINE__ . ' - Packet::create() : packet without a type.');
        }

        $type = explode('_', $data['type'])[1];
        $packetName = '';

        if(!array_key_exists($type, PacketType::$PACKETS)) {
            throw new LogicException(__FILE__ . ' ' . __LINE__ . ' - Packet::create() : invalid packet type ' . $type);
        }

        if(!array_key_exists($data['type'], PacketType::$PACKETS[$type])) {
            throw new LogicException(__FILE__ . ' ' . __LINE__ . ' - Packet::create() : ' . $data['type'] . ' is not a valid login packet type.');
        }
        $packetName = PacketType::$PACKETS[$type][$data['type']];

        /*switch($type) {
            case PacketType::PACKETTYPE_LOGIN:
                if(!array_key_exists($data['type'], PacketType::$LOGIN_PACKETS)) {
                    throw new LogicException(__FILE__ . ' ' . __LINE__ . ' - Packet::create() : ' . $data['type'] . ' is not a valid login packet type.');
                }
                $packetName = PacketType::$LOGIN_PACKETS[$data['type']];
                break;
                case PacketType::PACKETTYPE_CHAT:
                    if(!array_key_exists($data['type'], PacketType::$LOGIN_PACKETS)) {
                        throw new LogicException(__FILE__ . ' ' . __LINE__ . ' - Packet::create() : ' . $data['type'] . ' is not a valid login packet type.');
                    }
                    $packetName = PacketType::$CHAT_PACKETS[$data['type']];
                    break;
            default:
                throw new LogicException(__FILE__ . ' ' . __LINE__ . ' - Packet::create() : invalid packet type ' . $type);
                break;
        }*/

        if(!empty($packetName)) {
            return new $packetName($data);
        }
    }

    public function send(Server $server, WebSocketUser $user) {
        $server->send($user, json_encode($this->data));
    }

    public function broadcast(Server $server, WebSocketUser $user, $flags) {
        $server->broadcast($user, json_encode($this->data), $flags);
    }

    public abstract function doAction(Server $server, WebSocketUser $user);

    protected static function getDataStructure() {
        return array_merge_recursive(Packet::$dataStructure, get_called_class()::$dataStructure);
    }

    /**
     * Serialize a packet to JSON array
     * @return array
     */
    public static function toJSONArray() {
        $json = array(
            'functions' =>  array(
                'get'   =>  array(),
                'set'   =>  array(),
            )
        );

        $className = get_called_class();
        $dataStructure = $className::getDataStructure();

        $_ = explode('\\', $className);
        $className = count($_) == 1 ? $className : $_[count($_)-1];

        $json['name'] = $className;

        foreach($dataStructure as $mode => $dataList) {
            foreach($dataList as $dataName) {
                $json['functions'][$mode][] = $dataName;
            }
        }

        return $json;
    }
}
