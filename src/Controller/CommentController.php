<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Recipe;
use App\Form\ReplyType;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Cookie;

class CommentController extends AbstractController
{
    
    /**
     * @Route("reply_to_comment/{id}", name="reply_to_comment")
     */
    public function replyToComment(Request $request, CommentRepository $commentRepository)
    {
        $username = $request->cookies->get("username");
        $email = $request->cookies->get("email");
        $id = $request->get("id");
        $reply_to = $commentRepository->find($id);
        $comment = new Comment();
        $comment->setReplyTo($id);
        if($username)
            $comment->setWriter($username);
        if($email)
            $comment->setEmail($email);
        $comment->setRecipe($reply_to->getRecipe());
        $replyForm = $this->createForm(ReplyType::class, $comment);
        $replyForm->handleRequest($request);

        if($replyForm->isSubmitted() && $replyForm->isValid()) {
            $response = new Response();
            $cookie = new Cookie('username', $comment->getWriter() , time() + (365 * 24 * 60 * 60));
            $response->headers->setCookie($cookie);
            $cookie = new Cookie('email', $comment->getEmail() , time() + (365 * 24 * 60 * 60));
            $response->headers->setCookie($cookie);
            $response->sendHeaders();
            $commentRepository->add($comment);
            return $this->redirectToRoute('recipe', ["id" => $reply_to->getRecipe()->getId()]);
        }   

        return $this->renderForm('comment/reply.html.twig', [
            "replyForm" => $replyForm,
            "id" => $id
        ]);
    }


}
