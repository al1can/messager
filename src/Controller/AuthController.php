<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\ResponseService;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\User;

class AuthController extends AbstractController
{
    private $responseService;
    private $userRepository;

    public function __construct(ResponseService $responseService, UserRepository $userRepository)
    {
        $this->responseService = $responseService;
        $this->userRepository = $userRepository;
    }

    #[Route('/auth', name: 'app_auth')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/AuthController.php',
        ]);
    }

    public function register(Request $request)
    {
        $request = $this->responseService->transformJsonBody($request);
        $firstName = $request->get('firstname');
        $lastName = $request->get('lastname');
        $username = $request->get('username');
        $email = $request->get('email');
        $password = $request->get('password');

        if (empty($request['phone_number'])
            || empty($request['country_code'])
            || empty($request['name'])
            || empty($request['email'])
            || empty($request['password']))
        {
            return $this->responseService->respondValidationError('Expecting mandatory parameters!');
        }

        $this->userRepository->addUser($request['phone_number'],
                                        $request['country_code'],
                                        $request['email'],
                                        $request['password'],
                                        $request['name']);

        return $this->responseService->respondWithSuccess('User succesfully created!');
    }

    public function getTokenUser(UserInterface $user, JWTTokenManagerInterface $JWTManager)
    {
        return new JsonResponse([
            'token' => $JWTManager->create($user)
        ]);
    }
}
