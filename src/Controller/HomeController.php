<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\Categorie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
  #[Route('/', name: 'home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $films = $entityManager->getRepository(Movie::class)->findBy([], ['id' => 'DESC'], 6);


        return $this->render('index.html.twig', [
            'films' => $films,
        ]);
    }

    public function categories(EntityManagerInterface $entityManager) {
        return $this->render('categories_list.html.twig', [
            'categories' => $categorie = $entityManager->getRepository(Categorie::class)->findAll()
        ]);
    }
}