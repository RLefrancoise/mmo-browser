<?php

namespace App\Database\Models;

use App\Database\Models\AbstractModel;

/**
 * World Position model
 * 
 * @package Database/Models
 * 
 * @Entity
 * @Table(name="WorldPosition")
 */
class WorldPosition extends AbstractModel
{
    /**
     * World Position ID
     * 
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * World Zone
     * 
     * @ManyToOne(targetEntity="WorldZone", cascade={"all"}, fetch="EAGER")
     * @JoinColumn(name="worldZone",        referencedColumnName="id")
     */
    protected $worldZone;

    /**
     * X coordinate
     * 
     * @Column(type="integer", nullable=false, options={"unsigned":true})
     */
    protected $x;

    /**
     * Y coordinate
     * 
     * @Column(type="integer", nullable=false, options={"unsigned":true})
     */
    protected $y;

    /**
     * Direction
     * 
     * @Column(type="integer", nullable=false, options={"unsigned":true})
     */
    protected $direction;

    /**
     * Get a JSON array from the WorldPosition
     *
     * @return array
     */
    public function toJSONArray() : array
    {
        return array(
            'x' =>  $this->getX(),
            'y' =>  $this->getY(),
            'direction' =>  $this->getDirection(),
        );
    }

    /**
     * Get WorldPosition ID
     *
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * Get the world zone linked to the position
     *
     * @return WorldZone
     */
    public function getWorldZone() : WorldZone
    {
        return $this->worldZone;
    }

    /**
     * Set the world zone linked to the position
     *
     * @param WorldZone $worldZone World zone linked to the position
     *
     * @return $this
     */
    public function setWorldZone(WorldZone $worldZone): self
    {
        $this->worldZone = $worldZone;
        return $this;
    }

    /**
     * Get the world position direction
     *
     * @return CharacterDirection
     */
    public function getDirection() : CharacterDirection
    {
        return $this->direction;
    }

    /**
     * Set the world position direction
     *
     * @param CharacterDirection $direction the world position direction
     *
     * @return $this
     */
    public function setDirection(CharacterDirection $direction): self
    {
        if (self::isValidDirection($direction)) {
            $this->direction = $direction;
        }

        return $this;
    }

    /**
     * Get the X coordinate
     *
     * @return int
     */
    public function getX() : int
    {
        return $this->x;
    }

    /**
     * Set the X coordinate
     *
     * @param int $x X coordinate
     *
     * @return $this
     */
    public function setX(int $x): self
    {
        $this->x = $x;
        return $this;
    }

    /**
     * Get the Y coordinate
     *
     * @return int
     */
    public function getY() : int
    {
        return $this->y;
    }

    /**
     * Set the Y coordinate
     *
     * @param int $y Y coordinate
     *
     * @return $this
     */
    public function setY(int $y): self
    {
        $this->y = $y;
        return $this;
    }

    /**
     * Is given direction valid ?
     *
     * @param CharacterDirection $direction direction to test
     * 
     * @return bool
     */
    public static function isValidDirection(CharacterDirection $direction) : bool
    {
        switch($direction) {
            case CharacterDirection::DOWN():
            case CharacterDirection::LEFT():
            case CharacterDirection::RIGHT():
            case CharacterDirection::UP():
                return true;
            default:
                return false;
        }
    }
}
