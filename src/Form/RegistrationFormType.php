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
                'constraints' => [ // l'utilisateur est obligé d'accepter les conditions pour s'inscrire sinon apparation du message ci-dessous
                    new IsTrue([
                        'message' => 'Veuillez accepter les conditions d\'utilisation du site pour vous inscrire.',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [ // utilisation de la classe Repeatedtype pour demander la confirmation du mot de passe
                'mapped' => false,
                'type' => PasswordType::class, // utilisation de la classe PasswordType pour utiliser les filtres de sécurité implémentés par symfony 
                'invalid_message' => 'Les mots de passes doivent être identiques', // message si le repeated password n'est pas confirmé
                'attr' => ['autocomplete' => 'new-password'],
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options' => ['label' => 'Mot de passe'], // label affiché de la case du formulaire
                'second_options' => ['label' => 'Confirmez le mot de passe'], // label affiché de la case du formulaire
                'constraints' => [
                    new NotBlank([ // contrainte du validateur de symfony, il faut saisir une valeur
                        'message' => 'Veuillez saisir un mot de passe',
                    ]),
                    new Regex([ // contrainte du validateur de symfony, le mot de passe doit contenir au minimum 1é caractères, une minuscule, une majuscule, un chiffre et un caractère spécial
                        'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{12,}$/',
                        'message' => 'Votre mot de passe doit avoir une longueur minimale de 12 caractères, contenir aux moins une minuscule, une majuscule, un chiffre et un caractère spécial',
                        ])
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
