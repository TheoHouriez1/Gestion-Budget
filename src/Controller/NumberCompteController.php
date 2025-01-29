<?php

namespace App\Controller;

use App\Entity\CompteBudget;
use App\Form\CompteBudgetType;
use App\Repository\CompteBudgetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/number/compte')]
final class NumberCompteController extends AbstractController
{
    // Afficher les comptes uniquement pour l'utilisateur connecté
    #[Route(name: 'app_number_compte_index', methods: ['GET'])]
    public function index(CompteBudgetRepository $compteBudgetRepository): Response
    {
        $user = $this->getUser(); // Récupère l'utilisateur connecté
        
        // Filtrer les comptes associés à l'utilisateur connecté
        $compteBudgets = $compteBudgetRepository->findBy(['user' => $user]);

        return $this->render('number_compte/index.html.twig', [
            'compte_budgets' => $compteBudgets,
        ]);
    }

    // Créer un nouveau compte et l'associer à l'utilisateur connecté
    #[Route('/new', name: 'app_number_compte_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $compteBudget = new CompteBudget();
        $form = $this->createForm(CompteBudgetType::class, $compteBudget);
        
        $user = $this->getUser(); // Récupère l'utilisateur connecté
        $compteBudget->setUser($user); // Associe l'utilisateur connecté au compte
        
        // Définir le solde initial à 0
        $compteBudget->setSolde(0);
        
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Le nom du compte est récupéré depuis le formulaire
            $compteBudget->setnamecompte($form->get('namecompte')->getData());
    
            $entityManager->persist($compteBudget);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_number_compte_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('number_compte/new.html.twig', [
            'compte_budget' => $compteBudget,
            'form' => $form,
        ]);
    }
    

    // Afficher un compte spécifique uniquement pour l'utilisateur connecté
    #[Route('/{id}', name: 'app_number_compte_show', methods: ['GET'])]
    public function show(CompteBudget $compteBudget): Response
    {
        // Vérifier que le compte appartient bien à l'utilisateur connecté
        if ($compteBudget->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas accéder à ce compte.');
        }

        return $this->render('number_compte/show.html.twig', [
            'compte_budget' => $compteBudget,
        ]);
    }

    // Modifier un compte existant uniquement pour l'utilisateur connecté
    #[Route('/{id}/edit', name: 'app_number_compte_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CompteBudget $compteBudget, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que le compte appartient bien à l'utilisateur connecté
        if ($compteBudget->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier ce compte.');
        }

        $form = $this->createForm(CompteBudgetType::class, $compteBudget);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_number_compte_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('number_compte/edit.html.twig', [
            'compte_budget' => $compteBudget,
            'form' => $form,
        ]);
    }

    // Supprimer un compte uniquement pour l'utilisateur connecté
    #[Route('/{id}', name: 'app_number_compte_delete', methods: ['POST'])]
    public function delete(Request $request, CompteBudget $compteBudget, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que le compte appartient bien à l'utilisateur connecté
        if ($compteBudget->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer ce compte.');
        }

        if ($this->isCsrfTokenValid('delete' . $compteBudget->getId(), $request->get('_token'))) {
            $entityManager->remove($compteBudget);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_number_compte_index', [], Response::HTTP_SEE_OTHER);
    }
}
