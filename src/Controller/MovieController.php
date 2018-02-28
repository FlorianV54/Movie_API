<?php

namespace App\Controller;

use App\Entity\Movie;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/movies")
 */
class MovieController extends Controller
{
    /**
     * Liste les utilisateurs ayant choisi un film
     * @Route("/{omdb_id}/users", name="users_list")
     * @Method({"GET"})
     */
    public function getUsersList($omdb_id)
    {
        $movieRepository = $this->getDoctrine()->getRepository(Movie::class);
        $movies = $movieRepository->findBy([
            'omdb_id' => $omdb_id
        ]);

        $response = [];
        foreach ($movies as $movie) {
            $response[] = $movie->getUser()->getUsername();
        }

        return new JsonResponse($response);
    }

    /**
     * Retourne le meilleur film selon l'ensemble des utilisateurs
     * @Route("/best", name="movie_best")
     * @Method({"GET"})
     */
    public function bestMovie()
    {
        $movieRepository = $this->getDoctrine()->getRepository(Movie::class);
        // Voir la query dans le MovieRepository
        $movie = $movieRepository->findBestMovie();

        return new JsonResponse($movie);
    }
}
