<?php

namespace App\Entity;

use App\Entity\Traits\CreatedAt;
use App\Entity\Traits\DoctrineId;
use Doctrine\ORM\Mapping as ORM;

class User
{
    use DoctrineId;
    use CreatedAt;

    /**
     * @var string
     */
    private $pseudo;

    /**
     * @var string
     */
    private $email;

    /**
     * @var \DateTime
     */
    private $birthDate;

}