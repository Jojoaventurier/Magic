<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\ForumPost;
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
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ForumTopicType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('topicTitle', TextType::class, [
                'attr' => [
                    'class' => 'flex flex-col p-3 m-2 rounded-lg border border-red-700 focus:outline-none focus:ring-2 focus:ring-red-700 focus:border-transparent', // Tailwind CSS classes for styling
                ],
                'label' => 'Titre du sujet :',
            ])
            
            ->add('forumPost', ForumPostType::class, [
                'mapped' => false,
                'label' => 'Forum Post', // The label text for screen readers
                'label_attr' => [
                    'class' => 'sr-only', // Use a class to hide the label visually
                ],
                'attr' => [
                    'class' => 'w-[600px] py-2', // Tailwind CSS classes for styling
                ],
            ])
    
            ->add('Valider', SubmitType::class, [
                'attr' => [
                    'class' => 'bg-red-700 text-white px-4 py-2 rounded-lg hover:bg-red-900 transition duration-200', // Tailwind CSS classes for button styling
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ForumTopic::class,
        ]);
    }
}
