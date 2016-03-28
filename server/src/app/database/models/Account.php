<?php

namespace App\Database\Models;

use LogicException;
use App\Database\Models\AbstractModel;
use Doctrine\Common\Collections\ArrayCollection;
use App\Database\Database;

/**
 * @Entity
 * @Table(name="Account")
 */
class Account extends AbstractModel {

    const ADMIN_LEVEL_NONE = 0;
    const ADMIN_LEVEL_USER = 1;
    const ADMIN_LEVEL_MOD = 2;
    const ADMIN_LEVEL_SUPER_MOD = 3;
    const ADMIN_LEVEL_ALL = 4;

    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @Column(type="string", length=50, unique=true, nullable=false)
     */
    protected $login;
    /**
     * @Column(type="string", nullable=false)
     */
    protected $password;
    /**
     * @Column(type="string", unique=true, nullable=false)
     */
    protected $mail;
    /**
     * @Column(type="boolean", options={"default":false})
     */
    protected $isLoggedIn;
    /**
     * @Column(type="string", unique=true, nullable=true)
     */
    protected $connectionToken;
    /**
     * @Column(type="integer", nullable=false, options={"default":1})
     */
    protected $adminLevel;
    /**
     * @OneToMany(targetEntity="Character", mappedBy="account", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    protected $characters = null;

    /**
     * @OneToOne(targetEntity="Character")
     * @JoinColumn(name="currentCharacter", referencedColumnName="id")
     */
    protected $currentCharacter;

    public function __construct()
    {
        parent::__construct();
        $this->characters = new ArrayCollection();

        $this->setIsLoggedIn(false);
        $this->setAdminLevel(Account::ADMIN_LEVEL_USER);
    }

    public function addCharacter($character) : self
    {
        $this->characters[] = $character;
        return $this;
    }

    public function getCharacters()
    {
        return $this->characters;
    }

    /**
     * Retrieves the currently set currentCharacter.
     *
     * @return mixed
     */
    public function getCurrentCharacter()
    {
        return $this->currentCharacter;
    }

    /**
     * Sets the currentCharacter to use.
     *
     * @param mixed $currentCharacter
     *
     * @return $this
     */
    public function setCurrentCharacter($currentCharacter): self
    {
        $this->currentCharacter = $currentCharacter;
        return $this;
    }

    public static function getFromLoginPassword($login, $password, $isLoggedIn = false)
    {
        $search = array(
            'login'         =>      $login,
            'password'      =>      $password,
        );

        if($isLoggedIn) $search['isloggedin'] = 'TRUE';

        return self::findOneBy($search);
    }

    public static function getFromConnectionToken($token, $isLoggedIn = false)
    {
        $search = array(
            'connectiontoken'           =>      $token,
        );

        if($isLoggedIn) $search['isloggedin'] = 'TRUE';

        return self::findOneBy($search);
    }

    public static function logoutAll() {
        $accounts = self::repository()->findByIsLoggedIn(true);

        foreach($accounts as $account) {
            $account->setIsLoggedIn(false);
            $account->setConnectionToken(null);
            $account->save();
        }
    }

    /**
    * @param $connectionToken
    * @return boolean
    * @throws LogicException
    */
    public function login($connectionToken) : bool
    {
        if($this->getIsLoggedIn()) {
            throw new LogicException("Account {$this->getLogin()} (ID: {$this->getId()}) is already logged in.");
        }

        $this->setIsLoggedIn(true);
        $this->setConnectionToken($connectionToken);
        $this->save();
        //Database::get()->save($this);

        return true;
    }

    public function logout() : bool
    {
        if(!$this->getIsLoggedIn()) {
            throw new LogicException("Account {$this->getLogin()} (ID: {$this->getId()}) is already logged out.");
        }

        $this->setIsLoggedIn(false);
        $this->setConnectionToken(null);
        $this->save();
        //Database::get()->save($this);

        return true;
    }

    /**
     * Retrieves the currently set id.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Retrieves the currently set login.
     *
     * @return mixed
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Sets the login to use.
     *
     * @param mixed $login
     *
     * @return $this
     */
    public function setLogin($login): self
    {
        $this->login = $login;
        return $this;
    }

    /**
     * Retrieves the currently set mail.
     *
     * @return mixed
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Sets the mail to use.
     *
     * @param mixed $mail
     *
     * @return $this
     */
    public function setMail($mail): self
    {
        $this->mail = $mail;
        return $this;
    }

    /**
     * Retrieves the currently set password.
     *
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Sets the password to use.
     *
     * @param mixed $password
     *
     * @return $this
     */
    public function setPassword($password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Retrieves the currently set isLoggedIn.
     *
     * @return mixed
     */
    public function getIsLoggedIn()
    {
        return $this->isLoggedIn;
    }

    /**
     * Sets the isLoggedIn to use.
     *
     * @param mixed $isLoggedIn
     *
     * @return $this
     */
    public function setIsLoggedIn($isLoggedIn): self
    {
        $this->isLoggedIn = $isLoggedIn;
        return $this;
    }

    /**
     * Retrieves the currently set connectionToken.
     *
     * @return mixed
     */
    public function getConnectionToken()
    {
        return $this->connectionToken;
    }

    /**
     * Sets the connectionToken to use.
     *
     * @param mixed $connectionToken
     *
     * @return $this
     */
    public function setConnectionToken($connectionToken): self
    {
        $this->connectionToken = $connectionToken;
        return $this;
    }

    /**
     * Retrieves the currently set adminLevel.
     *
     * @return mixed
     */
    public function getAdminLevel()
    {
        return $this->adminLevel;
    }

    /**
     * Sets the adminLevel to use.
     *
     * @param mixed $adminLevel
     *
     * @return $this
     */
    public function setAdminLevel($adminLevel): self
    {
        switch($adminLevel) {
            case Account::ADMIN_LEVEL_NONE:
            case Account::ADMIN_LEVEL_USER:
            case Account::ADMIN_LEVEL_MOD:
            case Account::ADMIN_LEVEL_SUPER_MOD:
            case Account::ADMIN_LEVEL_ALL:
                $this->adminLevel = $adminLevel;
                break;
            default:
                throw new LogicException(__FILE__ . " " . __LINE__ . " - invalid adminLevel $adminLevel (accountId : {$this->getId()})");
                break;
        }

        return $this;
    }
}
