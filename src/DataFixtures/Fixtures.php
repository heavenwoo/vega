<?php

namespace Vega\DataFixtures;

ini_set('memory_limit', -1);

use Vega\Entity\Answer;
use Vega\Entity\Comment;
use Vega\Entity\Post;
use Vega\Entity\Question;
use Vega\Entity\Tag;
use Vega\Entity\User;
use Vega\Utils\Slugger;
use Faker\Factory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class Fixtures extends Fixture
{

    const TAG_NUMS = 50;

    const USER_NUMS = 100;

    const QUESTION_NUMS = 1000;

    const ANSWER_NUMS = 30;

    const POST_NUMS = 100;

    const COMMENT_NUMS = 10;

    private $tagName;

    private $faker;

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->faker = Factory::create();
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadTag($manager);
        $this->loadUser($manager);
        $this->loadQuestion($manager);
        $this->loadPosts($manager);
    }

    public function loadTag(ObjectManager $manager)
    {
        $name = [];

        for ($i = 0; $i < self::TAG_NUMS; $i++) {
            $tag = new Tag();

            do {
                $tagName = $this->faker->word;
            } while (in_array(
                $tagName,
                $name
            )); //while ($manager->getRepository(Tag::class)->findBy(['name' => $name[$i]]) != null);

            $name[$i] = $tagName;

            $tag->setName($tagName);
            $tag->setDescription($this->faker->paragraph(mt_rand(3, 5)));
            $this->addReference('tag-'.$tagName, $tag);

            $manager->persist($tag);
        }

        $this->tagName = $name;

        $manager->flush();
    }

    public function loadUser(ObjectManager $manager)
    {
        $manager->persist(
            $this->setUser(
                'heaven',
                'heaven',
                'heavenwoo@live.com',
                true,
                true
            )
        );

        foreach (range(1, self::USER_NUMS) as $i) {
            $manager->persist($this->setUser('', '', '', '', '', $i));
        }

        $manager->flush();
    }

    private function setUser($username = '', $password = '', $email = '', $enable = false, $supperAdmin = false, $i = 0)
    {
        $user = new User();
        $user->setUsername($username ?: $this->faker->userName);
        $user->setEmail($email ?: $this->faker->email);
        $user->setEnabled($enable ? true : (bool)mt_rand(0, 1));
        $user->setPassword($this->passwordEncoder->encodePassword($user, $password ?: $this->faker->word));
        $user->setRoles($supperAdmin ? ['ROLE_ADMIN'] : ['ROLE_USER']);
        $user->setCreatedAt($this->faker->dateTimeBetween('-1 year', '-10 days'));
        $user->setUpdatedAt($user->getCreatedAt());

        $this->setReference('username-'.$i, $user);

        return $user;
    }

    public function loadQuestion(ObjectManager $manager)
    {
        foreach (range(1, self::QUESTION_NUMS) as $i) {
            $question = new Question();

            $question->setSubject(implode(' ', array_map('ucfirst', $this->faker->words(mt_rand(3, 5)))));
            $question->setSlug(Slugger::slugify($question->getSubject()));
            $question->setContent($this->faker->paragraph(mt_rand(6, 10)));
            $question->setViews(mt_rand(0, 10000));
            $question->setVote(mt_rand(0, 10000));
            $question->setCreatedAt($this->faker->dateTimeBetween('-1 year', '-10 days'));
            $question->setUser($this->getReference('username-'.mt_rand(0, self::USER_NUMS)));
            $question->addTags(...$this->getRandomTags());

            $this->addAnswers($manager, $question);

            $this->addComments($manager, $question);

            $manager->persist($question);
        }

        $manager->flush();
    }

    private function addAnswers(ObjectManager $manager, Question $question)
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
                $answer->setUser($this->getReference('username-'.mt_rand(0, self::USER_NUMS)));
                $answer->setCreatedAt($this->faker->dateTimeBetween($question->getCreatedAt(), 'now'));

                $this->addComments($manager, $answer);

                $question->addAnswer($answer);

                $manager->persist($answer);
            }
        } else {
            $question->setSolved(false);
        }
    }

    private function loadPosts(ObjectManager $manager)
    {
        foreach (range(1, mt_rand(1, self::POST_NUMS)) as $i) {
            $post = new Post();

            $post->setSubject(implode(' ', array_map('ucfirst', $this->faker->words(mt_rand(3, 5)))));
            $post->setSlug(Slugger::slugify($post->getSubject()));
            $post->setContent($this->faker->paragraph(mt_rand(6, 10)));
            $post->setViews(mt_rand(0, 10000));
            $post->setCreatedAt($this->faker->dateTimeBetween('-1 year', '-10 days'));
            $post->setUser($this->getReference('username-'.mt_rand(0, self::USER_NUMS)));
            $post->addTags(...$this->getRandomTags());

            $this->addComments($manager, $post);

            $manager->persist($post);
        }

        $manager->flush();
    }

    private function addComments(ObjectManager $manager, $entity)
    {
        foreach (range(1, mt_rand(1, self::COMMENT_NUMS)) as $i) {
            $comment = new Comment();

            $comment->setContent($this->faker->paragraph(mt_rand(1, 3)));
            $comment->setCreatedAt($this->faker->dateTimeBetween($entity->getCreatedAt(), 'now'));
            $comment->setUser($this->getReference('username-'.mt_rand(0, self::USER_NUMS)));

            $entity->addComment($comment);

            $manager->persist($comment);
        }
    }

    private function getRandomTags(): array
    {
        $tagNames = $this->tagName;
        shuffle($tagNames);
        $selectedTags = array_slice($tagNames, 0, mt_rand(2, 5));

        return array_map(
            function ($tagName) {
                return $this->getReference('tag-'.$tagName);
            },
            $selectedTags
        );
    }
}
