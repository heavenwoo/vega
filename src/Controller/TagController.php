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
 * @Route("/tag", name="tag_")
 *
 * @package Vega\Controller
 */
class TagController extends Controller
{

    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $tags = $this->getDoctrine()->getRepository(Tag::class)->findAll();
    }

    /**
     * @Route("/{name}", name="list", requirements={"name": "\w+"})
     *
     * @param Request            $request
     * @param Tag                $tag
     * @param QuestionRepository $questionRepository
     * @param TagRepository      $tagRepository
     * @param PaginatorInterface $paginator
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function list(
        Request $request,
        Tag $tag,
        QuestionRepository $questionRepository,
        TagRepository $tagRepository,
        PaginatorInterface $paginator
    ): Response {
        $settings = $this->getParameter('settings');

        $sort = strtolower($request->query->get('sort', null));

        $sort = in_array($sort, ['latest', 'hottest', 'unanswered']) ? $sort
            : 'latest';

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
                'tag_name'  => $tag->getName(),
                'sort'      => $sort,
            ]
        );
    }

    /**
     * @Route("/{name}/post", name="post_list", requirements={"name": "\w+"})
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Vega\Entity\Tag                          $tag
     * @param \Vega\Repository\PostRepository           $postRepository
     * @param \Vega\Repository\TagRepository            $tagRepository
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function posts(
        Request $request,
        Tag $tag,
        PostRepository $postRepository,
        TagRepository $tagRepository
    ): Response {
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
                'posts' => $posts,
                'tags'  => $tags,
            ]
        );
    }
}
