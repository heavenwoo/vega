<?php

namespace Vega\Controller;

use Faker\Factory;
use Vega\Entity\Answer;
use Vega\Entity\Comment;
use Vega\Entity\Post;
use Vega\Entity\Question;
use Vega\Entity\Tag;
use Vega\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Component\HttpFoundation\Response;
use Vega\Utils\Slugger;

class IndexController extends Controller
{
    const TAG_NUMS = 50;

    const USER_NUMS = 100;

    const QUESTION_NUMS = 100;

    const ANSWER_NUMS = 30;

    const POST_NUMS = 100;

    const COMMENT_NUMS = 10;

    private $em;

    private $faker;

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

    /**
     * @Route("/load", name="load")
     */
    public function load()
    {
        $this->em = $this->getDoctrine()->getManager();
        $this->faker = Factory::create();

        $this->loadQuestion();
        $this->loadPosts();

        return $this->redirectToRoute('question_list');
    }

    private function getRandomTags()
    {
        $tags = $this->getDoctrine()->getRepository(Tag::class)->findAll();

        shuffle($tags);

        return array_slice($tags, 0, mt_rand(2, 5));
    }

    private function getRandomUser()
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        return $users[mt_rand(0, (count($users) - 1))];
    }

    public function loadQuestion()
    {
        foreach (range(1, self::QUESTION_NUMS) as $i) {
            $question = new Question();

            $question->setSubject(implode(' ', array_map('ucfirst', $this->faker->words(mt_rand(3, 5)))));
            $question->setSlug(Slugger::slugify($question->getSubject()));
            $question->setContent($this->faker->paragraphs(mt_rand(6, 50), true));
            $question->setViews(mt_rand(0, 10000));
            $question->setVote(mt_rand(0, 10000));
            $question->setCreatedAt($this->faker->dateTimeBetween('-1 year', '-10 days'));
            $question->setUser($this->getRandomUser());
            $question->addTag(...$this->getRandomTags());

            $this->addAnswers($question);

            $this->addComments($question);

            $this->em->persist($question);
        }

        $this->em->flush();
    }

    private function addAnswers(Question $question)
    {
        $answerNums = mt_rand(0, self::ANSWER_NUMS);
        $question->setAnswerNums($answerNums);

        if ($answerNums > 0) {
            $question->setSolved((bool)mt_rand(0, 1));
            $isBestId = $question->isSolved() ? mt_rand(1, $answerNums) : 0;

            foreach (range(1, $answerNums) as $i) {
                $answer = new Answer();

                $answer->setContent($this->faker->paragraph(mt_rand(1, 3)));
                $answer->setBest($isBestId == $i);
                $answer->setVote(mt_rand(0, 100));
                $answer->setUser($this->getRandomUser());
                $answer->setCreatedAt($this->faker->dateTimeBetween($question->getCreatedAt(), 'now'));

                $this->addComments($answer);

                $question->addAnswer($answer);

                $this->em->persist($answer);
            }
        } else {
            $question->setSolved(false);
        }
    }

    private function loadPosts()
    {
        foreach (range(1, mt_rand(1, self::POST_NUMS)) as $i) {
            $post = new Post();

            $post->setSubject(implode(' ', array_map('ucfirst', $this->faker->words(mt_rand(3, 5)))));
            $post->setSlug(Slugger::slugify($post->getSubject()));
            $post->setContent($this->faker->paragraphs(mt_rand(6, 50), true));
            $post->setViews(mt_rand(0, 10000));
            $post->setCreatedAt($this->faker->dateTimeBetween('-1 year', '-10 days'));
            $post->setUser($this->getRandomUser());
            $post->addTag(...$this->getRandomTags());

            $this->addComments($post);

            $this->em->persist($post);
        }

        $this->em->flush();
    }

    private function addComments($entity)
    {
        foreach (range(1, mt_rand(1, self::COMMENT_NUMS)) as $i) {
            $comment = new Comment();

            $comment->setContent($this->faker->paragraph(mt_rand(1, 3)));
            $comment->setCreatedAt($this->faker->dateTimeBetween($entity->getCreatedAt(), 'now'));
            $comment->setUser($this->getRandomUser());

            $entity->addComment($comment);

            $this->em->persist($comment);
        }
    }
}