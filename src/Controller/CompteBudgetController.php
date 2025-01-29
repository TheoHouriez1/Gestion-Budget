<?php
// src/Controller/CompteBudgetController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CompteBudgetController extends AbstractController
{
    #[Route('/compte/budget', name: 'app_compte_budget')]
    public function index(): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Vérifier si l'utilisateur est connecté
        if (!$user) {
            // Rediriger si l'utilisateur n'est pas connecté
            return $this->redirectToRoute('app_login');
        }

        // Passer les informations de l'utilisateur à la vue
        return $this->render('compte_budget/index.html.twig', [
            'controller_name' => 'CompteBudgetController',
            'user_first_name' => $user->getPrenom(),
            'user_last_name' => $user->getNom(),
            'user_email' => $user->getEmail(),
        ]);
    }
}
