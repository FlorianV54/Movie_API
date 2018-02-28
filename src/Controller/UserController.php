<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Movie;
use App\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


/**
 * @Route("/users")
 */
class UserController extends Controller
{
    /**
    * Enregistre un nouvel utilisateur
    * @Route("/register", name="user_registration")
    * @Method({"POST"})
    */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, ValidatorInterface $validator)
    {
        $user = new User();

        // Appel du FormBuilder
        $form = $this->createForm(UserType::class, $user);
        // Récupération et soumission des données
        $data = json_decode($request->getContent(), true);
        $form->submit($data);

        // Si les données sont valides
        if ($form->isSubmitted() && $form->isValid()) {
            // Encodage du mot de passe
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            // Enregistrement du nouvel utilisateur en base de données
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // Renvoi de son ID
            return new JsonResponse([
              'id' => $user->getId()
            ]);
        // Si les données soumises ne sont pas valides
        } elseif (! $form->isValid()) {
            // Récupération des erreurs (via le validator)
            $errors = $validator->validate($user);

            // Si erreur(s) - renvoi de celles-ci en JSON
            if (count($errors) > 0) {

                // __toString method on the $errors variable is a                 ConstraintViolationList object.
                // Converts the violation into a nice string for a better debugging purposes.
                $errorsString = (string) $errors;

                return new JsonResponse([
                  'Errors' => $errorsString
                ], 400);
            }
        }
    }

    /**
    * Enregistre le choix d'un film d'un utilisateur
    * @Route("/{user_id}/movies", name="movie_store")
    * @Method({"POST"})
    */
    public function storeMovie(Request $request, $user_id)
    {
        // API Key OMDb
        $apiKey = '199f162b';

        // Récupération des données depuis la requête
        $data = json_decode($request->getContent(), true);
        // Vérification si présence ou non du "title"
        if (!isset($data['title']) || empty($data['title'])) {
            return new JsonResponse([
              "error" => "Missing title attribute."
            ], 422);
        }

        // Vérification que le titre contienne obligatoirement le mot "Pirate(s)"
        // (!!! avec ou sans "s" à la fin - car les titres des films OMDb sont en Anglais ex. "Pirates of the Caribbean: The Curse of the Black Pearl").
        $pirateWord = strpos(strtolower($data['title']), 'pirate');
        $piratesWord = strpos(strtolower($data['title']), 'pirates');

        if ($pirateWord === false && $piratesWord === false) {
          return new JsonResponse([
            "error" => "The title must contains the word Pirate(s)."
          ], 400);
        }

        // Construction de l'url OMDb
        $url = file_get_contents("http://www.omdbapi.com/?apiKey=".$apiKey."&type=movie&t=".urlencode($data['title']));

        // Récupération des données provenant de la base de films OMDb
        $omdbData = json_decode($url, true);

        // Vérification si film (titre choisi) existe dans la base OMDb
        if (isset($omdbData['Error'])) {
          return new JsonResponse([
            "error" => $omdbData['Error']
          ], 404);
        }

        // Si le film existe dans la base OMDb, récupération de son titre - affiche et OMDbId (imdbID)
        $movieTitle = $omdbData['Title'];
        $moviePoster = $omdbData['Poster'];
        $movieOmdbId = $omdbData['imdbID'];

        /**
         * Deux vérifications à faire avant l'enregistrement du film
         */
        // 1- que l'utilisateur n'a pas déjà enregistré plus de 3 films
        $movieRepository = $this->getDoctrine()->getRepository(Movie::class);
        $userRepository = $this->getDoctrine()->getRepository(User::class);

        $user = $userRepository->find($user_id); // récupère l'utilisateur
        $userMovies = $movieRepository->findByUser($user); // récupère ses films

        $movies_nbr = count($userMovies);
        if ($movies_nbr >= 3) {
          return new JsonResponse([
            "error" => "You can choose only 3 movies max."
          ], 400);
        }

        // 2- que l'utilisateur ne poste pas 2 ou 3 fois le même film.
        foreach ($userMovies as $movie) {
            // Si ce qu'il a déjà choisi comme films correspond à sonj nouveau choix
            if ($movie->getOmdbId() == $movieOmdbId) {
                return new JsonResponse([
                  "error" => "You cannot choose the same movie many times."
                ], 400);
            }
        }


        // Enregistrement du film en base de données (titre - affiche - OMDbId)
        $movie = new Movie();

        $movie->setTitle($movieTitle);
        $movie->setPoster($moviePoster);
        $movie->setOmdbId($movieOmdbId);
        $movie->setUser($user); // enregistrement de son user_id

        $em = $this->getDoctrine()->getManager();
        $em->persist($movie);
        $em->flush();

        // Renvoi des données enregistrées en JSON (titre + affiche)
        return new JsonResponse($movie->toArray(), 201);
     }

    /**
     * Supprime le choix d'un film d'un utilisateur
     * @Route("/{user_id}/movies/{movie_id}", name="movie_delete")
     * @Method({"DELETE"})
     */
    public function deleteMovie($user_id, $movie_id)
    {
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $user = $userRepository->find($user_id);

        $movieRepository = $this->getDoctrine()->getRepository(Movie::class);
        $movie = $movieRepository->find($movie_id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($movie);
        $em->flush();

        return new JsonResponse(NULL);
    }

    /**
     * Liste les choix de film d'un utilisateur
     * @Route("/{user_id}/movies", name="movies_list")
     * @Method({"GET"})
     */
    public function getUserMovies($user_id)
    {
        $movieRepository = $this->getDoctrine()->getRepository(Movie::class);
        $movies = $movieRepository->findByUser($user_id);

        $response = [];
        foreach ($movies as $movie) {
            $response[] = $movie->toArray();
        }

        return new JsonResponse($response);
    }
}
