<?php

namespace App\Controller;

use App\Repository\FoodRepository;
use App\Entity\Food;
use App\Repository\CategoryRepository;
use App\Service\FileUploader;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class FoodController extends AbstractController
{
    #[Route('/api/foods', name: 'getFoods', methods: ['GET'])]
    public function getFoodList(FoodRepository $foodRepository, SerializerInterface $serializer): JsonResponse
    {
        // filter if query name is set else return all foods
        if (isset($_GET['name'])) {
            // if includes name in category name like %name%
            $foodList = $foodRepository->createQueryBuilder('f')
                ->where('f.name LIKE :name')
                ->setParameter('name', '%' . $_GET['name'] . '%')
                ->getQuery()
                ->getResult();
        } else {
            $foodList = $foodRepository->findAll();
        }

        $jsonFoodList = $serializer->serialize($foodList, 'json', ['groups' => 'foodList']);

        return new JsonResponse($jsonFoodList, Response::HTTP_OK, [], true);
    }

    #[Route('/api/foods/{id}', name: 'getFoodById', methods: ['GET'])]
    public function getFoodById(FoodRepository $foodRepository, SerializerInterface $serializer, Request $request): JsonResponse
    {
        $foodList = $foodRepository->findBy(['id' => $request->get('id')]);
        $jsonFoodList = $serializer->serialize($foodList, 'json', ['groups' => 'foodList']);

        return new JsonResponse($jsonFoodList, Response::HTTP_OK, [], true);
    }

    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants pour créer un plat')]
    #[Route('/api/foods', name: 'createFood', methods: ['POST'])]
    public function createFood(FoodRepository $foodRepository, CategoryRepository $categoryRepository, SerializerInterface $serializer, FileUploader $fileUploader, EntityManagerInterface $em, Request $request): JsonResponse
    {
        $uploadedFile = $request->files->get('image');
        if (!$uploadedFile) {
            throw new BadRequestHttpException('"image" is required');
        }

        $food = new Food();
        $food->setName($request->request->get('name'));
        $food->setDescription($request->request->get('description'));
        $food->setPrice($request->request->get('price'));
        if ($request->request->get('featured') === 'true') {
            $food->setFeatured(true);
        } else {
            $food->setFeatured(false);
        }
        if ($request->request->get('active') === 'true') {
            $food->setActive(true);
        } else {
            $food->setActive(false);
        }
        $food->setCategory($categoryRepository->find($request->request->get('category_id')));
        $food->setImage($fileUploader->upload($uploadedFile));

        $em->persist($food);
        $em->flush();

        $jsonFood = $serializer->serialize($food, 'json', ['groups' => 'foodList']);

        return new JsonResponse($jsonFood, Response::HTTP_CREATED, [], true);
    }

    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants pour supprimer un plat')]
    #[Route('/api/foods/{id}', name: 'deleteFood', methods: ['DELETE'])]
    public function deleteFood(Food $food, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($food);
        $em->flush();

        return new JsonResponse(['message' => 'Food deleted'], Response::HTTP_OK);
    }

    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants pour mettre à jour un plat')]
    #[Route('/api/foods/{id}', name: 'updateFood', methods: ['POST'])]
    public function updateFood(FoodRepository $foodRepository, CategoryRepository $categoryRepository, SerializerInterface $serializer, FileUploader $fileUploader, EntityManagerInterface $em, Request $request): JsonResponse
    {
        $food = $foodRepository->find($request->get('id'));
        if (!$food) {
            throw $this->createNotFoundException('Le plat demandé n\'existe pas');
        }

        if ($request->files->get('image')) {
            $uploadedFile = $request->files->get('image');
            $food->setImage($fileUploader->upload($uploadedFile));
        }

        if ($request->request->get('name')) {
            $food->setName($request->request->get('name'));
        }

        if ($request->request->get('description')) {
            $food->setDescription($request->request->get('description'));
        }

        if ($request->request->get('price')) {
            $food->setPrice($request->request->get('price'));
        }

        if ($request->request->get('featured')) {
            if ($request->request->get('featured') === 'true') {
                $food->setFeatured(true);
            } else {
                $food->setFeatured(false);
            }
        }

        if ($request->request->get('active')) {
            if ($request->request->get('active') === 'true') {
                $food->setActive(true);
            } else {
                $food->setActive(false);
            }
        }

        if ($request->request->get('category_id')) {
            $food->setCategory($categoryRepository->find($request->request->get('category_id')));
        }

        $em->persist($food);
        $em->flush();

        $jsonFood = $serializer->serialize($food, 'json', ['groups' => 'foodList']);

        return new JsonResponse($jsonFood, Response::HTTP_OK, [], true);
    }
}
