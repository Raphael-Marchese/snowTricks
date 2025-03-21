<?php

namespace App\Form;

use App\Entity\Category;
use App\Service\CategoryNameEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', ChoiceType::class, [
                'label' => 'Category',
                'choices' => array_combine(
                // Création des labels pour les choix
                    array_map(fn($enum) => $enum->name, CategoryNameEnum::cases()),
                    array_map(fn($enum) => $enum->value, CategoryNameEnum::cases())
                ),
                'placeholder' => 'Choose a group',
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
