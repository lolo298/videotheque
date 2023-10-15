<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\Categorie;
use App\Form\Type\MovieType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
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
    if ($form->isSubmitted() && $form->isValid()) {
      $movie = $form->getData();
      $movieName = $movie->getTitle();
      $movie->setTitle(preg_replace('/-+/', '#dash#', $movie->getTitle()));
      $movieName = preg_replace('/\s+/', '-', $movie->getTitle());
      if (strlen($movieName) < 10) {
        return $this->render('movie_creation.html.twig', [
          'form' => $form,
          'success' => null,
          'error' => 'The title must be at least 10 characters long',
        ]);
      }
      $movieId = $movie->getId();

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

      $id = $movie->getId();


      return $this->redirectToRoute('film', [
        'categorie' => $movie->getCategorie()->getName(),
        'movie' => $movieName,
        'id' => $id,
      ]);
    }


    return $this->render('movie_creation.html.twig', [
      'form' => $form,
      'success' => null,
      'error' => null,
    ]);
  }

  #[Route('/{categorie}', name: 'categorie_films')]
  public function categorieFilms(EntityManagerInterface $entityManager, Request $request, $categorie): Response {
    $limit = 10;
    $page = $request->query->get('page', 1);
    
    
    
    $categorie = $entityManager->getRepository(Categorie::class)->findOneBy(['name' => $categorie]);

    $films = $entityManager->createQueryBuilder()
      ->select('m')
      ->from(Movie::class, 'm')
      ->where('m.categorie = :categorie')
      ->setParameter('categorie', $categorie)
      ->orderBy('m.id', 'DESC')
      ->setFirstResult(($page - 1) * $limit)
      ->setMaxResults($limit)
      ->getQuery();

      // $films = array_slice($films->toArray(), ($page - 1) * $limit , $limit);
      $films = new Paginator($films);
      $total = ceil(count($films) / 10);



    return $this->render('categorie_films.html.twig', [
      'films' => $films,
      'categorie' => $categorie,
      'total' => $total,
      'page' => $page,
    ]);
  }

  #[Route('/{categorie}/{movie<.+>}-{id}', name: 'film')]
  public function film(EntityManagerInterface $entityManager, $categorie, $movie, $id): Response {
    $movie = urldecode($movie);
    $name = preg_replace('/-/', ' ', $movie);
    $film = $entityManager->getRepository(Movie::class)->findOneBy(['id' => $id, 'title' => $name]);

    if (!$film) throw $this->createNotFoundException('The movie does not exist');

    return $this->render('film.html.twig', [
      'film' => $film,
    ]);
  }

  #[Route('/{categorie}/{movie<.+>}-{id}/edit', name: 'film_edit')]
  public function edit(EntityManagerInterface $entityManager, Request $request, $categorie, $movie, $id) {
    $movie = urldecode($movie);
    $name = preg_replace('/-/', ' ', $movie);
    $film = $entityManager->getRepository(Movie::class)->findOneBy(['id' => $id, 'title' => $name]);

    if (!$film) throw $this->createNotFoundException('The movie does not exist');

    $form = $this->createForm(MovieType::class, $film);
    $form->handleRequest($request);

    if ($form->isSubmitted()) {
      $movie = $form->getData();
      $movieName = $movie->getTitle();
      if (!$form->isValid() || strlen($movieName) < 10) {
        return $this->render('movie_creation.html.twig', [
          'form' => $form->createView(),
          'success' => null,
          'error' => 'The title must be at least 10 characters long',
        ]);
      }

      $film = $form->getData();
      $movieName = $film->getTitle();
      $movieCategory = $film->getCategorie()->getName();

      $coverPath = $form->get('cover')->getData();
      if ($coverPath) {
        $fileType = $coverPath->getMimeType();
        $cover = file_get_contents($coverPath);
        $cover = base64_encode($cover);

        $film->setCover($cover);
        $film->setCoverType($fileType);
      }

      $entityManager->persist($film);
      $entityManager->flush();

      return $this->redirectToRoute('film', [
        'categorie' => $movieCategory,
        'movie' => preg_replace('/\s+/', '-', $movieName),
        'id' => $id,
      ]);
    }
    return $this->render('movie_creation.html.twig', [
      'form' => $form->createView(),
      'success' => null,
    ]);
  }

  #[Route('/{categorie}/{movie<.+>}-{id}/delete', name: 'film_delete')]
  public function delete(EntityManagerInterface $entityManager, $categorie, $movie, $id, Request $request) {
    $movie = urldecode($movie);
    $name = preg_replace('/-/', ' ', $movie);
    $film = $entityManager->getRepository(Movie::class)->findOneBy(['id' => $id, 'title' => $name]);

    if (!$film) throw $this->createNotFoundException('The movie does not exist');

    $entityManager->remove($film);
    $entityManager->flush();


    return $this->redirectToRoute('home');
  }

  public function qte(EntityManagerInterface $entityManager) {
    $films_qte = $entityManager->getRepository(Movie::class)->count([]);
    return new Response($films_qte);
  }
}
