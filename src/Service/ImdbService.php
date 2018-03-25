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

        if ($result->Response === 'True') {
            if (isset($result->Search)){
                $data = [];
                foreach ($result->Search as $result) {
                    $data[] = [
                        'Title'  => $result->Title,
                        'Poster' => $result->Poster,
                        'imdbID' => $result->imdbID
                    ];
                }

                return $data;
            }
            return [
                'Title'  => $result->Title,
                'Poster' => $result->Poster,
                'imdbID' => $result->imdbID
            ];
        }

        return false;
    }

    /**
     * @return array
     */
    public function getResult()
    {
        $counting = [];
        $choices = $this->entityManager->getRepository(Choice::class)->findAll();

        foreach ($choices as $choice) {
            if (array_key_exists($choice->getFilm(), $counting)) {
                $counting[$choice->getFilm()]++;
            } else {
                $counting[$choice->getFilm()] = 1;
            }

        }
        arsort($counting);

        $searchModel = new ImdbSearchModel();
        $searchModel
            ->setImdbId(key($counting));

        return array_merge(['votes' => current($counting)], $this->searchFilm($searchModel));
    }

    /**
     * @param User $user
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
                $choice->setFilm($result['imdbID']);

                $user->addChoice($choice);
            }

            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
    }
}