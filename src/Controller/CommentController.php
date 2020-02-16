<?php

namespace Vega\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Vega\Entity\Comment;
use Vega\Entity\Post;
use Vega\Form\CommentType;

/**
 * Class CommentController
 *
 * @Route("/comment", name="comment_")
 *
 * @package Vega\Controller
 */
class CommentController extends Controller
{

    /**
     * @Route("/create/{id}", requirements={"id": "\d+"}, name="create", methods={"POST"})
     *
     * @param Request $request
     * @param Post    $post
     *
     * @return Response
     */
    public function create(Request $request, Post $post): Response
    {
        $comment = new Comment();
        $comment->setUser($this->getUser());
        $post->addComment($comment);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setPost($post);

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'answer.created');

            return $this->redirectToRoute('post_show', ['id' => $post->getId(), 'slug' => $post->getSlug()]);
        }

        return $this->render(
            'answer/answer_form_error.html.twig',
            [
                'comment'  => $comment,
                'question' => $post,
            ]
        );
    }
}
