<?php

namespace BoSearch\Form;

use BoSearch\BoSearch;
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

    public function getName()
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
            $categoriesArray[$category->getId()] = $category->getTitle();
        }

        $this->formBuilder
            ->add(
                'product_id',
                'integer',
                [
                    'label' => $this->translator->trans('Product ID', [], BoSearch::DOMAIN_NAME),
                    'label_attr' => ['for' => 'product_id'],
                    'required' => false
                ]
            )->add(
                'ref',
                'text',
                [
                    'label' => $this->translator->trans('Product reference', [], BoSearch::DOMAIN_NAME),
                    'label_attr' => ['for' => 'ref'],
                    'required' => false
                ]
            )->add(
                'category',
                'choice',
                [
                    'choices' =>  $categoriesArray,
                    'multiple' => true,
                    'label' => $this->translator->trans('Category', [], BoSearch::DOMAIN_NAME),
                    'label_attr' => ['for' => 'category'],
                    'required' => false
                ]
            )->add(
                'is_visible',
                'choice',
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
                'choice',
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
                'choice',
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
                'number',
                [
                    'label' => $this->translator->trans('Minimum stock', [], BoSearch::DOMAIN_NAME),
                    'label_attr' => ['for' => 'stock_min'],
                    'required' => false
                ]
            )->add(
                'stock_max',
                'number',
                [
                    'label' => $this->translator->trans('Maximum stock', [], BoSearch::DOMAIN_NAME),
                    'label_attr' => ['for' => 'stock_max'],
                    'required' => false
                ]
            )->add(
                'page',
                'number',
                [
                    'required' => false
                ]
            );
    }
}
