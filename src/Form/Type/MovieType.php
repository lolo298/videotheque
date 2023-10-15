<?php

namespace App\Form\Type;

use App\Entity\Movie;
use App\Entity\Categorie;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;

class MovieType extends AbstractType {
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $url = $_SERVER['REQUEST_URI'];
    $url = explode('/', $url);
    $mode = array_pop($url);
    $editing = $mode == 'edit';

    $builder
    //Set the min length of the title to 10 characters
      ->add('title', TextType::class, [
        'attr' => ['minlength' => 10],
      ])
      ->add('description', TextareaType::class, [
        'attr' => ['rows' => 10],
      ])
      ->add('categorie', EntityType::class, [
        'class' => Categorie::class,
        'choice_label' => 'name',
      ])
      ->add('cover', FileType::class, [
        'label' => 'Cover',
        'required' => false,
        'attr' => ['accept' => 'image/*'],
        'constraints' => [
          new File([
            'maxSize' => '1024k',
            'mimeTypes' => [
              'image/*',
            ],
            'mimeTypesMessage' => 'Please upload a valid image',
          ])
        ],
        'data_class' => null,
      ])
      ->add('save', SubmitType::class, ['label' => $editing ? 'Update Movie' : 'Create Movie'])
    ;
  }

  public function configureOptions(OptionsResolver $resolver) {
    $resolver->setDefaults([
      'data_class' => Movie::class,
    ]);
  }
}