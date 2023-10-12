<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\Categorie;
use App\Form\Type\MovieType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MovieController extends AbstractController {
  #[Route('/create', name: 'nouveau_film')]
  public function new(Request $request, EntityManagerInterface $entityManager): Response {
    $movie  = new Movie();

    $form = $this->createForm(MovieType::class, $movie);

    $form->handleRequest($request);
    if ($form->isSubmitted()) {
      if (!$form->isValid()) {
        return $this->render('movie_creation.html.twig', [
          'form' => $form,
          'success' => null,
        ]);
      }
      $movie = $form->getData();
      $movieName = preg_replace('/\s+/', '-', $movie->getTitle());
      $movieId = $movie->getId();
      //get the file from the form
      $coverPath = $form->get('cover')->getData();
      if ($coverPath) {
        $fileType = $coverPath->getMimeType();
        $cover = file_get_contents($coverPath);
        $cover = base64_encode($cover);

        $movie->setCover($cover);
        $movie->setCoverType($fileType);
      }


      $entityManager->persist($movie);
      $entityManager->flush();


      //refresh the page with a success message
      return $this->render('movie_creation.html.twig', [
        'form' => $form,
        'success' => 'Movie created successfully',
      ]);
    }


    return $this->render('movie_creation.html.twig', [
      'form' => $form,
      'success' => null,
    ]);
  }

  #[Route('/{categorie}', name: 'categorie_films')]
  public function categorieFilms(EntityManagerInterface $entityManager, $categorie): Response {
    $categorie = $entityManager->getRepository(Categorie::class)->findOneBy(['name' => $categorie]);
    $films = $categorie->getMovies();

    return $this->render('categorie_films.html.twig', [
      'films' => $films,
      'categorie' => $categorie,
    ]);
  }

  //allow routes like Back-to-the-future-1-1
  #[Route('/{categorie}/{movie<[\w-]+>}-{id}', name: 'film')]
  public function film(EntityManagerInterface $entityManager, $categorie, $movie, $id): Response {
    $name = preg_replace('/-/', ' ', $movie);
    $film = $entityManager->getRepository(Movie::class)->findOneBy(['id' => $id, 'title' => $name]);

    if (!$film) throw $this->createNotFoundException('The movie does not exist');

    return $this->render('film.html.twig', [
      'film' => $film,
    ]);
  }

  #[Route('/{categorie}/{movie<[\w-]+>}-{id}/edit', name: 'film_edit')]
  public function edit(EntityManagerInterface $entityManager, $categorie, $movie, $id) {
    $name = preg_replace('/-/', ' ', $movie);
    $film = $entityManager->getRepository(Movie::class)->findOneBy(['id' => $id, 'title' => $name]);

    if (!$film) throw $this->createNotFoundException('The movie does not exist');

    $form = $this->createForm(MovieType::class, $film);

    return $this->render('movie_creation.html.twig', [
      'form' => $form->createView(),
      'success' => null,
    ]);
  }
}
