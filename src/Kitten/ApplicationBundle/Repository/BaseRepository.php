<?php

namespace Kitten\ApplicationBundle\Repository;

class BaseRepository {
    
    protected $container;

    public function __construct($container) {
        $this->container = $container;
    }

    protected function getManager() {
        return $this->container->get('doctrine.orm.entity_manager');
    }

    protected function getConnection() {
        return $this->getManager()->getConnection();
    }
}