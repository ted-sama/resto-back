<?php

namespace App\Controller;

use App\Entity\Food;
use App\Repository\FoodRepository;
use App\Repository\CartItemRepository;
use App\Entity\CartItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class CartController extends AbstractController
{
    #[Route('/api/cart', name: 'addToCart', methods: ['POST'])]
    public function addToCart(SerializerInterface $serializer, FoodRepository $foodRepository, CartItemRepository $cartRepository, EntityManagerInterface $em, Request $request): JsonResponse
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $payload = json_decode($request->getContent(), true);

        $food = $foodRepository->find($payload['item_id']);

        if (!$food) {
            return new JsonResponse(['error' => 'Item not found'], Response::HTTP_NOT_FOUND);
        }

        if ($cartRepository->findOneBy(['food' => $food, 'user' => $user])) {
            $foodToUpdate = $cartRepository->findOneBy(['food' => $food, 'user' => $user]);

            if ($payload["quantity"] <= 0) {
                $em->remove($foodToUpdate);
                $em->flush();

                $userCart = $cartRepository->findBy(['user' => $user]);
                $jsonUserCart = $serializer->serialize($userCart, 'json', ['groups' => 'cart']);

                return new JsonResponse($jsonUserCart, Response::HTTP_OK, [], true);
            }
            $foodToUpdate->setQuantity($foodToUpdate->getQuantity() + $payload["quantity"]);
            $em->persist($foodToUpdate);
            $em->flush();

            $userCart = $cartRepository->findBy(['user' => $user]);
            $jsonUserCart = $serializer->serialize($userCart, 'json', ['groups' => 'cart']);

            return new JsonResponse($jsonUserCart, Response::HTTP_OK, [], true);
        }

        $cartItem = new CartItem();
        $cartItem->setFood($food);

        if ($payload["quantity"] > 0) {
            $cartItem->setQuantity($payload["quantity"]);
        } else {
            $cartItem->setQuantity(1);
        }

        $cartItem->setUser($user);

        $em->persist($cartItem);
        $em->flush();

        $userCart = $cartRepository->findBy(['user' => $user]);
        $jsonUserCart = $serializer->serialize($userCart, 'json', ['groups' => 'cart']);

        return new JsonResponse($jsonUserCart, Response::HTTP_OK, [], true);
    }

    #[Route('/api/cart/quantity', name: 'setQuantity', methods: ['POST'])]
    public function setQuantity(SerializerInterface $serializer, FoodRepository $foodRepository, CartItemRepository $cartRepository, EntityManagerInterface $em, Request $request): JsonResponse
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $payload = json_decode($request->getContent(), true);

        $food = $foodRepository->find($payload['item_id']);

        if (!$food) {
            return new JsonResponse(['error' => 'Item not found'], Response::HTTP_NOT_FOUND);
        }

        $foodToUpdate = $cartRepository->findOneBy(['food' => $food, 'user' => $user]);

        if (!$foodToUpdate) {
            return new JsonResponse(['error' => 'Item not found in cart'], Response::HTTP_NOT_FOUND);
        }

        if ($payload["quantity"] <= 0) {
            $em->remove($foodToUpdate);
            $em->flush();

            $userCart = $cartRepository->findBy(['user' => $user]);
            $jsonUserCart = $serializer->serialize($userCart, 'json', ['groups' => 'cart']);

            return new JsonResponse($jsonUserCart, Response::HTTP_OK, [], true);
        }

        $foodToUpdate->setQuantity($payload["quantity"]);

        $em->persist($foodToUpdate);
        $em->flush();

        $userCart = $cartRepository->findBy(['user' => $user]);
        $jsonUserCart = $serializer->serialize($userCart, 'json', ['groups' => 'cart']);

        return new JsonResponse($jsonUserCart, Response::HTTP_OK, [], true);
    }

    #[Route('/api/cart', name: 'getCart', methods: ['GET'])]
    public function getCart(CartItemRepository $cartRepository, SerializerInterface $serializer): JsonResponse
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $userCart = $cartRepository->findBy(['user' => $user]);
        $jsonUserCart = $serializer->serialize($userCart, 'json', ['groups' => 'cart']);

        return new JsonResponse($jsonUserCart, Response::HTTP_OK, [], true);
    }
}
