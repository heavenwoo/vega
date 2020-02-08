<?php

namespace Vega\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Vega\Entity\Comment;
use Vega\Repository\PostRepository;
use Vega\Repository\TagRepository;
use Vega\Entity\Post;
use Vega\Form\CommentType;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class PostController
 *
 * @Route("/post")
 *
 * @package Vega\Controller
 */
class PostController extends Controller
{
    /**
     * @Route("", name="post_list")
     */
    public function list(Request $request, PostRepository $postRepository, TagRepository $tagRepository, PaginatorInterface $paginator): Response
    {
        $settings = $this->getParameter('settings');

        $posts = $paginator->paginate(
            $postRepository->findPostsListQuery(),
            $request->query->getInt('page', 1),
            20
        );

        $tags = $tagRepository->findBy([], null, $settings['tag_nums']);

        return $this->render("post/list.html.twig", [
            'posts' => $posts,
            'tags' => $tags,
        ]);
    }

    /**
     * @Route("/show/{id}/{slug}", name="post_show", requirements={"id": "\d+"}, methods={"GET"})
     *
     * @param int $id
     * @param PostRepository $postRepository
     * @param TagRepository $tagRepository
     * @param Request $request
     * @return Response
     */
    public function show(int $id, PostRepository $postRepository, TagRepository $tagRepository, Request $request, PaginatorInterface $paginator): Response
    {
        /** @var Post $post */
        $post = $postRepository->getPostById($id);

        if (null == $post) {
            return $this->redirectToRoute('post_index');
        }

        $comment = new Comment();

        $commentForm = $this->createForm(CommentType::class, $comment);

        // views number add 1
        $this->incrementView($post);

        return $this->render("post/show.html.twig", [
            'post' => $post,
            'tags' => $tagRepository->findBy([], null, 10),
            'commentForm' => $commentForm->createView(),
        ]);
    }
}
