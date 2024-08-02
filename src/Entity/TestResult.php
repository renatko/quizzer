<?php

namespace App\Entity;

use App\Repository\TestResultRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

#[ORM\Entity(repositoryClass: TestResultRepository::class)]
class TestResult
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "SEQUENCE")]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(
        targetEntity: TestResultAnswer::class,
        mappedBy: 'testResult',
        fetch: 'EAGER'
    )]
    private ?Collection $answers = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return ?Collection<TestQuestionAnswer>
     */
    public function getAnswers(): ?Collection
    {
        return $this->answers;
    }

    public function setAnswers(?Collection $answers): void
    {
        $this->answers = $answers;
    }
}
