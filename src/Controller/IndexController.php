<?php

namespace Vega\Controller;

use Vega\Repository\QuestionRepository;
use Vega\Repository\TagRepository;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Component\Pager\PaginatorInterface;

class IndexController extends Controller
{

    private $serializer = null;

    /**
     * @Route("/", name="question_index", defaults={"page": "1"},
     *             options={"sitemap"=true}, methods={"GET"})
     * @Cache(smaxage="10")
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->redirectToRoute('question_list');
    }
}