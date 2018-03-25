<?php

namespace App\Controller;

use App\Entity\Choice;
use App\Entity\User;
use App\Form\ChoiceType;
use App\Form\UserType;
use App\Service\ImdbService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UserController
 * @package App\Controller
 */
class UserController extends Controller
{
    /** @var EntityManager */
    private $entityManger;

    /**
     * @return EntityManager
     */
    private function getEntityManager()
    {
        if (!$this->entityManger instanceof EntityManager) {
            $this->entityManger = $this->getDoctrine()->getManager();
        }

        return $this->entityManger;
    }

    /**
     * @Route("/user/create", methods={POST})
     * @param Request $request
     * @param ImdbService $imdbService
     * @return JsonResponse
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function create(Request $request, ImdbService $imdbService)
    {
        $newUser = new User();
        $form = $this->createForm(UserType::class, $newUser);

        $form->handleRequest($request);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $this->getEntityManager()->persist($newUser);
            $this->getEntityManager()->flush($newUser);

            $imdbService->easterEgg($newUser);

            return new JsonResponse(
                [
                    'success' => true,
                    'user'    => $newUser->__toArray(),
                ]
            );

        } else {
            $errors = [];
            foreach ($form->getErrors(true, true) as $error) {
                $errors[] = $error->getMessage();
            }

            return new JsonResponse(
                [
                    'success' => false,
                    'errors'  => $errors,
                ]
            );
        }
    }

    /**
     * @Route("/user/{id}/submit-choice", methods={POST})
     * @param User $user
     * @param Request $request
     * @return JsonResponse
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveChoice(User $user, Request $request)
    {
        $newChoice = (new Choice())->setUser($user);
        $form = $this->createForm(ChoiceType::class, $newChoice);

        $form->handleRequest($request);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $this->getEntityManager()->persist($newChoice);
            $this->getEntityManager()->flush($newChoice);

            return new JsonResponse(
                [
                    'success' => true,
                    'choice'  => $newChoice->__toArray(),
                ]
            );
        } else {
            $errors = [];
            foreach ($form->getErrors(true, true) as $error) {
                $errors[] = $error->getMessage();
            }

            return new JsonResponse(
                [
                    'success' => false,
                    'errors'  => $errors,
                ]
            );
        }
    }

    /**
     * @Route("/user/{id}/delete/{film}", methods={DELETE})
     * @param User $user
     * @param string $film
     * @return JsonResponse
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteChoice(User $user, $film)
    {
        foreach ($user->getChoices() as $choice) {
            if ($choice->getFilm() === $film) {
                $this->getEntityManager()->remove($choice);
                $this->getEntityManager()->flush();

                return new JsonResponse(['succes' => true]);
            }
        }

        return new JsonResponse(
            [
                'succes'  => false,
                'message' => 'The user did not choose this film',
            ]
        );
    }

    /**
     * @Route("/user/{id}/list-choice", methods={GET})
     * @param User $user
     * @param ImdbService $imdbService
     * @return JsonResponse
     */
    public function listChoices(User $user, ImdbService $imdbService)
    {
        $results = $imdbService->getUserFilms($user);

        return new JsonResponse($results);
    }

    /**
     * @Route("/list-users/{choice}", methods={GET})
     * @param string $choice
     * @return JsonResponse
     */
    public function listUser($choice)
    {
        /** @var ArrayCollection|User[] $users */
        $users = $this->getDoctrine()->getRepository(User::class)->findByChoice($choice);

        $usersData = [];
        foreach ($users as $user) {
            $usersData[] = $user->__toArray();
        }

        return new JsonResponse($usersData);
    }

    /**
     * @Route("/result", methods={GET})
     * @param ImdbService $imdbService
     * @return JsonResponse
     */
    public function result(ImdbService $imdbService)
    {
        $result = $imdbService->getResult();

        return new JsonResponse($result);
    }
}
