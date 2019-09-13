<?php

/**
 * Charset.php
 * 
 * @author Renaud Lefrançoise <renaud.lefrancoise@gmail.com>
 */
namespace App\Database\Models;

use App\Database\Models\AbstractModel;

/**
 * Charset model
 * 
 * @Entity
 * @Table(name="Charset")
 */
class Charset extends AbstractModel
{

    /**
     * Charset ID
     * 
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Charset name
     * 
     * @Column(type="string", length=50, unique=true, nullable=false)
     */
    protected $name;

    /**
     * Charset file name
     * 
     * @Column(type="string", length=50, unique=true, nullable=false)
     */
    protected $file;

    /**
     * Charset is locked ?
     * 
     * @Column(type="boolean", options={"default":false})
     */
    protected $isLocked;

    /**
     * Charset owner
     * 
     * @ManyToOne(targetEntity="Account", cascade={"all"}, fetch="EAGER")
     * @JoinColumn(name="owner",          referencedColumnName="id")
     */
    protected $owner;

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
     * Retrieves the currently set file.
     *
     * @return string
     */
    public function getFile() : string
    {
        return $this->file;
    }

    /**
     * Sets the charset file name
     *
     * @param string $file The file name of the charset
     *
     * @return $this
     */
    public function setFile(string $file): self
    {
        $this->file = $file;
        return $this;
    }

    /**
     * Is charset locked ?
     *
     * @return bool
     */
    public function getIsLocked() : bool
    {
        return $this->isLocked;
    }

    /**
     * Set charset locked
     *
     * @param bool $isLocked Charset locked flag
     *
     * @return $this
     */
    public function setIsLocked(bool $isLocked): self
    {
        $this->isLocked = $isLocked;
        return $this;
    }

    /**
     * Get the charset name
     *
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Sets the charset name
     *
     * @param string $name Name of the charset
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get the charset owner
     *
     * @return Account|null
     */
    public function getOwner() : ?Account
    {
        return $this->owner;
    }

    /**
     * Sets the charset owner
     *
     * @param Account $owner Owner of the charset
     *
     * @return $this
     */
    public function setOwner(Account $owner): self
    {
        $this->owner = $owner;
        return $this;
    }
}
