<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\ImdbService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class DefaultController
 * @package App\Controller
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function index(ImdbService $service)
    {
        $user = new User();
        $user->setPseudo('bruce_wayne');

        $service->easterEgg($user);

        return $this->render('base.html.twig');
    }
}
