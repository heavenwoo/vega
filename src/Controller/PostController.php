<?php

namespace Vega\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Vega\Entity\Comment;
use Vega\Repository\CommentRepository;
use Vega\Repository\PostRepository;
use Vega\Repository\TagRepository;
use Vega\Entity\Post;
use Vega\Form\CommentType;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class PostController
 *
 * @Route("/post", name="post_")
 *
 * @package Vega\Controller
 */
class PostController extends Controller
{

    /**
     * @Route("", name="list")
     */
    public function list(
        Request $request,
        PostRepository $postRepository,
        TagRepository $tagRepository,
        PaginatorInterface $paginator
    ): Response {
        $settings = $this->getParameter('settings');

        $posts = $paginator->paginate(
            $postRepository->findPostsListQuery(),
            $request->query->getInt('page', 1),
            20
        );

        $tags = $tagRepository->findBy([], null, $settings['tag_nums']);

        return $this->render(
            "post/list.html.twig",
            [
                'posts' => $posts,
                'tags'  => $tags,
            ]
        );
    }

    /**
     * @Route("/show/{id}/{slug}", name="show", requirements={"id": "\d+"}, methods={"GET"})
     *
     * @param int                                       $id
     * @param \Vega\Repository\PostRepository           $postRepository
     * @param \Vega\Repository\TagRepository            $tagRepository
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Knp\Component\Pager\PaginatorInterface   $paginator
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(
        int $id,
        PostRepository $postRepository,
        TagRepository $tagRepository,
        CommentRepository $commentRepository,
        Request $request,
        PaginatorInterface $paginator
    ): Response {
        /** @var Post $post */
        $post = $postRepository->getPostById($id);

        if (null == $post) {
            return $this->redirectToRoute('post_index');
        }

        $comments = $paginator->paginate(
            $commentRepository->findAllCommentsQueryByPost($post),
            $request->query->getInt('page', 1),
            5
        );

        $comment = new Comment();

        $commentForm = $this->createForm(CommentType::class, $comment);

        // views number add 1
        $this->incrementView($post);

        return $this->render(
            "post/show.html.twig",
            [
                'post'        => $post,
                'comments'    => $comments,
                'tags'        => $tagRepository->findBy([], null, 10),
                'commentForm' => $commentForm->createView(),
            ]
        );
    }
}
