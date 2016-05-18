<?php

namespace BoSearch\Form;

use BoSearch\BoSearch;
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
            $statusArray[$status->getId()] = $status->getTitle();
        }

        // Initialize payment modules
        $paymentModuleArray = [];
        $paymentModuleList = (new ModuleQuery)->findByType(3);

        /** @var \Thelia\Model\Module $paymentModule */
        foreach ($paymentModuleList as $paymentModule) {
            $paymentModuleArray[$paymentModule->getId()] = $paymentModule->getTitle();
        }

        $this->formBuilder
            ->add(
                'ref',
                'text',
                [
                    'label' => $this->translator->trans('Order reference', [], BoSearch::DOMAIN_NAME),
                    'label_attr' => ['for' => 'ref'],
                    'required' => false
                ]
            )->add(
                'invoiceRef',
                'text',
                [
                    'label' => $this->translator->trans('Invoice reference', [], BoSearch::DOMAIN_NAME),
                    'label_attr' => ['for' => 'invoiceRef'],
                    'required' => false
                ]
            )->add(
                'company',
                'text',
                [
                    'label' => $this->translator->trans('Company', [], BoSearch::DOMAIN_NAME),
                    'label_attr' => ['for' => 'company'],
                    'required' => false
                ]
            )->add(
                'customer',
                'text',
                [
                    'label' => $this->translator->trans('Customer', [], BoSearch::DOMAIN_NAME),
                    'label_attr' => ['for' => 'customer'],
                    'required' => false
                ]
            )->add(
                'paymentModule',
                'choice',
                [
                    'choices' =>  $paymentModuleArray,
                    'multiple' => true,
                    'label' => $this->translator->trans('Payment mean', [], BoSearch::DOMAIN_NAME),
                    'label_attr' => ['for' => 'paymentModule'],
                    'required' => false
                ]
            )->add(
                'status',
                'choice',
                [
                    'choices' =>  $statusArray,
                    'multiple' => true,
                    'label' => $this->translator->trans('Status', [], BoSearch::DOMAIN_NAME),
                    'label_attr' => ['for' => 'status'],
                    'required' => false
                ]
            )->add(
                'orderDateMin',
                "date",
                [
                    "label" => $this->translator->trans("From", [], BoSearch::DOMAIN_NAME),
                    "label_attr" => ["for" => "orderDateMin"],
                    "required" => false,
                    "input"  => "datetime",
                    "widget" => "single_text",
                    "format" => "dd/MM/yyyy"
                ]
            )->add(
                'orderDateMax',
                'date',
                [
                    'label' => $this->translator->trans('To', [], BoSearch::DOMAIN_NAME),
                    'label_attr' => ['for' => 'orderDateMax'],
                    'required' => false,
                    "input"  => "datetime",
                    "widget" => "single_text",
                    "format" => "dd/MM/yyyy"
                ]
            );
    }

    public function getName()
    {
        return self::ORDER_FORM_NAME;
    }
}