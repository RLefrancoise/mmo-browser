<?php
namespace App\Database\Models;
use App\Database\Models\AbstractModel;

/**
 * @Entity
 * @Table(name="WorldMap")
 */
class WorldMap extends AbstractModel {
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

    public static function findByName($mapName) {
        return self::findOneBy(array('name' =>  $mapName));
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
}
