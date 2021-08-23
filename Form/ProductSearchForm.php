<?php

namespace BoSearch\Form;

use BoSearch\BoSearch;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Thelia\Form\BaseForm;
use Thelia\Model\CategoryQuery;

/**
 * Class ProductSearchForm
 * @package BoSearch\Form
 * @author Etienne Perriere <eperriere@openstudio.fr>
 */
class ProductSearchForm extends BaseForm
{
    const PRODUCT_FORM_NAME = 'product-search-form';

    public static function getName()
    {
        return self::PRODUCT_FORM_NAME;
    }

    protected function buildForm()
    {
        // Initialize payment modules
        $categoriesArray = [];
        $categoriesList = (new CategoryQuery)->find();

        /** @var \Thelia\Model\Category $category */
        foreach ($categoriesList as $category) {
            $categoriesArray[$category->getTitle()] = $category->getId();
        }

        $this->formBuilder
            ->add(
                'product_id',
                IntegerType::class,
                [
                    'label' => $this->translator->trans('Product ID', [], BoSearch::DOMAIN_NAME),
                    'label_attr' => ['for' => 'product_id'],
                    'required' => false
                ]
            )->add(
                'ref',
                TextType::class,
                [
                    'label' => $this->translator->trans('Product reference', [], BoSearch::DOMAIN_NAME),
                    'label_attr' => ['for' => 'ref'],
                    'required' => false
                ]
            )->add(
                'category',
                ChoiceType::class,
                [
                    'choices' =>  $categoriesArray,
                    'multiple' => true,
                    'label' => $this->translator->trans('Category', [], BoSearch::DOMAIN_NAME),
                    'label_attr' => ['for' => 'category'],
                    'required' => false
                ]
            )->add(
                'is_visible',
                ChoiceType::class,
                [
                    'choices' => [
                        'all' => 'all',
                        'yes' => 'yes',
                        'no' => 'no'
                    ],
                    'multiple' => false,
                    'label' => $this->translator->trans('Visible', [], BoSearch::DOMAIN_NAME),
                    'label_attr' => ['for' => 'is_visible'],
                    'required' => false
                ]
            )->add(
                'is_new',
                ChoiceType::class,
                [
                    'choices' => [
                        'all' => 'all',
                        'yes' => 'yes',
                        'no' => 'no'
                    ],
                    'multiple' => false,
                    'label' => $this->translator->trans('New', [], BoSearch::DOMAIN_NAME),
                    'label_attr' => ['for' => 'is_new'],
                    'required' => false
                ]
            )->add(
                'is_promo',
                ChoiceType::class,
                [
                    'choices' => [
                        'all' => 'all',
                        'yes' => 'yes',
                        'no' => 'no'
                    ],
                    'multiple' => false,
                    'label' => $this->translator->trans('Sale', [], BoSearch::DOMAIN_NAME),
                    'label_attr' => ['for' => 'is_promo'],
                    'required' => false
                ]
            )->add(
                'stock_min',
                NumberType::class,
                [
                    'label' => $this->translator->trans('Minimum stock', [], BoSearch::DOMAIN_NAME),
                    'label_attr' => ['for' => 'stock_min'],
                    'required' => false
                ]
            )->add(
                'stock_max',
                NumberType::class,
                [
                    'label' => $this->translator->trans('Maximum stock', [], BoSearch::DOMAIN_NAME),
                    'label_attr' => ['for' => 'stock_max'],
                    'required' => false
                ]
            )->add(
                'page',
                NumberType::class,
                [
                    'required' => false
                ]
            );
    }
}
