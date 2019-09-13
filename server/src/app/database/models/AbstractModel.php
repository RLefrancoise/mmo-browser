<?php

namespace App\Database\Models;

use App\Database\Database;
use Doctrine\ORM\EntityRepository;

/**
 * Base class for all Database models.
 * 
 * @package Database/Models
 */
abstract class AbstractModel
{
    public function __construct()
    {
    }

    /**
     * Save the model into the database
     *
     * @return self
     */
    public function save() : self
    {
        Database::get()->getEntityManager()->persist($this);
        Database::get()->getEntityManager()->flush($this);
        return $this;
    }

    public static function findById($id) : ?AbstractModel
    {
        return get_called_class()::repository(get_called_class())->find(array('id'    =>  $id));
    }

    public static function findBy($params) : array
    {
        return get_called_class()::repository(get_called_class())->findBy($params);
    }

    public static function findOneBy($params) : ?AbstractModel
    {
        return get_called_class()::repository(get_called_class())->findOneBy($params);
    }

    public static function repository() : EntityRepository
    {
        return Database::get()->getEntityManager()->getRepository(get_called_class());
    }
}
