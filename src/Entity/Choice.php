<?php

namespace App\Entity;

use App\Entity\Traits\DoctrineId;
use App\Validator\FilmExist;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Class Choice
 * @package App\Entity
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Entity\Repository\ChoiceRepository")
 * @UniqueEntity(
 *     fields={"user", "film"},
 *     errorPath="film",
 *     message="The user already voted for this film"
 * )
 */
class Choice
{
    use DoctrineId;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="choices")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * Imdb film Id
     *
     * @var string
     * @FilmExist()
     * @ORM\Column(type="string")
     */
    private $film;

    public function __toArray()
    {
        return [
            'user' => $this->getUser() ? $this->getUser()->getId() : null,
            'film' => $this->getFilm() ? $this->getFilm() : null,
        ];
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return string
     */
    public function getFilm()
    {
        return $this->film;
    }

    /**
     * @param string $film
     * @return $this
     */
    public function setFilm($film)
    {
        $this->film = $film;

        return $this;
    }
}