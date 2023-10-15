<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\Categorie;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;


class HomeController extends AbstractController {
    #[Route('/', name: 'home')]
    public function index(EntityManagerInterface $entityManager, Request $request): Response {

        $page = $request->query->get('page', 1);
        $total = ceil($entityManager->getRepository(Movie::class)->count([]) / 10);
        $limit = 10;

        $query = $entityManager->createQueryBuilder()
            ->select('m')
            ->from(Movie::class, 'm')
            ->orderBy('m.id', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery();

        $paginator = new Paginator($query);

        return $this->render('index.html.twig', [
            'films' => $paginator,
            'total' => $total,
            'page' => $page,
        ]);
    }

    public function categories(EntityManagerInterface $entityManager, $type, $value = '') {
        return $this->render('categories_list.html.twig', [
            'categories' => $entityManager->getRepository(Categorie::class)->findAll(),
            'type' => $type,
            'value' => $value,
        ]);
    }

    #[Route('/search', name: 'search')]
    public function search(EntityManagerInterface $entityManager, Request $request) {
        $search = $request->query->get('q');
        $cat = $request->query->get('t');
        $date = $request->query->get('d');
        $query = $entityManager->createQueryBuilder()
            ->select('m')
            ->from(Movie::class, 'm')
            ->innerJoin('m.categorie', 'c');
        
            if($search) {
                $query->andWhere('m.title LIKE :search')
                ->setParameter('search', "%$search%");
            }

            if($cat && $cat != 'All') {
                $query->andWhere('c.name = :cat')
                ->setParameter('cat', strtolower($cat));
            }

            if($date) {
                $query->andWhere('m.date_ajout LIKE :date')
                ->setParameter('date', "%$date%");
            }



        $films = $query->getQuery()
            ->getResult();



        return $this->render('search.html.twig', [
            'films' => $films,
            'search' => $search,
            'cat' => $cat,
            'date' => $date,
        ]);
    }
}
