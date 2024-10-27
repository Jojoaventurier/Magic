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

        ->add('textContent', TextareaType::class, [
            'attr' => [
                'class' => 'w-full flex flex-col p-3 m-2 rounded-lg border border-red-700 focus:outline-none focus:ring-2 focus:ring-red-700 focus:border-transparent' // Customization of the textarea appearance
            ],
            'label' => 'Votre message :',
            'label_attr' => [
                'class' => 'text-white mb-2' // Adding white text color and margin-bottom for spacing
            ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ForumPost::class,
        ]);
    }
}
