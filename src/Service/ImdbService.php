<?php

namespace App\Service;

use App\Entity\Choice;
use App\Entity\User;
use App\Model\ImdbSearchModel;
use Doctrine\ORM\EntityManager;

/**
 * Class ImdbService
 * @package App\Service
 */
class ImdbService
{
    /** @var string */
    private $imdbApiKey;

    /** @var string */
    private $imdbUrl;

    /** @var EntityManager */
    private $entityManager;

    /**
     * ImdbService constructor.
     * @param string $imdbApiKey
     * @param string $imdbUrl
     * @param EntityManager $entityManager
     */
    public function __construct($imdbApiKey, $imdbUrl, EntityManager $entityManager)
    {
        $this->imdbApiKey = $imdbApiKey;
        $this->imdbUrl = $imdbUrl;
        $this->entityManager = $entityManager;
    }

    /**
     * @param User $user
     * @return array
     */
    public function getUserFilms(User $user)
    {
        $films = [];
        foreach ($user->getChoices() as $choice) {
            $searchModel = new ImdbSearchModel();
            $searchModel
                ->setImdbId($choice->getFilm());
            $films[$choice->getFilm()] = $this->searchFilm($searchModel);
        }

        return $films;
    }

    /**
     * @param ImdbSearchModel $searchModel
     * @return array|bool
     */
    public function searchFilm(ImdbSearchModel $searchModel)
    {
        $data = array_merge(['apikey' => $this->imdbApiKey], $searchModel->__toArray());

        $queryParameters = http_build_query($data);

        $curl = curl_init($this->imdbUrl.'?'.$queryParameters);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $result = json_decode(curl_exec($curl));

        curl_close($curl);


        if ($result->Response === 'False') {
            return [
                'success' => false,
                'message' => $result->Error
            ];
        }

        if (isset($result->Search)){
            $data = [];
            foreach ($result->Search as $result) {
                $data[] = [
                    'success' => true,
                    'title'   => $result->Title,
                    'poster'  => $result->Poster,
                    'imdbId'  => $result->imdbID
                ];
            }

            return $data;
        }
        return [
            'success' => true,
            'title'   => $result->Title,
            'poster'  => $result->Poster,
            'imdbId'  => $result->imdbID
        ];

    }

    /**
     * @param User $user
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function easterEgg(User $user) {
        if ($user->getPseudo() === 'bruce_wayne') {
            $searchModel = new ImdbSearchModel();
            $searchModel
                ->setSearch('batman');
            $results = $this->searchFilm($searchModel);

            foreach ($results as $result) {
                $choice = new Choice();
                $choice->setFilm($result['imdbId']);

                $user->addChoice($choice);
            }

            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
    }
}