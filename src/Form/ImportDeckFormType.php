<?php

namespace App\Form;

use App\Entity\Deck;
use App\Entity\User;
use App\Entity\Format;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ImportDeckFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('deckName', TextType::class, ['label' => 'Veuillez saisir le nom du deck :'] )
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Public' => true,
                    'Privé' => false
                ],
                'label' => 'Visibilité du deck :'
            ])
            ->add('format', EntityType::class, [
                'class' => Format::class,
                'choice_label' => 'formatName',
                'label' => 'Choix du format :'
            ])
            ->add('deckList', TextareaType::class, [
                'label' => 'Deck List',
                'attr' => [
                    'rows' => 10,
                    'cols' => 50,
                    'placeholder' => "Format: '1 Elesh Norn, Mother of Machines'"
                ]
            ])
            ->add('importer', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Deck::class,
        ]);
    }
}
