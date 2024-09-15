<?php

namespace App\Form;

use App\Entity\Deck;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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
            ->add('discordUsername', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Regex([
                        'pattern' => '/^discord\.gg\/.*$/',
                        'message' => 'Please provide a valid Discord invite link starting with "discord.gg/".',
                    ])
                ],
            ])
            ->add('youtubeChannel', TextType::class, [
                'required' => false,  
                'constraints' => [
                    new Regex([
                        'pattern' => '/^youtube\.com\/.*$/',
                        'message' => 'Please provide a valid YouTube channel link starting with "youtube.com/".',
                    ])
                ],
            ])
            ->add('twitchUsername', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Regex([
                        'pattern' => '/^twitch\.tv\/.*$/',
                        'message' => 'Please provide a valid Twitch link starting with "twitch.tv/".',
                    ])
                ],
            ])
            ->add('allowStream', CheckboxType::class, [
                'required' => false,
                'label' => 'Afficher le live stream Twitch sur la page de profil',
                'attr' => [
                    'class' => 'form-check-input', // Add custom styling class if necessary (e.g., for Bootstrap/Tailwind)
                ],
            ])
            ->add('globalChat', CheckboxType::class, [
                'required' => false,
                'label' => 'Activer le chat sur tout le site (oldschool version)',
                'attr' => [
                    'class' => 'form-check-input', // Add custom styling class if necessary
                ],
            ])
            ->add('modernChat', CheckboxType::class, [
                'required' => false,
                'label' => 'Version moderne :',
                'attr' => [
                    'class' => 'form-check-input', // Add custom styling class if necessary
                ],
            ])
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
