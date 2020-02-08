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
     * @param Request            $request
     * @param QuestionRepository $questionRepository
     * @param TagRepository      $tagRepository
     *
     * @return Response
     */
    public function index(
        Request $request,
        QuestionRepository $questionRepository,
        TagRepository $tagRepository,
        PaginatorInterface $paginator
    ): Response {
        $settings = $this->getParameter('settings');

        $sort = strtolower($request->query->get('sort', null));
        $pagesize = $request->query->get('pagesize', $settings['question_nums']);

        $sort = in_array($sort, ['latest', 'hottest', 'unanswered']) ? $sort : 'latest';

        $queryName = 'find'.ucfirst($sort).'Query';

        $questions = $paginator->paginate(
            $questionRepository->$queryName(),
            $request->query->getInt('page', 1),
            $pagesize
        );

        $tags = $tagRepository->findBy([], null, $settings['tag_nums']);

        return $this->render(
            "question/index.html.twig",
            [
                'questions' => $questions,
                'tags'      => $tags,
                'sort'      => $sort,
            ]
        );
    }

    /**
     * @Route("/api", name="question_index_api", options={"sitemap"=true},
     *                methods={"GET"})
     *
     * @param Request            $request
     * @param QuestionRepository $questionRepository
     *
     * @return Response
     */
    public function api(
        Request $request,
        QuestionRepository $questionRepository
    ): Response {
        $query = $questionRepository->findLatestQuery();
        $paginator = new Paginator($query, $fetchJoinCollection = true);

        //dump($serializer->serialize($paginator->getIterator()->getArrayCopy(), 'json'));
        //return $this->json($serializer->serialize($paginator->getIterator()->getArrayCopy(), 'json'));
        return new JsonResponse(
            $this->getSerializer()->serialize(
                $paginator->getIterator()->getArrayCopy(),
                'json'
            ), 200, [], true
        );
    }

    /**
     * @return \JMS\Serializer\Serializer
     */
    private function getSerializer()
    {
        if ($this->serializer === null) {
            $this->serializer = \JMS\Serializer\SerializerBuilder::create()
                ->build();
        }

        return $this->serializer;
    }
}
