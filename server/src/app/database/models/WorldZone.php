<?php

namespace App\Database\Models;

use App\Server;
use App\Database\Models\AbstractModel;

/**
 * WorldZone model
 * 
 * @package Database/Models
 * 
 * @Entity
 * @Table(name="WorldZone")
 */
class WorldZone extends AbstractModel
{
    /**
     * World zone ID
     * 
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * World zone name
     * 
     * @Column(type="string", unique=true, nullable=false)
     */
    protected $name;

    /**
     * World zone map
     * 
     * @ManyToOne(targetEntity="WorldMap", cascade={"all"}, fetch="EAGER")
     * @JoinColumn(name="worldmap",        referencedColumnName="id")
     */
    protected $worldMap;

    /**
     * Get data of the zone
     *
     * @param Server $server The game server instance
     * 
     * @return void
     */
    public function getData(Server $server)
    {
        return $server->getGameData(
            'map', array('name'  =>  $this->getName(),)
        );
    }

    /**
     * Get WorldZone ID
     *
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * Get the world zone name
     *
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Set the world zone name
     *
     * @param string $name The name of the zone
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get the world zone map
     *
     * @return WorldMap
     */
    public function getWorldMap() : WorldMap
    {
        return $this->worldMap;
    }

    /**
     * Set the world zone map
     *
     * @param WorldMap $worldMap The world zone map
     *
     * @return $this
     */
    public function setWorldMap(WorldMap $worldMap): self
    {
        $this->worldMap = $worldMap;
        return $this;
    }
}
