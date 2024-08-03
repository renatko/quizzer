<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class QuestionnaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $questions = $options['questions'];

        foreach ($questions as $questionId => $question) {
            $builder->add($questionId, ChoiceType::class, [
                'inherit_data' => false,
                'choices' => array_flip($question['answers']),
                'label' => $question['question'],
                'choice_attr' => fn ($answerId) => [
                    'class' => "question-answer answer-{$answerId}",
                ],
                'expanded' => true,
                'multiple' => true,
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'questions' => [],
        ]);

        $resolver->setAllowedTypes('questions', 'array');
    }
}
