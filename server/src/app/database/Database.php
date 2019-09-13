<?php

namespace App\Database;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class Database
{
    protected $entityManager;
    protected static $inst;

    public static function get() : Database
    {
        if (!Database::$inst) {
            Database::$inst = new Database();
        }

        return Database::$inst;
    }

    protected function __construct()
    {
        $isDevMode = true;

        // the connection configuration
        $dbParams = array(
            'driver'   => 'pdo_pgsql',
            'user'     => 'postgres',
            'password' => 'Ta!Of!Sy!035',
            'dbname'   => 'exitium_rpg',
        );

        $config = Setup::createAnnotationMetadataConfiguration(array(__DIR__ . '/models'), $isDevMode);
        $this->entityManager = EntityManager::create($dbParams, $config);
    }

    /**
     * Retrieves the currently set entityManager.
     *
     * @return EntityManager
     */
    public function getEntityManager() : EntityManager
    {
        return $this->entityManager;
    }

    public function fromTable($tableName) : EntityRepository
    {
        return $this->entityManager->getRepository($tableName);
    }

    public function createQuery($dql) : Query
    {
        return $this->entityManager->createQuery($dql);
    }

    public function save($model) : self
    {
        $this->entityManager->persist($model);
        return $this;
    }

    public function commit() : self
    {
        $this->entityManager->flush();
        return $this;
    }
}
