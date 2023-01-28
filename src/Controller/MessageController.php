<?php

namespace App\Controller;

use App\Entity\Group;
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
use Doctrine\Persistence\ManagerRegistry;
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

    #[Route('/api/v1/message', name: 'app_message', methods: 'GET')]
    public function index(): JsonResponse
    {
        $messages = $this->messageRepository->findAll();

        return $this->json([
            'messages' => $messages,
        ], JsonResponse::HTTP_OK);
    }

    #[Route('/api/v1/message/{message}', name: 'app_message_show', methods: 'GET')]
    public function show(Message $message, ManagerRegistry $doctrine): JsonResponse
    {   
        dd("asd");
        if ($message === null)
        {
            return $this->json([
                'response' => 'Requested message not found!'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        return $this->json([
            //$recipients->first()->toArray()
            $message->toArray()
        ]);
    }

    #[Route('/api/v1/message/{user}/user/{recipient_user}', name: 'app_message_store_user', methods: 'POST')]
    public function sendMessageToUser(User $user, User $recipient_user, Request $request, ValidatorInterface $validator, ManagerRegistry $doctrine): JsonResponse
    {
        //return $this->json($recipient_user);
        //$request = $request->query->all();
        $request = json_decode($request->getContent(), true);
        
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
        
        $recipient = new Recipient();
        $recipient
            ->setMessage($message)
            ->setRecipientUser($recipient_user);

        $errors = $validator->validate($recipient);
        if (count($errors) > 0)
        {
            $errorsString = (string) $errors;
            return new JsonResponse($errorsString);
        }
    
        try
        {
            $this->recipientRepository->save($recipient);
        } catch (Exception $e)
        {
            return $this->json([
                'Exception' => $e
            ]);
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
        return $this->json([$message->toArray()]);
        return $this->json([
            'status' => 'Message succesfully created!',
            'message' => $message,
            //'recipients' => $message->getRecipients()->first()->toArray()
        ], JsonResponse::HTTP_CREATED);
    }

    #[Route('/api/v1/message/{user}/group/{recipient_group}', name: 'app_message_store_group', methods: 'POST')]
    public function sendMessageToGroup(User $user, Group $recipient_group, Request $request, ValidatorInterface $validator, ManagerRegistry $doctrine): JsonResponse
    {
        //return $this->json($recipient_user);
        $request = json_decode($request->getContent(), true);
        
        if (empty($request['message']) || empty($user) || empty($recipient_group))
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
        
        $recipient = new Recipient();
        $recipient
            ->setMessage($message)
            ->setRecipientGroup($recipient_group);

        $errors = $validator->validate($recipient);
        if (count($errors) > 0)
        {
            $errorsString = (string) $errors;
            return new JsonResponse($errorsString);
        }
    
        try
        {
            $this->recipientRepository->save($recipient);
        } catch (Exception $e)
        {
            return $this->json([
                'Exception' => $e
            ]);
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
        return $this->json([$message->toArray()]);
        return $this->json([
            'status' => 'Message succesfully created!',
            'message' => $message,
            //'recipients' => $message->getRecipients()->first()->toArray()
        ], JsonResponse::HTTP_CREATED);
    }

    #[Route('/api/v1/message/{message}', name: 'app_message_delete', methods: 'DELETE')]
    public function delete(Message $message): JsonResponse
    {
        $this->messageRepository->remove($message, true);
        return $this->json([
            'status' => 'Message deleted',
        ], JsonResponse::HTTP_ACCEPTED);
    }
}
