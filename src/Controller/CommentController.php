<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Recipe;
use App\Form\CommentType;
use App\Form\ReplyType;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

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

    /**
     * @Route("/admin/comments", name="admin_comments")
     * @IsGranted("ROLE_ADMIN")
     */
    public function adminListComments(Request $request, CommentRepository $commentRepository)
    {
        $comments = $commentRepository->findAllComments();
        $replies = $commentRepository->findAllReplies();
        foreach ($replies as $reply) {
            $commentRepository->find($reply->getReplyTo())->addReply($reply);
        }
        return $this->render('admin/list_comments.html.twig', [
            "comments" => $comments
        ]);
    }
    

    /**
     * @Route("admin/edit_comment/{id}", name="admin_edit_comment")
     * @IsGranted("ROLE_ADMIN")
     */
    public function adminEditComment(Request $request, CommentRepository $commentRepository)
    {
        $comment = $commentRepository->find($request->get("id"));
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $commentRepository->save($comment);
            return $this->redirectToRoute('admin_comments');
        }   

        return $this->renderForm('modals/edit_comment.html.twig', [
            "form" => $form,
            "id" => $comment->getId()
        ]);
    }
    
    /**
     * @Route("admin/reply_to_comment/{id}", name="admin_reply_to_comment")
     * @IsGranted("ROLE_ADMIN")
     */
    public function adminReplyToComment(Request $request, CommentRepository $commentRepository, MailerInterface $mailer)
    {
        $id = $request->get("id");
        $reply_to = $commentRepository->find($id);
        $comment = new Comment();
        $comment->setWriter($this->getUser()->getUsername());
        $comment->setEmail($this->getParameter("app_gmail"));
        $comment->setReplyTo($id);
        $comment->setRecipe($reply_to->getRecipe());
        $form = $this->createForm(ReplyType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $commentRepository->add($comment);
            $this->sendEmail($mailer, $reply_to, $comment);
            return $this->redirectToRoute('admin_comments');
        }   

        return $this->renderForm('modals/admin_reply.html.twig', [
            "form" => $form,
            "id" => $id
        ]);
    }
    
    /**
     * @Route("admin/delete_comment/{id}", name="admin_delete_comment")
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteComment(Request $request, CommentRepository $commentRepository)
    {
        $commentRepository->deleteRepliesOf($request->get("id"));
        $commentRepository->delete($commentRepository->find($request->get("id")));

        return $this->redirectToRoute('admin_comments');
    }

    /**
     * @Route("/email")
     */
    public function sendEmail(MailerInterface $mailer, Comment $comment, Comment $reply)
    {
        $email = (new TemplatedEmail())
            ->from($this->getParameter("app_gmail"))
            ->to($comment->getEmail())
            ->subject("Quelqu'un a répondu à votre commentaire")
            ->htmlTemplate('emails/reply.html.twig')
            ->context(["comment" => $comment, "reply" => $reply]);

        $mailer->send($email);
    }
}
