<?php

namespace App\Database\Models;

use App\Database\Models\AbstractModel;

/**
 * @Entity
 * @Table(name="WorldPosition")
 */
class WorldPosition extends AbstractModel {
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ManyToOne(targetEntity="WorldZone", cascade={"all"}, fetch="EAGER")
     * @JoinColumn(name="worldZone", referencedColumnName="id")
     */
    protected $worldZone;

    /**
     * @Column(type="integer", nullable=false, options={"unsigned":true})
     */
    protected $x;
    /**
     * @Column(type="integer", nullable=false, options={"unsigned":true})
     */
    protected $y;
    /**
     * @Column(type="integer", nullable=false, options={"unsigned":true})
     */
    protected $direction;

    public function toJSONArray() {
        return array(
            'x' =>  $this->getX(),
            'y' =>  $this->getY(),
            'direction' =>  $this->getDirection(),
        );
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
     * Retrieves the currently set worldZone.
     *
     * @return mixed
     */
    public function getWorldZone()
    {
        return $this->worldZone;
    }

    /**
     * Sets the worldZone to use.
     *
     * @param mixed $worldZone
     *
     * @return $this
     */
    public function setWorldZone($worldZone): self
    {
        $this->worldZone = $worldZone;
        return $this;
    }

    /**
     * Retrieves the currently set direction.
     *
     * @return mixed
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * Sets the direction to use.
     *
     * @param mixed $direction
     *
     * @return $this
     */
    public function setDirection($direction): self
    {
        if(self::isValidDirection($direction)) {
            $this->direction = $direction;
        }

        return $this;
    }

    /**
     * Retrieves the currently set x.
     *
     * @return mixed
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * Sets the x to use.
     *
     * @param mixed $x
     *
     * @return $this
     */
    public function setX($x): self
    {
        $this->x = $x;
        return $this;
    }

    /**
     * Retrieves the currently set y.
     *
     * @return mixed
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * Sets the y to use.
     *
     * @param mixed $y
     *
     * @return $this
     */
    public function setY($y): self
    {
        $this->y = $y;
        return $this;
    }

    public static function isValidDirection($direction) {
        switch($direction) {
            case Character::DIRECTION_DOWN:
            case Character::DIRECTION_LEFT:
            case Character::DIRECTION_RIGHT:
            case Character::DIRECTION_UP:
                return true;
            default:
                return false;
        }
    }
}
