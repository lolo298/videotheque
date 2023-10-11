<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
  #[Route('/', name: 'home')]
    public function index(): Response
    {
        $films = [
            // [
            //     'id' => '1',
            //     'title' => 'The Godfather',
            //     'description' => 'The aging patriarch of an organized crime dynasty transfers control of his clandestine empire to his reluctant son.',
            //     'categorie' => 'Crime',
            //     'path' => 'crime/the-godfather-1'
            // ],
            // [
            //     'id' => '2',
            //     'title' => 'The Shawshank Redemption',
            //     'description' => 'The aging patriarch of an organized crime dynasty transfers control of his clandestine empire to his reluctant son.',
            //     'categorie' => 'Crime',
            //     'path' => 'crime/the-shawshank-redemption-2'
            // ]
        ];
        return $this->render('index.html.twig', [
            'films' => $films
        ]);
    }
}