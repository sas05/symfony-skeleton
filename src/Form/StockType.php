<?php

namespace App\Form;

use App\Entity\Stock;
use App\Entity\Company;
use App\Entity\Type;
use App\Entity\ExchangeMarket;
use App\Form\Type\CompanyInputType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Defines the form used to create and manipulate stock.
 *
 * @author Saeed Ahmed <saeed.sas@gmail.com>
 */
class StockType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // For the full reference of options defined by each form field type
        // see https://symfony.com/doc/current/reference/forms/types.html

        // By default, form fields include the 'required' attribute, which enables
        // the client-side form validation. This means that you can't test the
        // server-side validation errors from the browser. To temporarily disable
        // this validation, set the 'required' attribute to 'false':
        // $builder->add('name', null, ['required' => false, ...]);

        $builder
            ->add('company', EntityType::class, [
                'class' => Company::class,
                'choice_label' => function ($company) {
                    return $company->getName();
                },
                'mapped' => false,
                'label' => 'label.company',
                'required' => true,
            ])
            ->add('exchangeMarket', EntityType::class, [
                'class' => ExchangeMarket::class,
                'choice_label' => function ($exchangeMarket) {
                    return $exchangeMarket->getName();
                },
                'mapped' => false,
                'label' => 'label.exchangeMarket',
                'required' => true,
            ])
            ->add('type', EntityType::class, [
                'class' => Type::class,
                'choice_label' => function ($type) {
                    return $type->getName();
                },
                'mapped' => false,
                'label' => 'label.type',
                'required' => true,
            ])
            ->add('price', null, [
                'label' => 'label.price',
                'required' => true,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Stock::class,
        ]);
    }
}
