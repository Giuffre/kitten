<?php

namespace Kitten\RedirectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Kitten\RedirectBundle\Entity\Redirect;

class RedirectController extends Controller
{
    public function createRedirectAction(Request $request) {
        $url = $request->get('url');

        if (\filter_var($url, FILTER_VALIDATE_URL)) {
            $redirectRepository = $this->get('kitten.redirect_repository');
            $redirect = $redirectRepository->generateSource($url);
            if ($redirect instanceof Redirect) {
                return new JsonResponse(array(
                    'url' => $url, 
                    'shorten' => $this->generateUrl('kitten_redirect_redirect', array('shorten' => $redirect->getSource()), true),
                    )
                );
            }
        }

        return new JsonResponse(array(), 400);
    }

    public function redirectAction($shorten) {
        $entityManager = $this->get('doctrine');
        $redirect = $entityManager->getRepository('KittenRedirectBundle:Redirect')->findOneBySource($shorten);
        if ($redirect instanceof Redirect) {
            $destination = $redirect->getDestination();
            return new RedirectResponse($destination, 301);
        }

        throw $this->createNotFoundException();
    }
}
