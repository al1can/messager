<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\User;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GroupController extends AbstractController
{
    private $groupRepository;
    private $userRepository;

    public function __construct(GroupRepository $groupRepository, UserRepository $userRepository)
    {
        $this->groupRepository = $groupRepository;
        $this->userRepository = $userRepository;
    }

    #[Route('/api/v1/group', name: 'app_group', methods: 'GET')]
    public function index(): JsonResponse
    {
        $groups = $this->groupRepository->findAll();
        return $this->json([
            'groups' => $groups
        ], JsonResponse::HTTP_OK);
    }

    #[Route('/api/v1/group/{group}', name: 'app_group_show', methods: 'GET')]
    public function show(Group $group): JsonResponse
    {
        return $this->json([
            'group' => $group
        ], JsonResponse::HTTP_OK);
    }

    #[Route('/api/v1/group', name: 'app_group_store', methods: 'POST')]
    public function store(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $request = json_decode($request->getContent(), true);
        $user_ids = array_slice($request, 1, count($request), true);
        
        if (empty($request['name']) || empty($user_ids))
        {
            throw new NotFoundHttpException('Excepting mandatory parameters!');
        }
        
        $group = new Group();
        $group
            ->setName($request['name'])
            ->setCreateDate(new \DateTime('now'));
        
        foreach ($user_ids as $user_id)
        {
            $user = $this->userRepository->find($user_id);
            $group->addUser($user);
        }

        $errors = $validator->validate(($group));
        if (count($errors) > 0)
        {
            $errorsString = (string) $errors;
            return new JsonResponse($errorsString);
        }

        try
        {
            $this->groupRepository->save($group, true);
        } catch (Exception $e)
        {
            return $this->json([
                'Exception' => $e
            ]);
        }

        return $this->json([
            'status' => 'Group succesfully created',
            'group' => $group->toArray()
        ], JsonResponse::HTTP_CREATED);
    }

    #[Route('/api/v1/group/{group}', name: 'app_group_delete', methods: 'DELETE')]
    public function delete(Group $group): JsonResponse
    {
        $this->groupRepository->remove($group, true);
        return $this->json([
            'status' => 'Group succesfully deleted'
        ], JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route('/api/v1/group/{group}/add', name: 'app_group_add', methods: 'POST')]
    public function addUser(Group $group, Request $request): JsonResponse
    {
        $user_ids = json_decode($request->getContent(), true);
        foreach ($user_ids as $user_id)
        {
            $user = $this->userRepository->find($user_id);
            $group->addUser($user);
        }

        try
        {
            $this->groupRepository->save($group, true);
        } catch (Exception $e)
        {
            return $this->json([
                'Exception' => $e
            ]);
        }

        return $this->json([
            'status' => 'Users succesfully added group!',
            'group' => $group->toArray()
        ], JsonResponse::HTTP_OK);
    }

    #[Route('/api/v1/group/{group}/remove/{user}', name: 'app_group_remove', methods: 'DELETE')]
    public function removeUser(Group $group, User $user): JsonResponse
    {
        $group->removeUser($user);

        try
        {
            $this->groupRepository->save($group, true);
        } catch (Exception $e)
        {
            return $this->json([
                'Exception' => $e
            ]);
        }

        return $this->json([
            'status' => 'User succesfully removed from group!',
            'group' => $group->toArray()
        ], JsonResponse::HTTP_ACCEPTED);
    }
}
