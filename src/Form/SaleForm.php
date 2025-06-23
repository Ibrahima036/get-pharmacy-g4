<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Invoice;
use App\Entity\Sale;
use App\Entity\Supplier;
use App\Utils\ErrorMessage;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class SaleForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'choice_label' => function (Client $client) {
                    return $client->getClientInfo();
                },
                'mapped' => false,
                'label_attr' => ['class' => 'form-label'],
                'constraints' => [
                    new NotBlank(['message' => ErrorMessage::MESSAGE_DEFAULT])
                ],
                'label' => false,
                'placeholder' => 'Choisissez un client',
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sale::class,
        ]);
    }
}
