<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class SecurityController extends AbstractController
{

    //Connexion de l'utilisateur
    /**
     * @Route("/api/login_check", name="api_login", methods={"POST"})
     */
    public function login()
    {
        /*$user = $this->getUser();

        return $this->json([
            'email' => $user->getUsername(),
            'roles' => $user->getRoles(),
        ]);*/

        return $this->json("Ici normalement renvoi un token", 200);
    }
}
