<?php

namespace App\Tests\Controller;

use App\Entity\TestQuestionAnswer;
use App\Repository\TestQuestionAnswerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Field\ChoiceFormField;

class TestControllerTest extends WebTestCase
{
    protected ?KernelBrowser $client = null;
    protected ?EntityManagerInterface $em = null;

    protected ?TestQuestionAnswerRepository $answerRepository = null;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $em = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $this->answerRepository = $em->getRepository(TestQuestionAnswer::class);
    }

    public function testIndex()
    {
        $crawler = $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Test');

        $this->assertGreaterThan(0, $crawler->filter('.question-answer')->count());
    }

    public function testSubmit()
    {
        $allCorrectAnswers = $this->answerRepository->findBy(['isCorrect' => true]);
        $singleIncorrectAnswer = $this->answerRepository->findOneBy(['isCorrect' => false]);

        $allCorrectQuestions = [];
        foreach ($allCorrectAnswers as $answer) {
            if ($answer->getQuestion()->getId() != $singleIncorrectAnswer->getQuestion()->getId()) {
                $allCorrectQuestions[] = $answer->getQuestion()->getId();
            }
        }

        $crawler = $this->client->request('GET', '/');

        $form = $crawler->selectButton('Submit')->form();
        foreach ($allCorrectAnswers as $answer) {
            /**
             * @var $checkbox ChoiceFormField
             */
            foreach ($form['questionnaire'][$answer->getQuestion()->getId()] as $checkbox) {
                if ($checkbox->availableOptionValues()[0] == $answer->getId()) {
                    $checkbox->tick();
                }
            }
        }
        foreach ($form['questionnaire'][$singleIncorrectAnswer->getQuestion()->getId()] as $checkbox) {
            if ($checkbox->availableOptionValues()[0] == $singleIncorrectAnswer->getId()) {
                $checkbox->tick();
            }
        }
        $this->client->submit($form);

        $this->assertResponseRedirects();
        $this->assertMatchesRegularExpression('!/test/result/([0-9]+)!', $this->client->getResponse()->headers->get('Location'));

        $crawler = $this->client->followRedirect();
        foreach ($allCorrectQuestions as $questionId) {
            $this->assertEquals(
                1,
                $crawler->filter("#valid #question_{$questionId}")->count()
            );
        }

        $this->assertEquals(
            1,
            $crawler->filter("#invalid #question_{$singleIncorrectAnswer->getQuestion()->getId()}")->count()
        );
    }


    public function testIncompleteSubmit()
    {
        $crawler = $this->client->request('GET', '/');

        $form = $crawler->selectButton('Submit')->form();
        $this->client->submit($form);
        $this->assertEquals(
            422,
            $this->client->getResponse()->getStatusCode()
        );
    }

}
