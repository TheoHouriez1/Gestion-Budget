<?php

    namespace App\Controller;

    use App\Entity\Transaction;
    use App\Form\TransactionType;
    use App\Repository\TransactionRepository;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Bridge\Doctrine\Form\Type\EntityType;
    use Symfony\Component\Routing\Attribute\Route;
    use App\Entity\Category;
    
    #[Route('/transaction')]
    final class TransactionController extends AbstractController
    {
        #[Route(name: 'app_transaction_index', methods: ['GET'])]
        public function index(TransactionRepository $transactionRepository): Response
        {
            // Récupérer l'utilisateur connecté
            $user = $this->getUser();
        
            // Récupérer les transactions liées au compte budget de cet utilisateur
            $transactions = $transactionRepository->createQueryBuilder('t')
                ->join('t.Compte', 'c')
                ->where('c.user = :user')
                ->setParameter('user', $user)
                ->getQuery()
                ->getResult();
        
            return $this->render('transaction/index.html.twig', [
                'transactions' => $transactions,
            ]);
        }
        

        #[Route('/new', name: 'app_transaction_new', methods: ['GET', 'POST'])]
        public function new(Request $request, EntityManagerInterface $entityManager): Response
        {
            $transaction = new Transaction();
            $form = $this->createForm(TransactionType::class, $transaction);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // Récupérer le CompteBudget de l'utilisateur connecté
                $user = $this->getUser();
                $compteBudget = $entityManager->getRepository(CompteBudget::class)->findOneBy(['user' => $user]);
 
                if (!$compteBudget) {
                    $this->addFlash('error', 'Aucun compte budget trouvé pour cet utilisateur.');
                    return $this->redirectToRoute('app_transaction_index');
                }

                // Associer le CompteBudget à la transaction
                $transaction->setCompte($compteBudget);

                // Mettre à jour le solde en fonction du type de transaction
                if ($transaction->getType() === 'revenu') {
                    $compteBudget->setSolde($compteBudget->getSolde() + $transaction->getMontant());
                } elseif ($transaction->getType() === 'dépense') {
                    $compteBudget->setSolde($compteBudget->getSolde() - $transaction->getMontant());
                }

                $entityManager->persist($transaction);
                $entityManager->persist($compteBudget);
                $entityManager->flush();

                $this->addFlash('success', 'Transaction ajoutée avec succès.');
                return $this->redirectToRoute('app_transaction_index');
            }

            return $this->render('transaction/new.html.twig', [
                'transaction' => $transaction,
                'form' => $form,
            ]);
        }

        #[Route('/{id}', name: 'app_transaction_show', methods: ['GET'])]
        public function show(Transaction $transaction): Response
        {
            // Vérifier si la transaction appartient à l'utilisateur connecté
            if ($transaction->getCompte()->getUser() !== $this->getUser()) {
                throw $this->createAccessDeniedException('Vous ne pouvez pas accéder à cette transaction.');
            }

            return $this->render('transaction/show.html.twig', [
                'transaction' => $transaction,
            ]);
        }

        #[Route('/{id}/edit', name: 'app_transaction_edit', methods: ['GET', 'POST'])]
        public function edit(Request $request, Transaction $transaction, EntityManagerInterface $entityManager): Response
        {
            // Vérifier si la transaction appartient à l'utilisateur connecté
            if ($transaction->getCompte()->getUser() !== $this->getUser()) {
                throw $this->createAccessDeniedException('Vous ne pouvez pas modifier cette transaction.');
            }

            $form = $this->createForm(TransactionType::class, $transaction);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->flush();

                $this->addFlash('success', 'Transaction mise à jour avec succès.');
                return $this->redirectToRoute('app_transaction_index');
            }

            return $this->render('transaction/edit.html.twig', [
                'transaction' => $transaction,
                'form' => $form,
            ]);
        }

        #[Route('/{id}', name: 'app_transaction_delete', methods: ['POST'])]
        public function delete(Request $request, Transaction $transaction, EntityManagerInterface $entityManager): Response
        {
            // Vérifier si la transaction appartient à l'utilisateur connecté
            if ($transaction->getCompte()->getUser() !== $this->getUser()) {
                throw $this->createAccessDeniedException('Vous ne pouvez pas supprimer cette transaction.');
            }

            if ($this->isCsrfTokenValid('delete' . $transaction->getId(), $request->request->get('_token'))) {
                // Mettre à jour le solde après suppression
                $compteBudget = $transaction->getCompte();
                if ($transaction->getType() === 'revenu') {
                    $compteBudget->setSolde($compteBudget->getSolde() - $transaction->getMontant());
                } elseif ($transaction->getType() === 'dépense') {
                    $compteBudget->setSolde($compteBudget->getSolde() + $transaction->getMontant());
                }

                $entityManager->remove($transaction);
                $entityManager->persist($compteBudget);
                $entityManager->flush();

                $this->addFlash('success', 'Transaction supprimée avec succès.');
            }

            return $this->redirectToRoute('app_transaction_index');
        }
    }
