<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\RecipeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\SecurityBundle\Security;

#[Route('/recette', name: 'recipe.')]
class RecipeController extends AbstractController
{
    /**
     * This controller display all the recipes
     *
     * @param PaginatorInterface $paginator
     * @param RecipeRepository $repository
     * @param Request $request
     * @return Response
     */
    #[Route('/', name: 'index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(PaginatorInterface $paginator, RecipeRepository $repository, Request $request): Response
    {
        $recipes = $paginator->paginate(
            $repository->findBy(['user' => $this->getUser()]),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    /**
     * Creates a new recipe
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/creation', name: 'new', methods: ['GET', 'POST'])]

    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();
            //Pour la recette nouvellement créée soit directement assignée à l'user courant
            $recipe->setUser($this->getUser());

            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash(
                'succes',
                'Votre recette a été ajoutée avec succes'
            );

            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('pages/recipe/new.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Security("IsGranted('ROLE_USER') and user === recipe.getUser()")]
    #[Route('/edition/{id}', name: 'edit', methods: ['GET', 'POST'])]
    /**
     * Undocumented function
     *
     * @param integer $id
     * @param Recipe $recipe
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(int $id, Recipe $recipe, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();

            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash(
                'succes',
                'Votre recette a été modifiée'
            );

            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('pages/recipe/edit.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/suppresion/{id}', name: 'delete', methods: ['GET'])]
    /**
     * Undocumented function
     *
     * @param Recipe $recipe
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Recipe $recipe, EntityManagerInterface $manager): Response
    {
        $manager->remove($recipe);
        $manager->flush();

        $this->addFlash(
            'succes',
            'Votre recette a été supprimé'
        );

        return $this->redirectToRoute('recipe.index');
    }
}
