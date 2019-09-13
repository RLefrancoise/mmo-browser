<?php

/**
 * Account.php
 * 
 * @author Renaud Lefrançoise <renaud.lefrancoise@gmail.com>
 */
namespace App\Database\Models;

use LogicException;
use App\Database\Models\AbstractModel;
use Doctrine\Common\Collections\ArrayCollection;
use MyCLabs\Enum\Enum;

/**
 * Admin Level values for an account.
 * 
 * @package Database/Models
 * 
 * @method static AdminLevel NONE()
 * @method static AdminLevel USER()
 * @method static AdminLevel MOD()
 * @method static AdminLevel SUPER_MOD()
 * @method static AdminLevel ALL()
 */
class AdminLevel extends Enum
{
    private const NONE = 0;
    private const USER = 1;
    private const MOD = 2;
    private const SUPER_MOD = 3;
    private const ALL = 4;
}

/**
 * Account model
 * 
 * @package Database/Models
 * 
 * @Entity
 * @Table(name="Account")
 */
class Account extends AbstractModel
{
    /**
     * ID of the account.
     * 
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Login of the account
     * 
     * @Column(type="string", length=50, unique=true, nullable=false)
     */
    protected $login;

    /**
     * Password MD5 of the account
     * 
     * @Column(type="string", nullable=false)
     */
    protected $password;

    /**
     * Mail of the account
     * 
     * @Column(type="string", unique=true, nullable=false)
     */
    protected $mail;

    /**
     * Is logged in ?
     * 
     * @Column(type="boolean", options={"default":false})
     */
    protected $isLoggedIn;

    /**
     * Connection token of the account
     * 
     * @Column(type="string", unique=true, nullable=true)
     */
    protected $connectionToken;

    /**
     * Admin level of the account
     * 
     * @Column(type="integer", nullable=false, options={"default":1})
     */
    protected $adminLevel;

    /**
     * Characters of the account
     * 
     * @OneToMany(targetEntity="Character", mappedBy="account", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    protected $characters = null;

    /**
     * Current character of the account
     * 
     * @OneToOne(targetEntity="Character")
     * @JoinColumn(name="currentCharacter", referencedColumnName="id")
     */
    protected $currentCharacter;

    public function __construct()
    {
        parent::__construct();
        $this->characters = new ArrayCollection();

        $this->setIsLoggedIn(false);
        $this->setAdminLevel(AdminLevel::USER());
    }

    /**
     * Add a character to the account
     *
     * @param Character $character The character to add
     * 
     * @return self
     */
    public function addCharacter(Character $character) : self
    {
        $this->characters[] = $character;
        return $this;
    }

    /**
     * Get the characters of the account
     *
     * @return ArrayCollection
     */
    public function getCharacters() : ArrayCollection
    {
        return $this->characters;
    }

    /**
     * Retrieves the currently set currentCharacter.
     *
     * @return Character|null
     */
    public function getCurrentCharacter() : ?Character
    {
        return $this->currentCharacter;
    }

    /**
     * Sets the currentCharacter to use.
     *
     * @param Character|null $currentCharacter Character to set to the account
     *
     * @return $this
     */
    public function setCurrentCharacter(?Character $currentCharacter): self
    {
        $this->currentCharacter = $currentCharacter;
        return $this;
    }

    /**
     * Get an account from a login and a password
     *
     * @param string  $login      The login of the character
     * @param string  $password   The password of the character
     * @param boolean $isLoggedIn Should the account be logged in or not ?
     * 
     * @return Account|null
     */
    public static function getFromLoginPassword(string $login, string $password, bool $isLoggedIn = false) : ?Account
    {
        $search = array(
            'login'         =>      $login,
            'password'      =>      $password,
        );

        if ($isLoggedIn) $search['isloggedin'] = 'TRUE';

        return self::findOneBy($search);
    }

    /**
     * Get an account from a connection token
     *
     * @param string  $token      Token of the account
     * @param boolean $isLoggedIn Should the account be logged in or not ?
     * 
     * @return Account|null
     */
    public static function getFromConnectionToken(string $token, bool $isLoggedIn = false) : ?Account
    {
        $search = array(
            'connectiontoken'           =>      $token,
        );

        if ($isLoggedIn) $search['isloggedin'] = 'TRUE';

        return self::findOneBy($search);
    }

