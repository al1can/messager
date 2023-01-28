<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\UserRepository;
use Exception;
use LDAP\Result;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    #[Route('/api/v1/user', name: 'app_user', methods:'GET')]
    public function index(): JsonResponse
    {
        $users = $this->userRepository->findAll();
        $response = [];
        foreach ($users as $user)
        {
            $response[] = $user->toArray();
        }
        return $this->json([
            'users' => $response
        ], Response::HTTP_OK);
    }

    #[Route('/api/v1/user/{user}', name: 'app_user_show', methods: 'GET')]
    public function show(User $user): JsonResponse
    {
        if ($user === null)
        {
            return $this->json([
                'response' => 'User with requested fields not found!'
            ], Response::HTTP_BAD_REQUEST);
        }
        return $this->json([
            'user' => $user->toArray()
        ], Response::HTTP_OK);
    }

    #[Route('/api/v1/user', name: 'app_user_store', methods: 'POST')]
    public function store(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $request = json_decode($request->getContent(), true);
        
        if (empty($request['phone_number'])
            || empty($request['country_code'])
            || empty($request['name'])
            || empty($request['email'])
            || empty($request['password']))
        {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }

        $user = new User();
        $user
            ->setPhoneNumber($request['phone_number'])
            ->setCountryCode($request['country_code'])
            ->setEmail($request['email'])
            ->setPassword($request['password'])
            ->setName($request['name']);

        $errors = $validator->validate($user);
        if (count($errors) > 0)
        {
            $errorsString = (string) $errors;
            return new JsonResponse($errorsString);
        }

        try
        {
            $this->userRepository->save($user, true);
        } catch (Exception $e)
        {
            return $this->json([
                'Exception' => $e
            ]);
        }

        return $this->json([
            'status' => 'User succesfully created!',
            'user' => $user->toArray()
        ], JsonResponse::HTTP_CREATED);
    }

    #[Route('/api/v1/user/{user}', name: 'app_user_update', methods: 'PUT')]
    public function update(User $user, Request $request, ValidatorInterface $validator): JsonResponse
    {
        $request = json_decode($request->getContent(), true);

        if ($user === null)
        {
            return $this->json([
                'response' => 'User with requested fields not found!'
            ], Response::HTTP_BAD_REQUEST);
        }

        empty($request['name']) ? true : $user->setName($request['name']);
        empty($request['phone_number']) ? true : $user->setPhoneNumber($request['phone_number']);
        empty($request['country_code']) ? true : $user->setCountryCode($request['country_code']);
        empty($request['email']) ? true : $user->setEmail($request['email']);
        empty($request['password']) ? true : $user->setPassword($request['password']);

        $errors = $validator->validate($user);
        if (count($errors) > 0)
        {
            $errorsString = (string) $errors;
            return new JsonResponse($errorsString);
        }

        $this->userRepository->save($user, true);

        return $this->json([
            'status' => 'User succesfully updated!',
            'user' => $user->toArray()
        ], JsonResponse::HTTP_OK);
    }

    #[Route('/api/v1/user/{user}', name: 'app_user_delete', methods: 'DELETE')]
    public function delete(User $user): JsonResponse
    {
        $this->userRepository->remove($user, true);
        return $this->json([
            'status' => 'User succesfully deleted!',
        ], JsonResponse::HTTP_ACCEPTED);
    }
}
