<?php
// src/Controller/HomeController.php
namespace App\Controller;

use App\Entity\CompteBudget;
use App\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
#[Route('/', name: 'app_home')]
// src/Controller/HomeController.php
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

    // Initialiser le tableau de transactions
    $transactions = [];

    // Récupérer toutes les transactions associées aux comptes budgétaires
    foreach ($compteBudgets as $compte) {
        // Ajouter les transactions du compte courant
        foreach ($compte->getTransactions() as $transaction) {
            // Ajouter le namecompte de CompteBudget à chaque transaction
            $transactionData = [
                'transaction' => $transaction,
                'namecompte' => $compte->getNamecompte(), // Récupérer namecompte via la relation
            ];
            $transactions[] = $transactionData;
        }
    }

    // Passer les données à Twig, y compris le prénom et le nom
    return $this->render('home/index.html.twig', [
        'user' => $user,
        'prenom' => $user->getPrenom(),
        'nom' => $user->getNom(),
        'compteBudgets' => $compteBudgets,
        'transactions' => $transactions,
    ]);
    }

}

