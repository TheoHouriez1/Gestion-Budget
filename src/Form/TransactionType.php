<?php

// src/Form/TransactionType.php

namespace App\Form;

use App\Entity\Transaction;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransactionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Revenu' => 'revenu',
                    'Dépense' => 'depense',
                ],
                'label' => 'Type de transaction',
                'expanded' => false, // false pour un menu déroulant
                'multiple' => false, // on veut une seule option sélectionnée
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'nom',
                'label' => 'Catégorie',
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
            ])
            ->add('montant', NumberType::class, [
                'label' => 'Montant',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
        ]);
    }
}
