<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Repository\CartItemRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class OrderController extends AbstractController
{
    #[Route('/api/orders', name: 'placeOrder', methods: ['POST'])]
    public function placeOrder(CartItemRepository $cartRepository, EntityManagerInterface $em, Request $request): JsonResponse
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $userCart = $cartRepository->findBy(['user' => $user]);

        if (empty($userCart)) {
            return new JsonResponse(['error' => 'Your cart is empty'], Response::HTTP_BAD_REQUEST);
        }

        $payload = json_decode($request->getContent(), true);

        if (!isset($payload['address'])) {
            return new JsonResponse(['error' => 'Address is required'], Response::HTTP_BAD_REQUEST);
        }

        if (!isset($payload['city'])) {
            return new JsonResponse(['error' => 'City is required'], Response::HTTP_BAD_REQUEST);
        }

        if (!isset($payload['zip'])) {
            return new JsonResponse(['error' => 'Zip is required'], Response::HTTP_BAD_REQUEST);
        }

        if (!isset($payload['country'])) {
            return new JsonResponse(['error' => 'Country is required'], Response::HTTP_BAD_REQUEST);
        }

        $order = new Order();

        $totalPrice = 0;
        foreach ($userCart as $item) {
            $totalPrice += $item->getFood()->getPrice() * $item->getQuantity();
        }

        $totalItems = 0;
        foreach ($userCart as $item) {
            $totalItems += $item->getQuantity();
        }

        $order->setStatus("pending");
        $order->setUser($user);
        $order->setTotalPrice($totalPrice);
        $order->setTotalItems($totalItems);
        $order->setCreatedAt(new \DateTimeImmutable());
        $order->setAddress($payload['address']);
        $order->setCity($payload['city']);
        $order->setZip($payload['zip']);
        $order->setCountry($payload['country']);
        if (isset($payload['note'])) {
            $order->setNote($payload['note']);
        }

        $em->persist($order);
        $em->flush();

        foreach ($userCart as $item) {
            $orderDetail = new OrderDetail();
            $orderDetail->setOrderId($order);
            $orderDetail->setFood($item->getFood());
            $orderDetail->setUnitPrice($item->getFood()->getPrice());
            $orderDetail->setQuantity($item->getQuantity());
            $order->addOrderDetail($orderDetail);

            $em->persist($orderDetail);
            $em->flush();
        }

        foreach ($userCart as $item) {
            $em->remove($item);
            $em->flush();
        }

        return new JsonResponse(['message' => 'Order placed successfully'], Response::HTTP_CREATED);
    }

    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants pour modifier le statut d\'une commande')]
    #[Route('/api/orders/{id}/status', name: 'setOrderStatus', methods: ['POST'])]
    public function setOrderStatus(Order $order, OrderRepository $orderRepository, EntityManagerInterface $em, Request $request): JsonResponse
    {
        $order = $orderRepository->find($request->get('id'));

        $payload = json_decode($request->getContent(), true);

        if (!isset($payload['status'])) {
            return new JsonResponse(['error' => 'Status is required'], Response::HTTP_BAD_REQUEST);
        }

        if (!in_array($payload['status'], ['pending', 'delivered', 'canceled'])) {
            return new JsonResponse(['error' => 'Invalid status'], Response::HTTP_BAD_REQUEST);
        }

        $order->setStatus($payload['status']);

        $em->persist($order);
        $em->flush();

        return new JsonResponse(['message' => 'Order status updated successfully'], Response::HTTP_OK);
    }

    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants pour voir les commandes')]
    #[Route('/api/orders', name: 'getOrders', methods: ['GET'])]
    public function getOrders(OrderRepository $orderRepository): JsonResponse
    {
        $orders = $orderRepository->findAll();

        $data = [];

        foreach ($orders as $order) {
            $data[] = [
                'id' => $order->getId(),
                'status' => $order->getStatus(),
                'total_price' => $order->getTotalPrice(),
                'total_items' => $order->getTotalItems(),
                'created_at' => $order->getCreatedAt(),
                // address in object format
                'shipping_info' => [
                    'address' => $order->getAddress(),
                    'city' => $order->getCity(),
                    'zip' => $order->getZip(),
                    'country' => $order->getCountry(),
                ],
                // user infos
                'user' => [
                    'id' => $order->getUser()->getId(),
                    'email' => $order->getUser()->getEmail(),
                ],
                'note' => $order->getNote(),
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }
}
