<?php

namespace App\Database\Models;

use App\Database\Models\AbstractModel;

/**
 * @Entity
 * @Table(name="Character")
 */
class Character extends AbstractModel {

    const DIRECTION_DOWN = 0;
    const DIRECTION_LEFT = 1;
    const DIRECTION_RIGHT = 2;
    const DIRECTION_UP = 3;

    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @Column(type="string", length=50, unique=true, nullable=false)
     */
    protected $name;

    /**
     * @Column(type="string", unique=false, nullable=false)
     */
    protected $charset;

    /**
     * @OneToOne(targetEntity="WorldPosition")
     * @JoinColumn(name="worldPosition", referencedColumnName="id")
     */
    protected $worldPosition;

    /**
     * @ManyToOne(targetEntity="Account", cascade={"all"}, inversedBy="characters")
     */
    protected $account;

    public function __toString() : string
    {
        $s = '';
        $s .= "Id: {$this->getId()}" . PHP_EOL;
        $s .= "Name: {$this->getName()}" . PHP_EOL;

        return $s;
    }

    public function toJSONArray() {
        return array(
            'id'    =>  $this->getId(),
            'name'      =>  $this->getName(),
            'charset'   =>  $this->getCharset(),
            'position'  =>  $this->getWorldPosition()->toJSONArray(),
        );
    }

    /**
     * Retrieves the currently set name.
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name to use.
     *
     * @param mixed $name
     *
     * @return $this
     */
    public function setName($name): self
    {
        $this->name = $name;
        return $this;
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
     * Retrieves the currently set worldPosition.
     *
     * @return mixed
     */
    public function getWorldPosition()
    {
        return $this->worldPosition;
    }

    /**
     * Sets the worldPosition to use.
     *
     * @param mixed $worldPosition
     *
     * @return $this
     */
    public function setWorldPosition($worldPosition): self
    {
        $this->worldPosition = $worldPosition;
        return $this;
    }

    /**
     * Retrieves the currently set account.
     *
     * @return mixed
     */
    public function getAccount() : Account
    {
        return $this->account;
    }

    /**
     * Sets the account to use.
     *
     * @param mixed $account
     *
     * @return $this
     */
    public function setAccount(Account $account): self
    {
        $account->addCharacter($this);
        $this->account = $account;
        return $this;
    }

    public function isInSameLocation(Character $c = null) {
        if($c == null) return false;
        if($this->getWorldPosition()->getWorldZone()->getId() == $c->getWorldPosition()->getWorldZone()->getId()) return true;
        return false;
    }

    /**
     * Retrieves the currently set charset.
     *
     * @return mixed
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * Sets the charset to use.
     *
     * @param mixed $charset
     *
     * @return $this
     */
    public function setCharset($charset): self
    {
        $this->charset = $charset;
        return $this;
    }
}
