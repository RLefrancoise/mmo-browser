<?php

namespace App\Database\Models;

use App\Database\Models\AbstractModel;
use MyCLabs\Enum\Enum;

/**
 * Character direction enum
 * 
 * @package Database/Models
 * 
 * @method static CharacterDirection DOWN()
 * @method static CharacterDirection LEFT()
 * @method static CharacterDirection RIGHT()
 * @method static CharacterDirection UP()
 */
class CharacterDirection extends Enum
{
    const DOWN = 0;
    const LEFT = 1;
    const RIGHT = 2;
    const UP = 3;
}

/**
 * Character model
 * 
 * @package Database/Models
 * 
 * @Entity
 * @Table(name="Character")
 */
class Character extends AbstractModel
{
    /**
     * ID of the character
     * 
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Name of the character
     * 
     * @Column(type="string", length=50, unique=true, nullable=false)
     */
    protected $name;

    /**
     * Charset of the character
     * 
     * @OneToOne(targetEntity="Charset")
     * @JoinColumn(name="charset",       referencedColumnName="id", unique=false, nullable=false)
     */
    protected $charset;

    /**
     * World position of the character
     * 
     * @OneToOne(targetEntity="WorldPosition")
     * @JoinColumn(name="worldPosition",       referencedColumnName="id")
     */
    protected $worldPosition;

    /**
     * Account linked to the character
     * 
     * @ManyToOne(targetEntity="Account", cascade={"all"}, inversedBy="characters")
     * @JoinColumn(name="account",        referencedColumnName="id")
     */
    protected $account;

    public function __toString() : string
    {
        $s = '';
        $s .= "Id: {$this->getId()}" . PHP_EOL;
        $s .= "Name: {$this->getName()}" . PHP_EOL;

        return $s;
    }

    /**
     * Get a JSON array from the character
     *
     * @return array
     */
    public function toJSONArray() : array
    {
        return array(
            'id'    =>  $this->getId(),
            'name'      =>  $this->getName(),
            'charset'   =>  $this->getCharset()->getFile(),
            'position'  =>  $this->getWorldPosition()->toJSONArray(),
        );
    }

    /**
     * Get name of the character
     *
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Set the character name
     *
     * @param string $name Name of the character
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get the character ID
     *
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * Get the character world position
     *
     * @return WorldPosition|null
     */
    public function getWorldPosition() : ?WorldPosition
    {
        return $this->worldPosition;
    }

    /**
     * Set the character world position
     *
     * @param WorldPosition $worldPosition The character world position
     *
     * @return $this
     */
    public function setWorldPosition(WorldPosition $worldPosition): self
    {
        $this->worldPosition = $worldPosition;
        return $this;
    }

    /**
     * Get the account linked to the character
     *
     * @return Account
     */
    public function getAccount() : Account
    {
        return $this->account;
    }

    /**
     * Sets account linked to the character
     *
     * @param Account $account Account linked to the character
     *
     * @return $this
     */
    public function setAccount(Account $account): self
    {
        $account->addCharacter($this);
        $this->account = $account;
        return $this;
    }

    /**
     * Is the character in the same location as the given character ?
     *
     * @param Character $c The other character
     * 
     * @return bool
     */
    public function isInSameLocation(Character $c) : bool
    {
        if ($this->getWorldPosition()->getWorldZone()->getId() == $c->getWorldPosition()->getWorldZone()->getId()) return true;
        return false;
    }

    /**
     * Get the charset of the character
     *
     * @return Charset|null
     */
    public function getCharset() : ?Charset
    {
        return $this->charset;
    }

    /**
     * Sets the charset of the character
     *
     * @param Charset $charset The charset of the character
     *
     * @return $this
     */
    public function setCharset(Charset $charset): self
    {
        $this->charset = $charset;
        return $this;
    }
}
