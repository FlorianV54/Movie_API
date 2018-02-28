<?php

namespace App\Repository;

use App\Entity\Movie;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class MovieRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Movie::class);
    }


    /**
     * Requête pour touver le meilleur film de l'ensemble des utilisateurs
     */
    public function findBestMovie()
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT title, poster, COUNT(omdb_id) as m_count
            FROM movie
            GROUP BY omdb_id
            ORDER BY m_count DESC LIMIT 1
            ';
        $query = $conn->prepare($sql);
        $query->execute();

        return $query->fetchAll();
    }
}
