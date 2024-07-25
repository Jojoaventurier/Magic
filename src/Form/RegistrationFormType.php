<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
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
                'label' => 'Nom d\'utilisateur'
            ]) 
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'Accepter les conditions d\'utilisation',
                'constraints' => [ // l'utilisateur est obligé d'accepter les conditions pour s'inscrire sinon apparation du message ci-dessous
                    new IsTrue([
                        'message' => 'Veuillez accepter les conditions d\'utilisation du site pour vous inscrire.',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [ // utilisation de la classe Repeatedtype pour demander la confirmation du mot de passe
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'type' => PasswordType::class, // utilisation de la classe PasswordType pour utiliser les filtres de sécurité implémentés par symfony 
                'attr' => ['autocomplete' => 'new-password'],
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options' => ['label' => 'Mot de passe'], // label de la case du formulaire
                'second_options' => ['label' => 'Confirmez le mot de passe'], // label de la case du formulaire
                'constraints' => [
                    new NotBlank([ // contrainte du validateur de symfony, il faut saisir une valeur
                        'message' => 'Veuillez saisir un mot de passe',
                    ]),
                    new Length([ 
                        'min' => 8, // utilisation de la classe EmailType pour filtrer l'adresse mail saisie par l'utilisateur (XSS)
                        'minMessage' => 'Votre mot de passe doit avoir un minimum de 8 characters', // message affiché si l'utilisateur tente de s'inscrire sans remplir la condition
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
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
