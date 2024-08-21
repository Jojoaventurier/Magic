<?php

namespace App\Form;

use App\Entity\Deck;
use App\Entity\User;
use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('textContent', TextareaType::class, [
            'attr' => ['rows' => 4],
            'label' => 'Commentaire'
        ])
        ->add('submit', SubmitType::class, [
            'attr' => ['class' => 'bg-red-800 text-white px-6 py-2 rounded-lg hover:bg-red-900 transition duration-300'],
            'label' => 'Publier'
        ]);
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
