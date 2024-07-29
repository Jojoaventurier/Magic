<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\ForumTopic;
use App\Form\ForumPostType;
use App\Entity\ForumSubCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ForumTopicType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('topicTitle', TextType::class, [ // utilisation de TextareaType pour filtrer les données saisies
                'attr' => [
                    'style' => 'width: 700px; font-size: 0.8rem' // personnalisation de l'affichage de la case de texte à remplir par l'utilisateur
                ],
                'label' => 'Titre du sujet :'])
            ->add('textContent', ForumPostType::class)
            ->add('Valider', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ForumTopic::class,
        ]);
    }
}
