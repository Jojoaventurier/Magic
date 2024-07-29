<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\ForumPost;
use App\Entity\ForumTopic;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ForumPostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

        ->add('textContent', TextareaType::class, [ // utilisation de TextareaType pour filtrer les données saisies
            'attr' => [
                'style' => 'height: 50px; width: 700px; font-size: 0.8rem' // personnalisation de l'affichage de la case de texte à remplir par l'utilisateur
            ],
            'label' => 'Votre message :'])
        // ->add('Valider', SubmitType::class);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ForumPost::class,
        ]);
    }
}
