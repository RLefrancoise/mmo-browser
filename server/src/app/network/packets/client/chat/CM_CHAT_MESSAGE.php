<?php

namespace App\Network\Packets\Client\Chat;
use LogicException;
use App\Server;
use WebSocketUser;
use App\Network\Packets\Packet;
use App\Network\Packets\Client\Chat\ClientChatPacket;
use App\Database\Models\Account;
use App\Network\Packets\Server\Chat\SM_CHAT_MESSAGE;

class CM_CHAT_MESSAGE extends ClientChatPacket {

    const TYPE = 'cm_chat_message';

    public static $dataStructure = array(
        'get'  =>  array(
            'msg',
        ),
        'set' =>  array(
            'msg',
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

    public function getType() {
        return CM_CHAT_MESSAGE::TYPE;
    }

    public function doAction(Server $server, WebSocketUser $user) {
        parent::doAction($server, $user);
        
        $account = $server->getAccountFromUser($user);
        if($account !== null) {
            (new SM_CHAT_MESSAGE(array(
                'username'  =>  $account->getLogin(),
                'msg'   =>  $this->getMessage(),
            )))->doAction($server, $user);
        }
    }
}
