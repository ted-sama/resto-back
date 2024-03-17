<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController
{
    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    #[Route('/api/user', name: 'createUser', methods: ['POST'])]
    public function createUser(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);

        if (empty($payload['email'])) {
            return new JsonResponse(['error' => 'Email is required'], Response::HTTP_BAD_REQUEST);
        }

        if (empty($payload['password'])) {
            return new JsonResponse(['error' => 'Password is required'], Response::HTTP_BAD_REQUEST);
        }

        if (empty($payload['first_name'])) {
            return new JsonResponse(['error' => 'First name is required'], Response::HTTP_BAD_REQUEST);
        }

        if (empty($payload['last_name'])) {
            return new JsonResponse(['error' => 'Last name is required'], Response::HTTP_BAD_REQUEST);
        }

        if (empty($payload['phone_number'])) {
            return new JsonResponse(['error' => 'Phone number is required'], Response::HTTP_BAD_REQUEST);
        }

        $user = new User();

        $user->setEmail($payload['email']);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $payload['password']));
        $user->setFirstName($payload['first_name']);
        $user->setLastName($payload['last_name']);
        $user->setPhoneNumber($payload['phone_number']);
        $user->setRoles(["ROLE_USER"]);

        $em->persist($user);
        $em->flush();

        return new JsonResponse(['message' => 'User created'], Response::HTTP_CREATED);
    }

    #[Route('/api/user/me', name: 'getCurrentUser', methods: ['GET'])]
    public function getCurrentUser(): JsonResponse
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        return new JsonResponse([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'phone_number' => $user->getPhoneNumber(),
            'role' => $user->getRoles(),
        ]);
    }

    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants')]
    #[Route('api/user', name: 'getAllUsers', methods: ['GET'])]
    public function getAllUsers(UserRepository $userRepository): JsonResponse
    {
        // $users = $userRepository->findAll();

        // filter if query name is set else return all users
        if (isset($_GET['email'])) {
            // if includes email in user email like %email%
            $users = $userRepository->createQueryBuilder('u')
                ->where('u.email LIKE :email')
                ->setParameter('email', '%' . $_GET['email'] . '%')
                ->getQuery()
                ->getResult();
        } else {
            $users = $userRepository->findAll();
        }

        $data = [];

        foreach ($users as $user) {
            $data[] = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'first_name' => $user->getFirstName(),
                'last_name' => $user->getLastName(),
                'phone_number' => $user->getPhoneNumber(),
                'role' => $user->getRoles(),
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants')]
    #[Route('api/user/{id}', name: 'getUserById', methods: ['GET'])]
    public function getUserById(int $id, UserRepository $userRepository): JsonResponse
    {
        $user = $userRepository->find($id);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'phone_number' => $user->getPhoneNumber(),
            'role' => $user->getRoles(),
        ]);
    }

    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants')]
    #[Route('/api/user/{id}', name: 'updateUser', methods: ['PUT'])]
    public function updateUser(int $id, UserRepository $userRepository, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $user = $userRepository->find($id);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $payload = json_decode($request->getContent(), true);

        if (empty($payload['first_name'])) {
            return new JsonResponse(['error' => 'First name is required'], Response::HTTP_BAD_REQUEST);
        }

        if (empty($payload['last_name'])) {
            return new JsonResponse(['error' => 'Last name is required'], Response::HTTP_BAD_REQUEST);
        }

        if (empty($payload['phone_number'])) {
            return new JsonResponse(['error' => 'Phone number is required'], Response::HTTP_BAD_REQUEST);
        }

        $user->setFirstName($payload['first_name']);
        $user->setLastName($payload['last_name']);
        $user->setPhoneNumber($payload['phone_number']);

        $em->persist($user);
        $em->flush();

        return new JsonResponse(['message' => 'User updated'], Response::HTTP_OK);
    }

    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants')]
    #[Route('/api/user/{id}', name: 'deleteUser', methods: ['DELETE'])]
    public function deleteUser(int $id, UserRepository $userRepository, EntityManagerInterface $em): JsonResponse
    {
        $user = $userRepository->find($id);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $em->remove($user);
        $em->flush();

        return new JsonResponse(['message' => 'User deleted'], Response::HTTP_OK);
    }
}
