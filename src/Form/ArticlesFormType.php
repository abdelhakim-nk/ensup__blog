<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ArticlesFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, ['required' => true])
            ->add('contenu', TextareaType::class, ['required' => true])
            ->add('resume', TextType::class, ['required' => false])
            ->add('date_publication')
            ->add('date_creation')
            ->add('date_modification')
            ->add('slug', TextType::class, ['required' => true])
            ->add('auteur', EntityType::class, [
                'class' => 'App\Entity\Auteurs',
                'choice_label' => 'nom',
            ])
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}