    /**
     * Log out all accounts.
     *
     * @return void
     */
    public static function logoutAll()
    {
        $accounts = Account::repository()->findByIsLoggedIn(true);

        foreach ($accounts as $account) {
            $account->setIsLoggedIn(false);
            $account->setConnectionToken(null);
            $account->save();
        }
    }

    /**
     * Log a user and assign a connection token to the account.
     * 
     * @param string $connectionToken The connection token to assign to the account
     * 
     * @return bool
     * @throws LogicException
     */
    public function login(string $connectionToken) : bool
    {
        if ($this->getIsLoggedIn()) {
            throw new LogicException("Account {$this->getLogin()} (ID: {$this->getId()}) is already logged in.");
        }

        $this->setIsLoggedIn(true);
        $this->setConnectionToken($connectionToken);
        $this->save();

        return true;
    }

    /**
     * Log out an account
     *
     * @return $this
     * @throws LogicException
     */
    public function logout() : self
    {
        if (!$this->getIsLoggedIn()) {
            throw new LogicException("Account {$this->getLogin()} (ID: {$this->getId()}) is already logged out.");
        }

        $this->setIsLoggedIn(false);
        $this->setConnectionToken(null);
        $this->save();

        return $this;
    }

    /**
     * Retrieves the currently set id.
     *
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * Retrieves the currently set login.
     *
     * @return string
     */
    public function getLogin() : string
    {
        return $this->login;
    }

    /**
     * Sets the login to use.
     *
     * @param string $login The login to set
     *
     * @return $this
     */
    public function setLogin(string $login): self
    {
        $this->login = $login;
        return $this;
    }

    /**
     * Retrieves the currently set mail.
     *
     * @return string
     */
    public function getMail() : string
    {
        return $this->mail;
    }

    /**
     * Sets the mail to use.
     *
     * @param string $mail The mail to set
     *
     * @return $this
     */
    public function setMail(string $mail): self
    {
        $this->mail = $mail;
        return $this;
    }

    /**
     * Retrieves the currently set password.
     *
     * @return string
     */
    public function getPassword() : string
    {
        return $this->password;
    }

    /**
     * Sets the password to use.
     *
     * @param string $password The password to set
     *
     * @return $this
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Retrieves the currently set isLoggedIn.
     *
     * @return bool
     */
    public function getIsLoggedIn() : bool
    {
        return $this->isLoggedIn;
    }

    /**
     * Sets the isLoggedIn flag.
     *
     * @param bool $isLoggedIn Flag value
     *
     * @return $this
     */
    public function setIsLoggedIn(bool $isLoggedIn): self
    {
        $this->isLoggedIn = $isLoggedIn;
        return $this;
    }

    /**
     * Retrieves the currently set connectionToken.
     *
     * @return string|null
     */
    public function getConnectionToken() : ?string
    {
        return $this->connectionToken;
    }

    /**
     * Sets the connectionToken to use.
     *
     * @param string|null $connectionToken The connection token to set
     *
     * @return $this
     */
    public function setConnectionToken(?string $connectionToken): self
    {
        $this->connectionToken = $connectionToken;
        return $this;
    }

    /**
     * Retrieves the currently set adminLevel.
     *
     * @return AdminLevel
     */
    public function getAdminLevel() : AdminLevel
    {
        return $this->adminLevel;
    }

    /**
     * Sets the administration level of the account.
     *
     * @param AdminLevel $adminLevel The administration level to set
     *
     * @return $this
     * @throws LogicException
     */
    public function setAdminLevel(AdminLevel $adminLevel): self
    {
        switch($adminLevel) {
        case AdminLevel::NONE():
        case AdminLevel::USER():
        case AdminLevel::MOD():
        case AdminLevel::SUPER_MOD():
        case AdminLevel::ALL():
            $this->adminLevel = $adminLevel;
            break;
        default:
            throw new LogicException(__FILE__ . " " . __LINE__ . " - invalid adminLevel $adminLevel (accountId : {$this->getId()})");
            break;
        }

        return $this;
    }
}
