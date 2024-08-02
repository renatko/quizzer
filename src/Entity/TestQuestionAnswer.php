<?php

namespace App\Entity;

use App\Repository\TestQuestionAnswerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TestQuestionAnswerRepository::class)]
class TestQuestionAnswer
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "SEQUENCE")]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "text")]
    private ?string $answer = null;

    #[ORM\Column]
    private ?bool $isCorrect = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TestQuestion $question = null;

    protected ?bool $isValid = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    public function setAnswer(string $answer): static
    {
        $this->answer = $answer;

        return $this;
    }

    public function isCorrect(): ?bool
    {
        return $this->isCorrect;
    }

    public function setIsCorrect(bool $isCorrect): static
    {
        $this->isCorrect = $isCorrect;

        return $this;
    }

    public function getQuestion(): ?TestQuestion
    {
        return $this->question;
    }

    public function setQuestion(?TestQuestion $question): void
    {
        $this->question = $question;
    }

    public function getIsValid(): ?bool
    {
        return $this->isValid;
    }

    public function setIsValid(?bool $isValid): void
    {
        $this->isValid = $isValid;
    }
}
