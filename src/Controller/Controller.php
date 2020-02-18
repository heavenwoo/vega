<?php

namespace Vega\Controller;

use Vega\Entity\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseController;

class Controller extends BaseController
{
    /**
     * @param Entity $entity
     * @param int    $amount
     */
    protected function incrementView(Entity $entity, int $amount = 1)
    {
        $entity->increment('views', $amount);
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();
    }

    /**
     * @param Entity $entity
     * @param int    $amount
     */
    protected function incrementVote(Entity $entity, int $amount = 1)
    {
        $entity->increment('votes', $amount);
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();
    }

    /**
     * @param Entity $entity
     * @param int    $amount
     */
    protected function decrementVote(Entity $entity, int $amount = 1)
    {
        $entity->decrement('votes', $amount);
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();
    }
}
