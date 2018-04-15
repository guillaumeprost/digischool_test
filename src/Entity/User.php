<?php

namespace App\Entity;

use App\Entity\Traits\CreatedAt;
use App\Entity\Traits\DoctrineId;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Class User
 * @package App\Entity
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Entity\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("email")
 */
class User
{
    use DoctrineId;
    use CreatedAt;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $pseudo;

    /**
     * @var string
     * @Assert\Email()
     * @Assert\NotBlank()
     * @ORM\Column(type="string", unique=true)
     */
    private $email;

    /**
     * @var \DateTime
     * @Assert\DateTime()
     * @ORM\Column(type="date")
     */
    private $birthDate;

    /**
     * @var ArrayCollection|Choice[]
     * @ORM\OneToMany(targetEntity="App\Entity\Choice", mappedBy="user",cascade={"persist"})
     */
    private $choices;

    public function __toArray()
    {
        return [
            'id'        => $this->getId(),
            'pseudo'    => $this->getPseudo(),
            'email'     => $this->getEmail(),
            'birthDate' => $this->birthDate->format('d/m/Y'),
        ];
    }

    public function __construct()
    {
        $this->choices = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getPseudo()
    {
        return $this->pseudo;
    }

    /**
     * @param string $pseudo
     * @return $this
     */
    public function setPseudo($pseudo)
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * @param \DateTime $birthDate
     * @return $this
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * @return Choice[]|ArrayCollection
     */
    public function getChoices()
    {
        return $this->choices;
    }

    /**
     * @param Choice[]|ArrayCollection $choices
     * @return $this
     */
    public function setChoices($choices)
    {
        $this->choices = $choices;

        return $this;
    }

    /**
     * @param Choice $choice
     * @return User
     */
    public function addChoice(Choice $choice)
    {
        $this->choices->add($choice);
        $choice->setUser($this);

        return $this;
    }

    /**
     * @param Choice $choice
     * @return User
     */
    public function removeChoice(Choice $choice)
    {
        $this->choices->removeElement($choice);
        $choice->setUser(null);

        return $this;
    }
}