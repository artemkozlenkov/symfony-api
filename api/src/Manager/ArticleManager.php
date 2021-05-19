<?php

namespace Webapp\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Webapp\Entity\Article;

class ArticleManager
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function create(Article $data)
    {
        $this->em->transactional(function (EntityManagerInterface $manager) use ($data) {
            $manager->persist($data);
        });
    }
}
