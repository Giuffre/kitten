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

    public static function sanitizeUrl($url) {
        static::addhttp($url);

        return \filter_var($url, FILTER_VALIDATE_URL);
    }

    private static function addhttp(&$url) {
        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = "http://" . $url;
        }
    }
}