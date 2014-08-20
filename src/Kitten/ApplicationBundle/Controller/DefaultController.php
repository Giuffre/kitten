<?php

namespace Kitten\ApplicationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('KittenApplicationBundle:Default:index.html.twig');
    }
}
