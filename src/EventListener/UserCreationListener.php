<?php

// src/EventListener/UserCreationListener.php
namespace App\EventListener;

use App\Entity\User;
use App\Entity\CompteBudget;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;

class UserCreationListener
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function postPersist(User $user, LifecycleEventArgs $args): void
    {
        // Log pour vérifier si cette méthode est bien appelée
        $logger = $this->entityManager->getRepository(User::class)
            ->getEntityManager()
            ->getRepository(\Monolog\Logger::class)
            ->info('User Created: ' . $user->getId());

        // Vérifier si l'utilisateur est bien créé
        $compte = new CompteBudget();
        $compte->setUser($user);
        $compte->setSolde(0);  // Solde initial

        // Persist le compte
        $this->entityManager->persist($compte);
        $this->entityManager->flush();
    }
}
    