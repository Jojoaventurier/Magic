<?php

namespace App\Form;

use App\Entity\ForumPost;
use App\Entity\ForumTopic;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ForumPostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('textContent')
            ->add('creationDate', null, [
                'widget' => 'single_text',
            ])
            ->add('editDate', null, [
                'widget' => 'single_text',
            ])
            ->add('forumTopic', EntityType::class, [
                'class' => ForumTopic::class,
                'choice_label' => 'id',
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ForumPost::class,
        ]);
    }
}
