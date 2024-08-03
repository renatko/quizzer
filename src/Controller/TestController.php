<?php

namespace App\Controller;

use App\Entity\TestQuestion;
use App\Entity\TestQuestionAnswer;
use App\Entity\TestResult;
use App\Entity\TestResultAnswer;
use App\Form\QuestionnaireType;
use App\Repository\TestQuestionRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestController extends AbstractController
{
    #[Route('/', name: 'test_index')]
    public function test(TestQuestionRepository $questionRepository): Response
    {
        $form = $this->getForm($questionRepository);

        return $this->render('test/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/test/submit', name: 'test_submit', methods: ['POST'])]
    public function submit(Request $request, TestQuestionRepository $questionRepository, EntityManagerInterface $em): Response
    {
        $form = $this->getForm($questionRepository);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $answerIds = [];
            foreach ($form->getData() as $formAnswers) {
                $answerIds = array_merge($answerIds, $formAnswers);
            }
            $answerEntities = $em->getRepository(TestQuestionAnswer::class)
                ->findBy(['id' => $answerIds]);
            $testResult = new TestResult();
            foreach ($answerEntities as $answerEntity) {
                $answer = new TestResultAnswer();
                $answer->setQuestionAnswer($answerEntity);
                $answer->setTestResult($testResult);
                $em->persist($answer);
            }
            $em->persist($testResult);
            $em->flush();

            return $this->redirectToRoute('test_result', ['id' => $testResult->getId()]);
        } elseif ($form->isSubmitted() && !$form->isValid()) {
            return $this->render('test/index.html.twig', ['form' => $form], new Response(null, 422));
        }

        throw new BadRequestException();
    }

    #[Route('/test/result/{id}', name: 'test_result', methods: ['GET'])]
    public function result(TestResult $result, TestQuestionRepository $questionRepository): Response
    {
        $userAnswerIds = $result->getAnswers()
            ->map(
                fn (TestResultAnswer $answer) => $answer->getQuestionAnswer()->getId()
            );
        $questions = $questionRepository->findAll();
        $validQuestions = [];
        $invalidQuestions = [];
        foreach ($questions as $question) {
            $question = $this->validateQuestionAnswers($question, $userAnswerIds);
            $validAnswers = $question->getAnswers()->filter(
                fn (TestQuestionAnswer $answer) => true === $answer->getIsValid()
            );
            $invalidAnswers = $question->getAnswers()->filter(
                fn (TestQuestionAnswer $answer) => false === $answer->getIsValid()
            );
            if ($invalidAnswers->count() > 0 || 0 == $validAnswers->count()) {
                $invalidQuestions[] = $question;
            } else {
                $validQuestions[] = $question;
            }
        }

        return $this->render('test/result.html.twig', [
            'result' => $result,
            'userAnswerIds' => $userAnswerIds,
            'validQuestions' => $validQuestions,
            'invalidQuestions' => $invalidQuestions,
        ]);
    }

    protected function validateQuestionAnswers(TestQuestion $question, Collection $userAnswerIds): TestQuestion
    {
        foreach ($question->getAnswers() as $answer) {
            if ($userAnswerIds->contains($answer->getId())) {
                $answer->setIsValid($answer->isCorrect());
            }
        }

        return $question;
    }

    protected function getForm(TestQuestionRepository $questionRepository): FormInterface
    {
        $questions = [];
        $allQuestions = $questionRepository->findAll();
        shuffle($allQuestions);
        foreach ($allQuestions as $question) {
            $answers = [];
            $allAnswers = $question->getAnswers()->toArray();
            shuffle($allAnswers);
            foreach ($allAnswers as $answer) {
                $answers[$answer->getId()] = $answer->getAnswer();
            }
            $questions[$question->getId()] = [
                'question' => $question->getQuestion(),
                'answers' => $answers,
            ];
        }

        return $this->createForm(QuestionnaireType::class, [], ['questions' => $questions, 'action' => $this->generateUrl('test_submit')]);
    }
}
