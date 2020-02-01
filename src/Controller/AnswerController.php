<?php

namespace Vega\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Vega\Entity\Answer;
use Vega\Entity\Question;
use Vega\Form\AnswerType;

/**
 * Class AnswerController
 *
 * @Route("/answer")
 *
 * @package Vega\Controller
 */
class AnswerController extends Controller
{
    /**
     * @Route("/create/{id}", requirements={"id": "\d+"}, name="answer_create", methods={"POST"})
     *
     * @param Request $request
     * @param Question $question
     * @return Response
     */
    public function create(Request $request, Question $question): Response
    {
        $answer = new Answer();
        $answer->setUser($this->getUser());
        $question->addAnswer($answer);

        $form = $this->createForm(AnswerType::class, $answer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $answer->setQuestion($question);
            $answer->setBest(false);

            $em = $this->getDoctrine()->getManager();
            $em->persist($answer);
            $em->flush();

            $this->addFlash('success', 'answer.created');

            return $this->redirectToRoute('question_show', ['id' => $question->getId(), 'slug' => $question->getSlug()]);
        }

        return $this->render('answer/answer_form_error.html.twig', [
            'answer' => $answer,
            'question' => $question,
        ]);
    }
}
