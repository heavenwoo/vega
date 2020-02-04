<?php

namespace Vega\Controller;

use Doctrine\ORM\QueryBuilder;
use Vega\Entity\Setting;
use Vega\Entity\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Doctrine\Common\Collections\ArrayCollection;

class Controller extends BaseController
{

    public function __construct()
    {
        //dump(realpath(__DIR__ . '/../../'));
        if (!is_file(dirname(getcwd()) . '/installed.lock')) {
            //dump('uninstalled');
        }
    }

    /**
     * @Cache(expires="1 week")
     * @return array
     */
    public function getSettings(): array
    {
        $settings = $this->getDoctrine()->getRepository(Setting::class)->findAll();

        /* @var Setting $setting*/
        foreach ($settings as $setting) {
            $settingArray[$setting->getName()] = $setting->getValue();
        }

        return $settingArray;
    }

    /**
     * @param Entity $entity
     * @param int $amount
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
