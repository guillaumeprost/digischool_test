<?php

namespace App\Validator;


use Symfony\Component\Validator\Constraint;

/**
 * Class FilmExist
 * @package App\Validator
 * @Annotation
 */
class FilmExist extends Constraint
{
    public $message = 'The Film with the id {{ string }} does not exist';

}
