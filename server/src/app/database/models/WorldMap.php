<?php

namespace App\Database\Models;

use App\Database\Models\AbstractModel;

/**
 * World Map model
 * 
 * @package Database/Models
 * 
 * @Entity
 * @Table(name="WorldMap")
 */
class WorldMap extends AbstractModel
{
    /**
     * ID of the world map
     * 
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Name of the world map
     * 
     * @Column(type="string", unique=true, nullable=false)
     */
    protected $name;

    /**
     * Find a WorldMap by its name
     *
     * @param string $mapName name of the map
     * 
     * @return WorldMap|null
     */
    public static function findByName(string $mapName) : ?WorldMap
    {
        return self::findOneBy(array('name' =>  $mapName));
    }

    /**
     * Get WorldMap ID
     *
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * Get WorldMap name
     *
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Set the world map name
     *
     * @param string $name name of the map
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }
}
