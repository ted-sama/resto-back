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

class AdminController extends AbstractController
{
    private $userPasswordHasher;
    private $adminCode = "admin_code";

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    #[Route('/api/admin', name: 'createAdminUser', methods: ['POST'])]
    public function createAdminUser(Request $request, EntityManagerInterface $em): JsonResponse
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

        if ($payload['admin_code'] !== $this->adminCode) {
            return new JsonResponse(['error' => 'Admin code is wrong'], Response::HTTP_BAD_REQUEST);
        }

        $user = new User();

        $user->setEmail($payload['email']);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $payload['password']));
        $user->setFirstName($payload['first_name']);
        $user->setLastName($payload['last_name']);
        $user->setPhoneNumber($payload['phone_number']);
        $user->setRoles(["ROLE_ADMIN"]);

        $em->persist($user);
        $em->flush();

        return new JsonResponse(['message' => 'Admin user created'], Response::HTTP_CREATED);
    }
}
