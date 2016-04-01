<?php

namespace App\Database\Models;
use App\Database\Models\AbstractModel;

/**
 * @Entity
 * @Table(name="Charset")
 */
class Charset extends AbstractModel {

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
     * @Column(type="string", length=50, unique=true, nullable=false)
     */
    protected $file;
    /**
     * @Column(type="boolean", options={"default":false})
     */
    protected $isLocked;
    /**
     * @ManyToOne(targetEntity="Account", cascade={"all"}, fetch="EAGER")
     * @JoinColumn(name="owner", referencedColumnName="id")
     */
    protected $owner;

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
     * Retrieves the currently set file.
     *
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Sets the file to use.
     *
     * @param mixed $file
     *
     * @return $this
     */
    public function setFile($file): self
    {
        $this->file = $file;
        return $this;
    }

    /**
     * Retrieves the currently set isLocked.
     *
     * @return mixed
     */
    public function getIsLocked()
    {
        return $this->isLocked;
    }

    /**
     * Sets the isLocked to use.
     *
     * @param mixed $isLocked
     *
     * @return $this
     */
    public function setIsLocked($isLocked): self
    {
        $this->isLocked = $isLocked;
        return $this;
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
     * Retrieves the currently set owner.
     *
     * @return mixed
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Sets the owner to use.
     *
     * @param mixed $owner
     *
     * @return $this
     */
    public function setOwner($owner): self
    {
        $this->owner = $owner;
        return $this;
    }
}
