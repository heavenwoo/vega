<?php

namespace Vega\Controller;

use Vega\Entity\Answer;
use Vega\Entity\Comment;
use Vega\Entity\Entity;
use Vega\Entity\Question;
use Vega\Form\AnswerType;
use Vega\Form\CommentType;
use Vega\Form\QuestionType;
use Vega\Repository\AnswerRepository;
use Vega\Repository\QuestionRepository;
use Vega\Repository\TagRepository;
use Vega\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vega\Utils\Slugger;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class QuestionController
 *
 * @Cache(maxage="1", public=true)
 * @Route("/question")
 * @package Vega\Controller
 */
class QuestionController extends Controller
{
    /**
     * @Route("/list", name="question_list", methods={"GET"})
     *
     * @param QuestionRepository $questionRepository
     * @param Request $request
     */
    public function list(QuestionRepository $questionRepository, Request $request)
    {
        //
    }

    /**
     * @Route("/{id}/{slug}", name="question_show", requirements={"id": "\d+"}, methods={"GET"})
     *
     * @param int $id
     * @param QuestionRepository $questionRepository
     * @param AnswerRepository $answerRepository
     * @param TagRepository $tagRepository
     * @param Request $request
     * @return Response|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function show(int $id, QuestionRepository $questionRepository, AnswerRepository $answerRepository, TagRepository $tagRepository, Request $request, PaginatorInterface $paginator): Response
    {
        /** @var Question $question */
        $question = $questionRepository->getQuestionById($id);

        if (null == $question) {
            return $this->redirectToRoute('question_index');
        }

        $answers = $paginator->paginate(
            $answerRepository->findAllAnswersQueryByQuestion($question),
            $request->query->getInt('page', 1),
            10
        );

        $answer = new Answer();
        $comment = new Comment();
        $answerForm = $this->createForm(AnswerType::class, $answer);
        $commentForm = $this->createForm(CommentType::class, $comment);

        // views number add 1
        $this->incrementView($question);

        return $this->render("question/show.html.twig", [
            'question' => $question,
            'answers' => $answers,
            'tags' => $tagRepository->findBy([], null, 50),
            'answerForm' => $answerForm->createView(),
            'commentForm' => $commentForm->createView(),
        ]);
    }

    /**
     * @Route("/create", name="question_create", methods={"GET", "POST"})
     *
     * @Security("has_role('ROLE_USER')")
     */
    public function create(Request $request): Response
    {
        $question = new Question();
        $question->setUser($this->getUser());
        $form = $this->createForm(QuestionType::class, $question);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $question->setCreatedAt(new \DateTime());
            $question->setSlug(Slugger::slugify($question->getSubject()));
            $question->setViews(0);
            $question->setAnswerNums(0);
            $question->setSolved(false);
            $question->setVote(0);

            $em = $this->getDoctrine()->getManager();
            $em->persist($question);
            $em->flush();

            $this->addFlash('success', 'question.created');

            return $this->redirectToRoute('question_show', ['id' => $question->getId(), 'slug' => $question->getSlug()]);
        }

        return $this->render("question/create.html.twig", [
            'question' => $question,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/edit/{id}", name="question_edit", requirements={"id": "\d+"}, methods={"GET", "POST"})
     *
     * @param Request $request
     * @param Question $question
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Exception
     */
    public function edit(Request $request, Question $question)
    {
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $question->setUpdatedAt(new \DateTime());
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'question.updated');

            return $this->redirectToRoute('question_show', ['id' => $question->getId()]);
        }

        return $this->render("question/create.html.twig", [
            'question' => $question,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="question_delete", requirements={"id": "\d+"}, methods={"GET"})
     * @Security("has_role('ROLE_USER')")
     * Security("is_granted('delete', question)")
     *
     * @param Question $question
     */
    public function delete(Request $request, Question $question)
    {
        if (
            $this->getUser()->getUsername() != $question->getUser()->getUsername() ||
            $this->isGranted('ROLE_ADMIN')
        ) {
            $this->addFlash('error', 'question.no_permission_delete');

            return $this->redirectToRoute('question_show', [
                'id' => $question->getId(),
                'slug' => $question->getSlug(),
            ]);
        }
        
        $question->getTags()->clear();
        $question->getAnswers()->clear();
        $question->getComments()->clear();

        $em = $this->getDoctrine()->getManager();
        $em->remove($question);
        $em->flush();

        $this->addFlash('success', 'question.deleted');

        return $this->redirectToRoute('question_list');
    }
}
