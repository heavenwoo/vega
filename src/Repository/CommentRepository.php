<?php

namespace Vega\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Vega\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Vega\Entity\Post;
use Vega\Entity\User;

class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function findCommentsByUser(User $user)
    {
        return $this->createQueryBuilder('c')
            ->where('c.user = :user')->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function findAllCommentsQueryByPost(Post $post)
    {
        return $this->createQueryBuilder('c')
            ->select('c', 'u')
            ->join('c.user', 'u')
            ->where('c.post = :post')->setParameter('post', $post)
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery();
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('c')
            ->where('c.something = :value')->setParameter('value', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
