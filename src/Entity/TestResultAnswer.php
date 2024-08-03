<?php

namespace App\Entity;

use App\Repository\TestResultAnswerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TestResultAnswerRepository::class)]
class TestResultAnswer
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TestQuestionAnswer $questionAnswer = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TestResult $testResult = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestionAnswer(): ?TestQuestionAnswer
    {
        return $this->questionAnswer;
    }

    public function setQuestionAnswer(?TestQuestionAnswer $questionAnswer): static
    {
        $this->questionAnswer = $questionAnswer;

        return $this;
    }

    public function getTestResult(): ?TestResult
    {
        return $this->testResult;
    }

    public function setTestResult(?TestResult $testResult): void
    {
        $this->testResult = $testResult;
    }
}
