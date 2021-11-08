<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\IngredientRepository;
use App\Repository\RecipeRepository;
use App\Repository\StepRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecipeController extends AbstractController
{

    /**
     * @Route("/", name="index")
     */
    public function index(Request $request, RecipeRepository $recipeRepository) {
        return $this->render("recipe/index.html.twig", [
            "recipes" => $recipeRepository->findAll()
        ]);
    }

    /**
     * @Route("/recipe/add", name="add_recipe")
     */
    public function add(Request $request, RecipeRepository $recipeRepository,
        IngredientRepository $ingredientRepository, StepRepository $stepRepository): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $image = $form['image']->getData();
            $imageName = $recipe->getName() . "." . $image->guessExtension();
            $directory = $this->getParameter('images_directory');
            $image->move($directory, $imageName);
            $recipeRepository->add($recipe->getName(), $recipe->getType(), $imageName);
            $r = $recipeRepository->findOneByName($recipe->getName());
            foreach ($recipe->getIngredients() as $ingredient) {
                $ingredient->setRecipe($r);
                $ingredientRepository->save($ingredient);
            }
            foreach ($recipe->getSteps() as $step) {
                $step->setRecipe($r);
                $stepRepository->save($step);
            }
            return $this->redirectToRoute("recipe", ["id" => $r->getId()]);
        } elseif($form->isSubmitted() && !$form->isValid()) {
            foreach($form->getErrors() as $error)
                var_dump($error->getMessage());die;
        }

        return $this->render('recipe/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/recipe/{id}", name="recipe")
     */
    public function show(Request $request, RecipeRepository $recipeRepository,
        IngredientRepository $ingredientRepository, StepRepository $stepRepository): Response
    {
        $recipe = $recipeRepository->find($request->get("id"));
        $ingredients = $ingredientRepository->findByRecipe($recipe);
        foreach ($ingredients as $ingredient) {
            $recipe->addIngredient($ingredient);
        }
        $steps = $stepRepository->findByRecipe($recipe);
        foreach ($steps as $step) {
            $recipe->addStep($step);
        }
        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe
        ]);
    } 

    /**
     * @Route("/delete_recipe/{id}", name="delete_recipe")
     */
    public function deleteRecipe(Request $request, RecipeRepository $recipeRepository,
        IngredientRepository $ingredientRepository, StepRepository $stepRepository)
    {
        $recipe = $recipeRepository->find($request->get("id"));
        foreach ($recipe->getIngredients() as $ingredient) {
            $ingredientRepository->delete($ingredient);
        }
        foreach ($recipe->getSteps() as $step) {
            $stepRepository->delete($step);
        }
        $recipeRepository->delete($recipe);

        return $this->redirectToRoute('index');
    }
}
