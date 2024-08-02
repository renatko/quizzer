<?php

namespace App\Repository;

use App\Entity\TestQuestionAnswer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TestQuestionAnswer>
 */
class TestQuestionAnswerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TestQuestionAnswer::class);
    }
}
