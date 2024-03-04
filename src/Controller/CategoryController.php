<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class CategoryController extends AbstractController
{
    #[Route('/api/categories', name: 'getCategories', methods: ['GET'])]
    public function getCategoryList(CategoryRepository $categoryRepository, SerializerInterface $serializer): JsonResponse
    {
        $categoryList = $categoryRepository->findAll();
        $jsonCategoryList = $serializer->serialize($categoryList, 'json', ['groups' => 'categoryList']);

        return new JsonResponse($jsonCategoryList, Response::HTTP_OK, [], true);
    }

    #[Route('/api/categories/{id}', name: 'getCategoryById', methods: ['GET'])]
    public function getCategoryById(CategoryRepository $categoryRepository, SerializerInterface $serializer, Request $request): JsonResponse
    {
        $categoryList = $categoryRepository->findBy(['id' => $request->get('id')]);
        $jsonCategoryList = $serializer->serialize($categoryList, 'json', ['groups' => 'categoryList']);

        return new JsonResponse($jsonCategoryList, Response::HTTP_OK, [], true);
    }

    #[Route('/api/categories/{id}/foods', name: 'getFoodFromCategory', methods: ['GET'])]
    public function getFoodFromCategory(CategoryRepository $categoryRepository, SerializerInterface $serializer, Request $request): JsonResponse
    {
        $category = $categoryRepository->find($request->get('id'));

        // Récupérer les plats associés à la catégorie
        $foods = $category->getFoods();

        // Sérialiser les données en JSON en incluant les plats
        $jsonCategory = $serializer->serialize($foods, 'json', ['groups' => 'foodByCategory']);

        return new JsonResponse($jsonCategory, Response::HTTP_OK, [], true);
    }

    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants pour créer une catégorie')]
    #[Route('/api/categories', name: 'createCatgeory', methods: ['POST'])]
    public function createCategory(CategoryRepository $categoryRepository, SerializerInterface $serializer, FileUploader $fileUploader, EntityManagerInterface $em, Request $request): JsonResponse
    {
        $uploadedFile = $request->files->get('image');
        if (!$uploadedFile) {
            throw new BadRequestHttpException('"image" is required');
        }

        $category = new Category();
        $category->setName($request->request->get('name'));
        if ($request->request->get('featured') === 'true') {
            $category->setFeatured(true);
        } else {
            $category->setFeatured(false);
        }
        if ($request->request->get('active') === 'true') {
            $category->setActive(true);
        } else {
            $category->setActive(false);
        }
        $category->setImage($fileUploader->upload($uploadedFile));

        $em->persist($category);
        $em->flush();

        $jsonCategory = $serializer->serialize($category, 'json', ['groups' => 'categoryList']);

        return new JsonResponse($jsonCategory, Response::HTTP_CREATED, [], true);
    }

    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants pour supprimer une catégorie')]
    #[Route('/api/categories/{id}', name: 'deleteCategory', methods: ['DELETE'])]
    public function deleteCategory(Category $category, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($category);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants pour mettre à jour une catégorie')]
    #[Route('/api/categories/{id}', name: 'updateCategory', methods: ['POST'])]
    public function updateCategory(CategoryRepository $categoryRepository, SerializerInterface $serializer, FileUploader $fileUploader, EntityManagerInterface $em, Request $request): JsonResponse
    {
        $category = $categoryRepository->find($request->get('id'));
        if (!$category) {
            throw $this->createNotFoundException('La catégorie demandée n\'existe pas');
        }

        if ($request->files->get('image')) {
            $uploadedFile = $request->files->get('image');
            $category->setImage($fileUploader->upload($uploadedFile));
        }

        if ($request->request->get('name')) {
            $category->setName($request->request->get('name'));
        }

        if ($request->request->get('featured')) {
            if ($request->request->get('featured') === 'true') {
                $category->setFeatured(true);
            } else {
                $category->setFeatured(false);
            }
        }

        if ($request->request->get('active')) {
            if ($request->request->get('active') === 'true') {
                $category->setActive(true);
            } else {
                $category->setActive(false);
            }
        }

        $em->persist($category);
        $em->flush();

        $jsonCategory = $serializer->serialize($category, 'json', ['groups' => 'foodList']);

        return new JsonResponse($jsonCategory, Response::HTTP_OK, [], true);
    }
}
