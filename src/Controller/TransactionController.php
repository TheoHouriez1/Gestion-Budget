<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Form\TransactionType;
use App\Repository\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/transaction')]
final class TransactionController extends AbstractController
{
    // Afficher les transactions uniquement pour l'utilisateur connecté
    #[Route(name: 'app_transaction_index', methods: ['GET'])]
    public function index(TransactionRepository $transactionRepository): Response
    {
        $user = $this->getUser(); // Récupère l'utilisateur connecté
        $compte = $user->getCompteBudgets()->first(); // Récupère le compte associé à l'utilisateur

        // Récupère les transactions associées au compte de l'utilisateur
        $transactions = $transactionRepository->findBy(['Compte' => $compte]);

        return $this->render('transaction/index.html.twig', [
            'transactions' => $transactions,
        ]);
    }

    // Créer une nouvelle transaction et associer le compte de l'utilisateur
    #[Route('/new', name: 'app_transaction_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $transaction = new Transaction();
        $form = $this->createForm(TransactionType::class, $transaction);
    
        // Assurez-vous que le compte de l'utilisateur connecté est automatiquement ajouté à la transaction
        $user = $this->getUser();
        $compte = $user->getCompteBudgets()->first(); // Récupère le premier compte de l'utilisateur
    
        if ($compte) {
            $transaction->setCompte($compte); // Associe le compte à la transaction
        }
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Si le type est "Revenu", on ajoute au solde
            if ($transaction->getType() === 'revenu') {
                $compte->setSolde($compte->getSolde() + $transaction->getMontant());
            }
    
            // Si le type est "Dépense", on soustrait du solde
            if ($transaction->getType() === 'depense') {
                $compte->setSolde($compte->getSolde() - $transaction->getMontant());
            }
    
            // Sauvegarde les modifications dans la base de données
            $entityManager->persist($transaction);
            $entityManager->flush(); // Met à jour la base de données
    
            // Redirige vers la page d'index
            return $this->redirectToRoute('app_transaction_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('transaction/new.html.twig', [
            'transaction' => $transaction,
            'form' => $form,
        ]);
    }

    // Afficher une transaction spécifique
    #[Route('/{id}', name: 'app_transaction_show', methods: ['GET'])]
    public function show(Transaction $transaction): Response
    {
        // Assurer que l'utilisateur est bien le propriétaire de la transaction
        if ($transaction->getCompte()->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas accéder à cette transaction.');
        }

        return $this->render('transaction/show.html.twig', [
            'transaction' => $transaction,
        ]);
    }

    // Modifier une transaction existante
    #[Route('/{id}/edit', name: 'app_transaction_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Transaction $transaction, EntityManagerInterface $entityManager): Response
    {
        // Assurer que l'utilisateur est bien le propriétaire de la transaction
        if ($transaction->getCompte()->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier cette transaction.');
        }

        $form = $this->createForm(TransactionType::class, $transaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_transaction_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('transaction/edit.html.twig', [
            'transaction' => $transaction,
            'form' => $form->createView(),
        ]);
    }

    // Supprimer une transaction
    #[Route('/{id}', name: 'app_transaction_delete', methods: ['POST'])]
    public function delete(Request $request, Transaction $transaction, EntityManagerInterface $entityManager): Response
    {
        // Assurer que l'utilisateur est bien le propriétaire de la transaction
        if ($transaction->getCompte()->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer cette transaction.');
        }

        if ($this->isCsrfTokenValid('delete' . $transaction->getId(), $request->get('_token'))) {
            $entityManager->remove($transaction);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_transaction_index', [], Response::HTTP_SEE_OTHER);
    }
}
