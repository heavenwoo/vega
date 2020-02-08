<?php

namespace Vega\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vega\Repository\PostRepository;
use Vega\Repository\QuestionRepository;
use Vega\Repository\TagRepository;
use Vega\Entity\Tag;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class TagController
 *
 * @Route("/tag")
 *
 * @package Vega\Controller
 */
class TagController extends Controller
{

    /**
     * @Route("/", name="tag_index")
     */
    public function index()
    {
        $tags = $this->getDoctrine()->getRepository(Tag::class)->findAll();
    }

    /**
     * @Route("/{name}", name="tag_list", requirements={"name": "\w+"})
     *
     * @param Request       $request
     * @param int           $id
     * @param TagRepository $tagRepository
     */
    public function list(
        Request $request,
        Tag $tag,
        QuestionRepository $questionRepository,
        TagRepository $tagRepository,
        PaginatorInterface $paginator
    ): Response {
        $settings = $this->getParameter('settings');

        $query = $questionRepository->findQuestionsQueryByTag($tag);

        $questions = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            20
        );

        $tags = $tagRepository->findBy([], null, $settings['tag_nums']);

        return $this->render(
            "tag/list.html.twig",
            [
                'questions' => $questions,
                'tags'      => $tags,
            ]
        );
    }

    /**
     * @Route("/{name}/post", name="tag_post_list", requirements={"name":
     *                        "\w+"})
     *
     * @param Request        $request
     * @param Tag            $tag
     * @param PostRepository $postRepository
     * @param TagRepository  $tagRepository
     */
    public function posts(
        Request $request,
        Tag $tag,
        PostRepository $postRepository,
        TagRepository $tagRepository
    ) {
        $settings = $this->getParameter('settings');

        $query = $postRepository->findPostsQueryByTag($tag);

        $paginator = $this->get('knp_paginator');

        $posts = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            20
        );

        $tags = $tagRepository->findBy([], null, $settings['tag_nums']);

        return $this->render(
            "tag/post_list.html.twig",
            [
                'posts'   => $posts,
                'tags'    => $tags,
            ]
        );
    }
}
