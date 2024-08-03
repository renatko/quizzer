<?php

namespace App\Entity;

use App\Repository\TestQuestionRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TestQuestionRepository::class)]
class TestQuestion
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    private ?string $question = null;

    #[ORM\OneToMany(
        targetEntity: TestQuestionAnswer::class,
        mappedBy: 'question',
        fetch: 'EAGER'
    )]
    private ?Collection $answers = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): static
    {
        $this->question = $question;

        return $this;
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
