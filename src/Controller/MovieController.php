<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class MovieController extends AbstractController
{
  #[Route('/create', name: 'nouveau film')]
    public function index(): Response
    {
      $form = $this->createFormBuilder(null, ['csrf_protection' => false])
      ->add('title', TextType::class)
      ->add('description', TextType::class)
      ->add('categorie', TextType::class)
      ->add('path', TextType::class)
      ->getForm();

      return $this->render('movie_creation.html.twig', [
        'form' => $form
      ]);
    }
}