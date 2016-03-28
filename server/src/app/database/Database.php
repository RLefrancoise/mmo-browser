<?php

namespace App\Database;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class Database {

    protected $entityManager;
    protected static $inst = null;

    public static function get() {
        if(Database::$inst == null) {
            Database::$inst = new Database();
        }

        return Database::$inst;
    }

    protected function __construct() {
        $isDevMode = true;

        // the connection configuration
        $dbParams = array(
            'driver'   => 'pdo_pgsql',
            'user'     => 'postgres',
            'password' => 'ThOfSh0!',
            'dbname'   => 'exitium_rpg',
        );

        $config = Setup::createAnnotationMetadataConfiguration(array(__DIR__ . '/models'), $isDevMode);
        //$config = Setup::createYAMLMetadataConfiguration(array(__DIR__."/../../../config/yaml"), $isDevMode);
        $this->entityManager = EntityManager::create($dbParams, $config);
    }

    /**
     * Retrieves the currently set entityManager.
     *
     * @return mixed
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    public function fromTable($tableName) {
        return $this->entityManager->getRepository($tableName);
    }

    public function createQuery($dql) {
        return $this->entityManager->createQuery($dql);
    }

    public function save($model) : self {
        $this->entityManager->persist($model);
        return $this;
    }

    public function commit() : self {
        $this->entityManager->flush();
        return $this;
    }
}
