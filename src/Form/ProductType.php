<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Supplier;
use App\Enum\PharmaceuticalFormType;
use App\Utils\ErrorMessage;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Unique;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => "Le nom du product",
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label'],
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => ErrorMessage::MESSAGE_DEFAULT])
                ],

            ])
            ->add('description', TextareaType::class, [
                'label' => "La description",
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label'],
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => ErrorMessage::MESSAGE_DEFAULT])
                ],

            ])
            ->add('unitPrice', NumberType::class, [
                'label' => "Le prix unitaire",
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label'],
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => ErrorMessage::MESSAGE_DEFAULT])
                ],

            ])
            ->add('quantityStock', NumberType::class, [
                'label' => "La quantité",
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label'],
                'required' => true,
                'mapped' => false,
                'data' => $options['quantity_default'] ?? null,
                'constraints' => [
                    new NotBlank(['message' => ErrorMessage::MESSAGE_DEFAULT]),
                ],
            ])
            ->add('dosage', TextType::class, [
                'label' => "Le dosage",
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label'],
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => ErrorMessage::MESSAGE_DEFAULT]),
                ],
            ])
            ->add('category', EntityType::class, [
                'label' => 'La catégorie',
                'class' => Category::class,
                'choice_label' => 'name',
                'label_attr' => ['class' => 'form-label'],
                'constraints' => [
                    new NotBlank(['message' => ErrorMessage::MESSAGE_DEFAULT])
                ]
            ])
            ->add('supplier', EntityType::class, [
                'label' => 'Le fournisseur',
                'class' => Supplier::class,
                'choice_label' => 'fullname',
                'label_attr' => ['class' => 'form-label'],
                'constraints' => [
                    new NotBlank(['message' => ErrorMessage::MESSAGE_DEFAULT])
                ]
            ])
            ->add('expirationDate', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date d\'expiration',
                'required' => false,
                'label_attr' => ['class' => 'form-label'],
            ])
            ->add('form', EnumType::class, [
                'class' => PharmaceuticalFormType::class,
                'choice_label' => fn($choice) => $choice->value,
                'label' => 'Forme pharmaceutique',
                'constraints' => [
                    new NotBlank(['message' => ErrorMessage::MESSAGE_DEFAULT])
                ]
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'quantity_default' => null,
        ]);
    }
}
