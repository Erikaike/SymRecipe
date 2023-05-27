<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\IngredientType;
use Doctrine\ORM\Mapping\Entity;

#[Route('/ingredient', name: 'ingredient')]
class IngredientController extends AbstractController
{
    /**
     * This controller displays all ingredients
     *
     * @param IngredientRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/', name: '.index', methods: ['GET'])]
    public function index(IngredientRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $ingredients = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/ingredient/index.html.twig', [
            'ingredients' => $ingredients
        ]);
    }

    /**
     * This controller show a form which create an ingredient
     *
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     */
    #[Route('/nouveau', name: '.new', methods: ['GET', 'POST'])]
    public function new(EntityManagerInterface $manager, Request $request): Response
    {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();

            $manager->persist($ingredient);
            $manager->flush();

            $this->addFlash(
                'succes',
                'Votre ingrédient a été ajouté'
            );

            return $this->redirectToRoute('ingredient.index');
        }
        return $this->render('pages/ingredient/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/edition/{id}', name: '.edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Ingredient $ingredient, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();

            $manager->persist($ingredient);
            $manager->flush();

            $this->addFlash(
                'succes',
                'Votre ingrédient a été modifié'
            );

            return $this->redirectToRoute('ingredient.index');
        }

        return $this->render('pages/ingredient/edit.html.twig', [
            'form' => $form
        ]);
    }
    #[Route('/suppresion/{id}', name: '.delete', methods: ['GET'])]
    public function delete(Ingredient $ingredient, EntityManagerInterface $manager): Response
    {
        $manager->remove($ingredient);
        $manager->flush();

        $this->addFlash(
            'succes',
            'Votre ingrédient a été supprimé'
        );

        return $this->redirectToRoute('ingredient.index');
    }
}
