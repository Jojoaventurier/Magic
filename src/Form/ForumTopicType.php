<?php

namespace App\Form;

use App\Entity\ForumSubCategory;
use App\Entity\ForumTopic;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ForumTopicType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('topicTitle')
            ->add('creationDate', null, [
                'widget' => 'single_text',
            ])
            ->add('editDate', null, [
                'widget' => 'single_text',
            ])
            ->add('closed')
            ->add('forumSubCategory', EntityType::class, [
                'class' => ForumSubCategory::class,
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
            'data_class' => ForumTopic::class,
        ]);
    }
}
