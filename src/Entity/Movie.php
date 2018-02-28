<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MovieRepository")
 */
class Movie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @ORM\Column(type="string")
     */
    private $poster;

    /**
     * @ORM\Column(type="string")
     */
    private $omdb_id;

    /**
     * Relation entre l'entity "Movie" et "User"
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="movies")
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;


    public function toArray()
    {
        return [
            'title' => $this->title,
            'poster' => $this->poster,
        ];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getPoster()
    {
        return $this->poster;
    }

    public function setPoster($poster)
    {
        $this->poster = $poster;
    }

    public function getOmdbId()
    {
        return $this->omdb_id;
    }

    public function setOmdbId($omdb_id)
    {
        $this->omdb_id = $omdb_id;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }
}
