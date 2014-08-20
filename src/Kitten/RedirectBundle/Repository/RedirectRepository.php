<?php

namespace Kitten\RedirectBundle\Repository;

use Kitten\ApplicationBundle\Repository\BaseRepository;
use Kitten\RedirectBundle\Entity\Redirect;

class RedirectRepository extends BaseRepository {

    private static $allowedCharacters;

    public function __construct($container, $allowedCharacters) {
        parent::__construct($container);
        self::$allowedCharacters = $allowedCharacters;
    }
    
    public function generateSource($url, $expiration = null) {
        if (empty($url)) {
            return false;
        }

        $currentAvailableLength = $this->getCurrentAvailableLength();
        if ($currentAvailableLength !== false) { 
            do {
                $source = self::generateRandomString($currentAvailableLength);
            } while ($this->sourceAlreadyExists($source));
            
            $redirect = new Redirect();
            $redirect
                ->setSource($source)
                ->setDestination($url)
                ->setExpiration($expiration)
                ->setCreated(\time())
            ;
            $entityManager = $this->getManager();
            $entityManager->persist($redirect);
            $entityManager->flush();

            return $redirect;
        }
        
        return false;
    }

    private static function generateRandomString($length = 5) {
        $characters = self::$allowedCharacters;
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[\rand(0, \strlen($characters) - 1)];
        }
        
        return $randomString;
    }

    protected function sourceAlreadyExists($candidate) {
        $entityManager = $this->getManager();
        $redirect = $entityManager->getRepository('KittenRedirectBundle:Redirect')->findOneBySource($candidate);

        return ($redirect instanceof Redirect) ? true : false;
    }

    protected function getCurrentAvailableLength() {
        $connection = $this->getConnection();
        $query = "
            SELECT 
                COUNT(id) AS total
            FROM 
                redirect
            ;
        ";
        $statement = $connection->prepare($query);
        $statement->execute();
        $result = $statement->fetch();

        if (isset($result['total']) && \is_numeric($result['total'])) {
            $total = $this->getCorrectAvailableLength((int) $result['total']);

            return ($total < 5) ? 5 : $total;
        }

        return false;
    }

    private function getCorrectAvailableLength($count) {
        $countBase64 = \base64_encode($count);
        
        return \strlen($countBase64);
    }
}