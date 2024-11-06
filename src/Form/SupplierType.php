<?php

namespace App\Form;

use App\Entity\Supplier;
use App\Utils\ErrorMessage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class SupplierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullname', TextType::class, [
                'label' => "Le nom du fournisseur",
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label'],
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => ErrorMessage::MESSAGE_DEFAULT])
                ],

            ])
            ->add('address', TextType::class, [
                'label' => "L'adresse",
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label'],
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => ErrorMessage::MESSAGE_DEFAULT])
                ],

            ])
            ->add('phone', TextType::class, [
                'label' => "Le téléphone",
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'form-label'],
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => ErrorMessage::MESSAGE_DEFAULT]),
                ],

            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Supplier::class,
        ]);
    }
}
