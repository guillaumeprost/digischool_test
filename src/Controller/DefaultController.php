<?php

namespace App\Controller;

use App\Entity\User;
use App\Model\ImdbSearchModel;
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
        return $this->render('base.html.twig');
    }
}
