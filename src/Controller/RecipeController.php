<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Form\CommentType;
use App\Form\ReplyType;
use App\Repository\CommentRepository;
use App\Repository\IngredientRepository;
use App\Repository\RecipeRepository;
use App\Repository\StepRepository;
use App\Repository\TypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Cookie;

class RecipeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(Request $request, RecipeRepository $recipeRepository, TypeRepository $typeRepository)
    {
        return $this->render("recipe/index.html.twig", [
            "types" => $typeRepository->findAll(),
            "recipes" => $recipeRepository->findAll()
        ]);
    }

    /**
     * @Route("/admin", name="admin_index")
     * @IsGranted("ROLE_ADMIN")
     */
    public function adminIndex(Request $request)
    {
        return $this->redirectToRoute('admin_recipes');
    }

    /**
     * @Route("/admin/recipes", name="admin_recipes")
     * @IsGranted("ROLE_ADMIN")
     */
    public function adminListRecipes(Request $request, RecipeRepository $recipeRepository,
        TypeRepository $typeRepository)
    {
        $recipes = $recipeRepository->findAll();
        foreach ($recipes as $recipe) {
            $recipe->setType($typeRepository->find($recipe->getType())->getName());
        }
        return $this->render('admin/list_recipes.html.twig',[
            "recipes" => $recipes
        ]);
    }

    /**
     * @Route("/admin/recipe/add", name="admin_add_recipe")
     * @IsGranted("ROLE_ADMIN")
     */
    public function add(Request $request, RecipeRepository $recipeRepository,
        IngredientRepository $ingredientRepository, StepRepository $stepRepository): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $image = $form['image']->getData();
            if($image) {
            $imageName = $recipe->getName() . "." . $image->guessExtension();
            $directory = $this->getParameter('images_directory');
            $image->move($directory, $imageName);
            }
            $recipeRepository->add($recipe->getName(), $recipe->getType(), $recipe->getDifficulty(), 
                $recipe->getPreparationTime(), $recipe->getCookingTime(), $imageName = null);
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
        }
        return $this->render('admin/form_recipe.html.twig', [
            "page_title" => "Nouvelle",
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/edit_recipe/{id}", name="admin_edit_recipe")
     */
    public function editRecipe(Request $request, RecipeRepository $recipeRepository,
    IngredientRepository $ingredientRepository, StepRepository $stepRepository)
    {
        $recipe = $recipeRepository->find($request->get("id"));
        $old_ings = $ingredientRepository->findByRecipe($recipe);
        $old_steps = $stepRepository->findByRecipe($recipe);

        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            foreach ($old_ings as $ing) {
                if(false === $recipe->getIngredients()->contains($ing)) {
                    $ingredientRepository->delete($ing);
                }
            }

            foreach ($old_steps as $step) {
                if(false === $recipe->getSteps()->contains($step)) {
                    $stepRepository->delete($step);
                }
            }

            $recipeRepository->save($recipe);
            return $this->redirectToRoute('admin_edit_recipe', ["id" => $request->get("id")]);
        }

        return $this->render('admin/form_recipe.html.twig',[
            "page_title" => "Modifier la",
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("admin/delete_recipe/{id}", name="admin_delete_recipe")
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteRecipe(Request $request, RecipeRepository $recipeRepository,
        IngredientRepository $ingredientRepository, StepRepository $stepRepository)
    {
        $recipeRepository->delete($recipeRepository->find($request->get("id")));

        return $this->redirectToRoute('admin_recipes');
    }

    /**
     * @Route("/recipes/{type}", name="list_type")
     */
    public function listType(Request $request, RecipeRepository $recipeRepository,
        TypeRepository $typeRepository)
    {
        $type = $request->get("type");
        $recipes = $recipeRepository->findByType($type);

        return $this->render('recipe/list_type.html.twig', [
            "types" => $typeRepository->findAll(),
            "type" => $type,
            "recipes" => $recipes
        ]);
    }

    /**
     * @Route("/recipe/{id}", name="recipe")
     */
    public function show(Request $request, RecipeRepository $recipeRepository,
        IngredientRepository $ingredientRepository, StepRepository $stepRepository,
            TypeRepository $typeRepository, CommentRepository $commentRepository)
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
        $nbComments = count($recipe->getComments());
        $comments = $commentRepository->findCommentsByRecipe($recipe);
        $replies = $commentRepository->findRepliesByRecipe($recipe);
        foreach ($replies as $reply) {
            $commentRepository->find($reply->getReplyTo())->addReply($reply);
        }
        $recipe->setComments($comments);
        
        $username = $request->cookies->get("username");
        $email = $request->cookies->get("email");
        $comment = new Comment();
        $comment->setRecipe($recipe);
        if($username)
            $comment->setWriter($username);
        if($email)
            $comment->setEmail($email);
        $formComment = $this->createForm(CommentType::class, $comment);
        $formComment->handleRequest($request);

        if($formComment->isSubmitted() && $formComment->isValid()) {
            if($comment->getStars() == NULL)
                $formComment->addError(new FormError("Veuillez donner une note."));
            else {
                $response = new Response();
                $cookie = new Cookie('username', $comment->getWriter() , time() + (365 * 24 * 60 * 60));
                $response->headers->setCookie($cookie);
                $cookie = new Cookie('email', $comment->getEmail() , time() + (365 * 24 * 60 * 60));
                $response->headers->setCookie($cookie);
                $response->sendHeaders();
                $commentRepository->add($comment);
                return $this->redirectToRoute('recipe', ['id' => $request->get("id")]);
            }
        }

        return $this->renderForm('recipe/show.html.twig', [
            "types" => $typeRepository->findAll(),
            'recipe' => $recipe,
            'form' => $formComment,
            'nbComments' => $nbComments
        ]);
    }

}