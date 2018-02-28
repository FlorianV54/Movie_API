<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields="email", message="Email already taken")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $username;

    /**
    * @ORM\Column(type="string")
    * @Assert\NotBlank()
    */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Email()
     * @Assert\NotBlank()
     */
    private $email;

    /**
     * @ORM\Column(type="date")
     * @Assert\Date()
     * @Assert\NotBlank(message = "The date format is invalid. Please use YYYY-MM-DD format.")
     */
    private $birthdate;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $created_at;

    /**
     * Relation entre l'entity "User" et "Movie"
     * @ORM\OneToMany(targetEntity="App\Entity\Movie", mappedBy="user", cascade={"remove"})
     */
    private $movies;


    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getBirthDate()
    {
        return $this->birthdate;
    }

    public function setBirthDate($birthdate)
    {
        $this->birthdate = $birthdate;
    }

    public function getMovies()
    {
        return $this->movies;
    }

    public function setMovies($movies)
    {
        $this->movies = $movies;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * getRoles() - getSalt() - eraseCredentials()
     * Méthodes liées à la UserInterface
     */
    public function getRoles()
    {
        //
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
        //
    }
}
