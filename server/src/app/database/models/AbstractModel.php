<?php

namespace App\Database\Models;
use App\Database\Database;
use Doctrine\ORM\EntityManager;

abstract class AbstractModel {

    public function __construct() {
    }

    public function save() {
        Database::get()->getEntityManager()->persist($this);
        Database::get()->getEntityManager()->flush($this);
    }

    public static function findById($id) {
        return Database::get()->getEntityManager()->getRepository(get_called_class())->find(array('id'    =>  $id));
        //return self::find(array('id'    =>  $id));
    }

    public static function findBy($params) {
        /*$className = get_called_class();
        $sql = "SELECT m FROM $className m";

        $i = 0;
        foreach($params as $key => $value) {
            if($i == 0) $sql .= " WHERE m.{$key} = :{$key}";
            else $sql .= " AND m.{$key} = :{$key}";
            $i++;
        }

        $query = Database::get()->getEntityManager()->createQuery($sql);
        $query->setParameters($params);

        return $query->getResult();*/
        return Database::get()->getEntityManager()->getRepository(get_called_class())->findBy($params);
    }

    public static function findOneBy($params) {
        return Database::get()->getEntityManager()->getRepository(get_called_class())->findOneBy($params);
    }

    public static function repository() {
        return Database::get()->getEntityManager()->getRepository(get_called_class());
    }
}
