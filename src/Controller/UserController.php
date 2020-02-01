<?php

namespace Vega\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Vega\Entity\Answer;
use Vega\Entity\Post;
use Vega\Entity\Question;
use Vega\Entity\User;
use Vega\Form\UserType;
use Vega\Repository\AnswerRepository;
use Vega\Repository\CommentRepository;
use Vega\Repository\PostRepository;
use Vega\Repository\QuestionRepository;
use Vega\Repository\UserRepository;

/**
 * Class UserController
 *
 * @Route("/user")
 *
 * @package Vega\Controller
 */
class UserController extends Controller
{
    /**
     * @Route("/list", name="user_list")
     */
    public function list(UserRepository $userRepository)
    {
        return $this->render('user/list.html.twig', [
            'settings' => $this->getSettings(),
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/register", name="user_register")
     * @param Request $request
     * @param UserRepository $userRepository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function register(Request $request, UserRepository $userRepository)
    {
        $user = new User();
        $form = $this->createForm(UserType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($userRepository->findOneBy(['username' => $user->getUsername()])) {
                $this->addFlash('error', 'user.register.user_exist');

                return $this->redirectToRoute('user_register');
            }

            $user->setEnabled(true);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('index');
        }

        return $this->render('user/register.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{name}", name="user_show")
     */
    public function show(
        User $user,
        QuestionRepository $questionRepository,
        PostRepository $postRepository,
        AnswerRepository $answerRepository
    ) {
        return $this->render('user/show.html.twig', [
            'settings' => $this->getSettings(),
            'questions' => $questionRepository->findQuestionsByUser($user),
            'posts' => $postRepository->findPostsByUser($user),
            'answers' => $answerRepository->findAnswersByUser($user),
        ]);
    }

    /**
     * @Route("/{name}/edit", name="user_edit")
     * @Security("has_role('ROLE_USER')")
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * @Route("/delete/{username}", name="user_delete", methods={"GET"})
     * Security("is_granted('delete', user)")
     */
    public function delete(
        User $user,
        QuestionRepository $questionRepository,
        AnswerRepository $answerRepository,
        PostRepository $postRepository
    ) {
        $em = $this->getDoctrine()->getManager();

//        $questions = $questionRepository->findQuestionsByUser($user);
//        /** @var Question $question */
//        foreach ($questions as $question) {
//            $question->getAnswers()->clear();
//            $question->getComments()->clear();
//            $question->getTags()->clear();
//            dump($question);
//            $em->remove($question);
//        }

        $answers = $answerRepository->findAnswersByUser($user);
        /** @var Answer $answer */
        foreach ($answers as $answer) {
            $answer->getComments()->clear();
            $em->remove($answer);
        }

//        $posts = $postRepository->findPostsByUser($user);
//        /** @var Post $post */
//        foreach ($posts as $post) {
//            $post->getComments()->clear();
//            $post->getTags()->clear();
//            $em->remove($post);
//        }

        $em->flush();
    }
}
