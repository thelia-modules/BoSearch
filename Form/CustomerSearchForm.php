<?php

namespace BoSearch\Form;

use BoSearch\BoSearch;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Thelia\Form\BaseForm;

/**
 * Class CustomerSearchForm
 * @package BoSearch\Form
 * @author Etienne Perriere
 */
class CustomerSearchForm extends BaseForm
{
    const CUSTOMER_FORM_NAME = 'customer-search-form';

    /**
     * @return null
     */
    protected function buildForm()
    {
        $this->formBuilder
            ->add(
                'company',
                TextType::class,
                [
                    'label' => $this->translator->trans('Company', [], BoSearch::DOMAIN_NAME),
                    'label_attr' => ['for' => 'company'],
                    'required' => false
                ]
            )->add(
                'lastname',
                TextType::class,
                [
                    'label' => $this->translator->trans('Lastname', [], BoSearch::DOMAIN_NAME),
                    'label_attr' => ['for' => 'lastname'],
                    'required' => false
                ]
            )->add(
                'firstname',
                TextType::class,
                [
                    'label' => $this->translator->trans('Firstname', [], BoSearch::DOMAIN_NAME),
                    'label_attr' => ['for' => 'firstname'],
                    'required' => false
                ]
            )->add(
                'email',
                TextType::class,
                [
                    'label' => $this->translator->trans('Email', [], BoSearch::DOMAIN_NAME),
                    'label_attr' => ['for' => 'email'],
                    'required' => false
                ]
            )->add(
                'subscriptionDateMin',
                DateType::class,
                [
                    "label" => $this->translator->trans("From", [], BoSearch::DOMAIN_NAME),
                    "label_attr" => ["for" => "subscriptionDateMin"],
                    "required" => false,
                    "input"  => "datetime",
                    "widget" => "single_text",
//                    "format" => "dd/MM/yyyy"
                ]
            )->add(
                'subscriptionDateMax',
                DateType::class,
                [
                    'label' => $this->translator->trans('To', [], BoSearch::DOMAIN_NAME),
                    'label_attr' => ['for' => 'subscriptionDateMax'],
                    'required' => false,
                    "input"  => "datetime",
                    "widget" => "single_text",
//                    "format" => "dd/MM/yyyy"
                ]
            );
    }

    public static function getName()
    {
        return self::CUSTOMER_FORM_NAME;
    }
}