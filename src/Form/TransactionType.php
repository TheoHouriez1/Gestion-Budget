<?php

namespace App\Form;

use App\Entity\Transaction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
                    'Revenu' => 'Revenu',
                    'Dépense' => 'Dépense',
                ],
                'label' => 'Type de transaction',
                'expanded' => false, // dropdown
                'multiple' => false, // single choice
                'placeholder' => 'Sélectionnez un type', // Option par défaut
            ])
            ->add('category', TextType::class, [
                'label' => 'Catégorie',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
            ])
            ->add('montant', NumberType::class, [
                'label' => 'Montant',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
        ]);
    }
}
