<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [    // utilisation de la classe EmailType pour filtrer l'adresse mail saisie par l'utilisateur (XSS)
                'label' => 'Adresse email'   // nom affiché de la case du formulaire
            ]) 
            ->add('userName', TextType::class, [    // utilisation de la classe TextType pour filtrer le pseudo saisi par l'utilisateur (XSS)
                'label' => 'Nom d\'utilisateur',
                'constraints' => [
                    new Length([ 
                        'min' => 4, 
                        'minMessage' => 'Votre nom d\' utilisateur doit avoir un minimum de 4 caractères et un maximum de 12', // message affiché si l'utilisateur tente de s'inscrire sans remplir la condition
                        'maxMessage' => 'Votre nom d\' utilisateur doit avoir un minimum de 4 caractères et un maximum de 12',
                        'max' => 12,
                    ])
                ]
            ]) 
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'J\'ai lu et j\'accepte les <a href="#">conditions</a>',
                'label_attr' => ['class' => 'sr-only'], // Masque visuellement l'étiquette
                'constraints' => [ // Oblige l'utilisateur à accepter les conditions
                    new IsTrue([
                        'message' => 'Veuillez accepter les conditions d\'utilisation du site pour vous inscrire.',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'mapped' => false,
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent être identiques',
                'attr' => ['autocomplete' => 'new-password'],
                'options' => [
                    'attr' => [
                        'class' => 'password-field w-full bg-transparent placeholder-gray-400 px-4 py-2 border rounded-lg focus:outline focus:outline-red-800',
                    ],
                ],
                'required' => true,
                'first_options' => [
                    'label' => 'Mot de passe',
                ],
                'second_options' => [
                    'label' => 'Confirmez le mot de passe',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un mot de passe',
                    ]),
                    new Regex([
                        'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{12,}$/',
                        'message' => 'Votre mot de passe doit avoir une longueur minimale de 12 caractères, contenir au moins une minuscule, une majuscule, un chiffre et un caractère spécial',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
