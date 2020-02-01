<?php

namespace Vega\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Question
 *
 * @ORM\Table(name="questions")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="Vega\Repository\QuestionRepository")
 */
class Question extends Entity
{
    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     */
    protected $subject;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $slug;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     * @Assert\Length(min=10, minMessage="question.content_too_short")
     */
    protected $content;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    protected $views;

    /**
     * @var integer
     *
     * @ORM\Column(name="answer_nums", type="integer")
     */
    protected $answerNums;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    protected $solved;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    protected $votes;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     */
    protected $user;

    /**
     * @var Answer[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Answer", mappedBy="question")
     * @ORM\OrderBy({"createdAt": "DESC"})
     */
    protected $answers;

    /**
     * @var Comment[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="question")
     * @ORM\OrderBy({"createdAt": "DESC"})
     */
    protected $comments;

    /**
     * @var Tag[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Tag", cascade={"persist"})
     * @ORM\JoinTable(name="question_tags")
     * @ORM\OrderBy({"name": "ASC"})
     */
    protected $tags;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject ?: '';
    }

    /**
     * @param string $subject
     */
    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content ?: '';
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return int
     */
    public function getViews(): int
    {
        return $this->views;
    }

    /**
     * @param int $views
     */
    public function setViews(int $views): void
    {
        $this->views = $views;
    }

    /**
     * @return int
     */
    public function getAnswerNums(): int
    {
        return $this->answerNums;
    }

    /**
     * @param int $answerNums
     */
    public function setAnswerNums(int $answerNums): void
    {
        $this->answerNums = $answerNums;
    }

    /**
     * @return bool
     */
    public function isSolved(): bool
    {
        return $this->solved;
    }

    /**
     * @param bool $solved
     */
    public function setSolved(bool $solved): void
    {
        $this->solved = $solved;
    }

    public function getVotes()
    {
        return $this->votes;
    }

    public function resetVote()
    {
        $this->votes = 0;
    }

    public function setVote(int $vote)
    {
        $this->votes = $vote;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return Answer
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    /**
     * @param Answer $answers
     */
    public function addAnswer(Answer $answer): void
    {
        $answer->setQuestion($this);
        $this->answers->add($answer);
    }

    public function removeAnswer(Answer $answer)
    {
        $this->answers->removeElement($answer);
        $answer->setQuestion(null);
    }

    /**
     * @return Tag
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    /**
     * @param array $tags
     */
    public function addTags(Tag ...$tags): void
    {
        foreach ($tags as $tag) {
            if (!$this->tags->contains($tag)) {
                $this->tags->add($tag);
            }
        }
    }

    public function removeTag(Tag $tag)
    {
        $this->tags->removeElement($tag);
    }

    /**
     * @return Collection
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * @param Comment $comments
     */
    public function addComment(Comment $comment): void
    {
        $comment->setQuestion($this);
        $this->comments->add($comment);
    }

    /**
     * @param Comment $comment
     */
    public function removeComment(Comment $comment): void
    {
        $this->comments->removeElement($comment);
        $comment->setQuestion(null);
    }
}
