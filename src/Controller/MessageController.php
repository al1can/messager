<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Message;
use App\Entity\User;
use App\Entity\Recipient;
use App\Repository\MessageRepository;
use App\Repository\RecipientRepository;
use App\Repository\UserRepository;
use DateTime;
use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MessageController extends AbstractController
{
    private $messageRepository;
    private $recipientRepository;
    private $userRepository;

    public function __construct(MessageRepository $messageRepository,
        RecipientRepository $recipientRepository,
        UserRepository $userRepository)
    {
        $this->messageRepository = $messageRepository;
        $this->recipientRepository = $recipientRepository;
        $this->userRepository = $userRepository;
    }
    #[Route('/message', name: 'app_message', methods: 'GET')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/MessageController.php',
        ]);
    }

    #[Route('/message/{message}', name: 'app_message_show', methods: 'GET')]
    public function show(Message $message): JsonResponse
    {
        return $this->json([$message]);
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/MessageController.php',
        ]);
    }

    #[Route('/message/{user}/{recipient_user}', name: 'app_message_store', methods: 'POST')]
    public function store(User $user, String $recipient_user, Request $request, ValidatorInterface $validator): JsonResponse
    {
        //return $this->json($recipient_user);
        $request = $request->query->all();
        
        if (empty($request['message']) || empty($user) || empty($recipient_user))
        {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }

        $message = new Message();
        $message
            ->setMessage($request['message'])
            ->setUserSent($user)
            ->setCreateDate(new DateTime('now'));

        $errors = $validator->validate($message);
        if (count($errors) > 0)
        {
            $errorsString = (string) $errors;
            return new JsonResponse($errorsString);
        }

        try
        {
            $this->messageRepository->save($message, true);
        } catch (Exception $e)
        {
            return $this->json([
                'Exception' => $e
            ]);
        }
        
        $recipient = new Recipient();
        $recipient
            ->setMessage($message)
            ->setRecipientUser($this->userRepository->find($recipient_user));

        $errors = $validator->validate($recipient);
        if (count($errors) > 0)
        {
            $errorsString = (string) $errors;
            return new JsonResponse($errorsString);
        }
    
        try
        {
            $this->recipientRepository->save($recipient, true);
        } catch (Exception $e)
        {
            return $this->json([
                'Exception' => $e
            ]);
        }

        return $this->json([
            'status' => 'Message succesfully created!',
            'message' => $message->toArray()
        ], JsonResponse::HTTP_CREATED);
    }
}
