<?php

namespace BoSearch\Form;

use BoSearch\BoSearch;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Thelia\Form\BaseForm;
use Thelia\Model\ModuleQuery;
use Thelia\Model\OrderStatusQuery;

/**
 * Class OrderSearchForm
 * @package BoSearch\Form
 * @author Etienne Perriere
 */
class OrderSearchForm extends BaseForm
{
    const ORDER_FORM_NAME = 'order-search-form';

    /**
     * @return null
     */
    protected function buildForm()
    {
        // Initialize status
        $statusArray = [];
        $statusList = (new OrderStatusQuery)->find();

        /** @var \Thelia\Model\OrderStatus $status */
        foreach ($statusList as $status) {
            $statusArray[$status->getTitle()] = $status->getId();
        }

        // Initialize payment modules
        $paymentModuleArray = [];
        $paymentModuleList = (new ModuleQuery)->findByType(3);

        /** @var \Thelia\Model\Module $paymentModule */
        foreach ($paymentModuleList as $paymentModule) {
            $paymentModuleArray[$paymentModule->getTitle()] = $paymentModule->getId();
        }

        $this->formBuilder
            ->add(
                'ref',
                TextType::class,
                [
                    'label' => $this->translator->trans('Order reference', [], BoSearch::DOMAIN_NAME),
                    'label_attr' => ['for' => 'ref'],
                    'required' => false
                ]
            )->add(
                'invoiceRef',
                TextType::class,
                [
                    'label' => $this->translator->trans('Invoice reference', [], BoSearch::DOMAIN_NAME),
                    'label_attr' => ['for' => 'invoiceRef'],
                    'required' => false
                ]
            )->add(
                'company',
                TextType::class,
                [
                    'label' => $this->translator->trans('Company', [], BoSearch::DOMAIN_NAME),
                    'label_attr' => ['for' => 'company'],
                    'required' => false
                ]
            )->add(
                'customer',
                TextType::class,
                [
                    'label' => $this->translator->trans('Customer', [], BoSearch::DOMAIN_NAME),
                    'label_attr' => ['for' => 'customer'],
                    'required' => false
                ]
            )->add(
                'paymentModule',
                ChoiceType::class,
                [
                    'choices' =>  $paymentModuleArray,
                    'multiple' => true,
                    'label' => $this->translator->trans('Payment mean', [], BoSearch::DOMAIN_NAME),
                    'label_attr' => ['for' => 'paymentModule'],
                    'required' => false
                ]
            )->add(
                'status',
                ChoiceType::class,
                [
                    'choices' =>  $statusArray,
                    'multiple' => true,
                    'label' => $this->translator->trans('Status', [], BoSearch::DOMAIN_NAME),
                    'label_attr' => ['for' => 'status'],
                    'required' => false
                ]
            )->add(
                'orderDateMin',
                DateType::class,
                [
                    "label" => $this->translator->trans("From", [], BoSearch::DOMAIN_NAME),
                    "label_attr" => ["for" => "orderDateMin"],
                    "required" => false,
                    "input"  => "datetime",
                    "widget" => "single_text",

                ]
            )->add(
                'orderDateMax',
                DateType::class,
                [
                    'label' => $this->translator->trans('To', [], BoSearch::DOMAIN_NAME),
                    'label_attr' => ['for' => 'orderDateMax'],
                    'required' => false,
                    "input"  => "datetime",
                    "widget" => "single_text",

                ]
            );
    }

    public static function getName()
    {
        return self::ORDER_FORM_NAME;
    }
}