<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;

class AccountController extends AbstractController
{
    /**
     * @Route("/account", name="account", methods={"GET"})
     */
    public function account(): Response
    {
        return $this->render('account/index.html.twig', [
            'account' => 'AccountController',
        ]);
    }
}
