<?php
// src/Controller/HomeController.php
namespace App\Controller;

use App\Entity\CompteBudget;
use App\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security; // Utiliser le bon namespace
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager, Security $security): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $security->getUser();
    
        // Vérifier si l'utilisateur est connecté
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
    
        // Récupérer tous les comptes budgétaires de l'utilisateur connecté
        $compteBudgets = $entityManager->getRepository(CompteBudget::class)->findBy(['user' => $user]);
    
        // Récupérer toutes les transactions associées aux comptes budgétaires
        $transactions = [];
        foreach ($compteBudgets as $compte) {
            $transactions = array_merge($transactions, $compte->getTransactions()->toArray());
        }
    
        // Passer les données à Twig
        return $this->render('home/index.html.twig', [
            'user' => $user,
            'compteBudgets' => $compteBudgets,
            'transactions' => $transactions,
        ]);
    }
}
