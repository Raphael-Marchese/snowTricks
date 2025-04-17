<?php

namespace App\Form;

use App\Entity\Figure;
use App\Service\CategoryNameEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateFigureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $figureGroup = $options['figureGroup'] ?? null;

        $builder
            ->add('name')
            ->add('description')
            ->add('figureGroup', ChoiceType::class, [
                'choices' => array_combine(
                    array_map(static fn($enum) => $enum->name, CategoryNameEnum::cases()),
                    array_map(static fn($enum) => $enum->value, CategoryNameEnum::cases())
                ),
                'placeholder' => 'Choose a group',
                'required' => true,
                'mapped' => false,
                'data' => $figureGroup ? $figureGroup->name->value : null
            ])
            ->add('illustrations', CollectionType::class, [
                'entry_type' => FileType::class,
                'entry_options' => [
                    'label' => 'Illustration',
                    'required' => false,
                ],
                'mapped' =>false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'required' => false,
            ])
            ->add('videos', CollectionType::class, [
                'entry_type' => FileType::class,
                'entry_options' => [
                    'label' => 'Video',
                    'required' => false,
                ],
                'mapped' =>false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Figure::class,
        ]);
        $resolver->setDefined(['figureGroup']);
    }
}
