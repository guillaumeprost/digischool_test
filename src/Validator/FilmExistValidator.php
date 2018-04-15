<?php

namespace App\Validator;

use App\Model\ImdbSearchModel;
use App\Service\ImdbService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class FilmExistValidator extends ConstraintValidator
{
    /** @var ImdbService  */
    private $imdbService;

    public function __construct(ImdbService $service)
    {
        $this->imdbService = $service;
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        $model = new ImdbSearchModel();
        $model->setImdbId($value);

        $response = $this->imdbService->searchFilm($model);

        if ($response['success'] === false )
        $this->context->buildViolation($constraint->message)
                      ->setParameter('{{ string }}', $value)
                      ->addViolation();
    }
}
