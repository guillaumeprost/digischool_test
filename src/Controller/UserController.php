<?php

namespace App\Controller;

use App\Entity\Choice;
use App\Entity\User;
use App\Form\ChoiceType;
use App\Form\UserType;
use App\Service\ImdbService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
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
     * @Route("/user")
     * @Method("POST")
     *
     * @param Request $request
     * @param ImdbService $imdbService
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
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

            /** @var FormError $error */
            foreach ($form->getErrors(true) as $error) {
                $errors[] = $error->getMessage();
            }

            return new JsonResponse(
                [
                    'success' => false,
                    'errors'  => $errors,
                ], 500
            );
        }
    }

    /**
     * @Route("/choice")
     * @Method("POST")
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveChoice(Request $request)
    {
        $user = $this->getEntityManager()->getRepository(User::class)->findOneById($request->query->get('userId', null));

        if(!$user instanceof User) {
            return new JsonResponse(
                [
                    'success' => false,
                    'choice'  => 'User not Found',
                ], 401
            );
        }

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
                ], 500
            );
        }
    }

    /**
     * @Route("/choice/{imdbId}")
     * @Method("DELETE")
     *
     * @param Request $request
     * @param string $imdbId
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteChoice(Request $request, $imdbId)
    {
        $user = $this->getEntityManager()->getRepository(User::class)->findOneById($request->query->get('userId', null));

        if(!$user instanceof User) {
            return new JsonResponse(
                [
                    'success' => false,
                    'choice'  => 'User not Found',
                ], 401
            );
        }

        foreach ($user->getChoices() as $choice) {
            if ($choice->getFilm() === $imdbId) {
                $this->getEntityManager()->remove($choice);
                $this->getEntityManager()->flush();

                return new JsonResponse(['succes' => true]);
            }
        }

        return new JsonResponse(
            [
                'succes'  => false,
                'message' => 'The user did not choose this film',
            ], 500
        );
    }

    /**
     * @Route("/choices")
     * @Method("GET")
     *
     * @param Request $request
     * @param ImdbService $imdbService
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     */
    public function listChoices(Request $request, ImdbService $imdbService)
    {
        $user = $this->getEntityManager()->getRepository(User::class)->findOneById($request->query->get('userId', null));

        if(!$user instanceof User) {
            return new JsonResponse(
                [
                    'success' => false,
                    'choice'  => 'User not Found',
                ], 401
            );
        }

        $results = $imdbService->getUserFilms($user);

        return new JsonResponse($results);
    }

    /**
     * @Route("/users")
     * @Method("GET")
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function listUser(Request $request)
    {
        /** @var ArrayCollection|User[] $users */
        $users = $this->getDoctrine()->getRepository(User::class)->findByChoice($request->query->get('choice', null));

        $usersData = [];
        foreach ($users as $user) {
            $usersData[] = $user->__toArray();
        }

        return new JsonResponse($usersData);
    }

    /**
     * @Route("/result")
     * @Method("GET")
     * @return JsonResponse
     */
    public function result()
    {
        $result = $this->getEntityManager()->getRepository('App:Choice')->countResult();

        return new JsonResponse($result);
    }
}
