<?php
namespace App\Database\Models;
use App\Server;
use App\Database\Models\AbstractModel;

/**
 * @Entity
 * @Table(name="WorldZone")
 */
class WorldZone extends AbstractModel {
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @Column(type="string", unique=true, nullable=false)
     */
    protected $name;
    /**
     * @ManyToOne(targetEntity="WorldMap", cascade={"all"}, fetch="EAGER")
     * @JoinColumn(name="worldmap", referencedColumnName="id")
     */
    protected $worldMap;

    public function getData(Server $server) {
        return $server->getGameData('map', array(
            'name'  =>  $this->getName(),
        ));
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
     * Retrieves the currently set worldMap.
     *
     * @return mixed
     */
    public function getWorldMap()
    {
        return $this->worldMap;
    }

    /**
     * Sets the worldMap to use.
     *
     * @param mixed $worldMap
     *
     * @return $this
     */
    public function setWorldMap($worldMap): self
    {
        $this->worldMap = $worldMap;
        return $this;
    }
}
