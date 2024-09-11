<?php

namespace App\Form;

use App\Entity\Deck;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder


            ->add('userName', TextType::class)
            ->add('description', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 5,  // Adjust the number of rows to make it taller
                    'class' => 'w-full px-4 py-2 border border-red-700 text-sm rounded focus:ring-2 focus:outline-none outline-none hover:bg-gray-100 hover:shadow-md shadow duration-300'
                ]
            ])
            ->add('discordUsername', TextType::class, ['required' => false])
            ->add('youtubeChannel', TextType::class, ['required' => false])
            ->add('twitchUsername', TextType::class, ['required' => false])
            ->add('submit', SubmitType::class)
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
