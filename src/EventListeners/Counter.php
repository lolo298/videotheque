<?php
namespace App\EventListeners;

use App\Entity\Compteur;
use App\Entity\Movie;
use Doctrine\ORM\Event\PostFlushEventArgs;

class Counter
{
    public function postFlush(Movie $movie, PostFlushEventArgs $event): void
    {
        $entityManager = $event->getObjectManager();
        $counter = $entityManager->getRepository(Compteur::class)->findFirst();
        $films_qte = $entityManager->getRepository(Movie::class)->count([]);
        $counter->setFilmsQte($films_qte);
        

        $entityManager->flush();
    }
}