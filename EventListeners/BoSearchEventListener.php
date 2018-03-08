<?php

namespace BoSearch\EventListeners;

use BoSearch\BoSearch;
use BoSearch\Form\CustomerSearchForm;
use CustomerFamily\Model\CustomerFamily;
use CustomerFamily\Model\CustomerFamilyQuery;
use CustomerFamily\Model\Map\CustomerCustomerFamilyTableMap;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\Join;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\Loop\LoopExtendsBuildModelCriteriaEvent;
use Thelia\Core\Event\Loop\LoopExtendsInitializeArgsEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Event\TheliaFormEvent;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\Translation\Translator;
use Thelia\Model\Map\AddressTableMap;
use Thelia\Model\Map\CustomerTableMap;
use Thelia\Model\Map\OrderAddressTableMap;
use Thelia\Model\Map\OrderTableMap;
use Thelia\Model\Map\ProductTableMap;
use Thelia\Model\ModuleQuery;
use PDO;

/**
 * Class BoSearchEventListener
 * @package BoSearch\EventListeners
 * @author Etienne Perriere <eperriere@openstudio.fr>
 */
class BoSearchEventListener implements EventSubscriberInterface
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param LoopExtendsBuildModelCriteriaEvent $event
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function extendCustomerLoop(LoopExtendsBuildModelCriteriaEvent $event)
    {
        // Get form data
        $data = $this->request->request->get(BoSearch::PARSED_DATA);

        if ($data != null) {

            /** @var \Thelia\Model\CustomerQuery $search */
            $search = $event->getModelCriteria();
            // Filter by company
            if ($data['company'] != null) {
                // Join address
                $customerAddressJoin = new Join(
                    CustomerTableMap::ID,
                    AddressTableMap::CUSTOMER_ID,
                    Criteria::INNER_JOIN
                );

                $search->addJoinObject($customerAddressJoin, 'customerAddressJoin');
                $search->addJoinCondition(
                    'customerAddressJoin',
                    AddressTableMap::COMPANY." LIKE '%".$data['company']."%'"
                );
                $search->groupBy(CustomerTableMap::ID);
            }

            // Filter by lastname
            if ($data['lastname'] != null) {
                $search->filterByLastname("%".$data['lastname']."%", Criteria::LIKE);
            }

            // Filter by firstname
            if ($data['firstname'] != null) {
                $search->filterByFirstname("%".$data['firstname']."%", Criteria::LIKE);
            }

            // Filter by email
            if ($data['email'] != null) {
                $search->filterByEmail("%".$data['email']."%", Criteria::LIKE);
            }

            // Filter by subscription - from
            if ($data['subscriptionDateMin'] != null) {
                $search->filterByCreatedAt($data['subscriptionDateMin'], Criteria::GREATER_EQUAL);
            }

            // Filter by subscription - to
            if ($data['subscriptionDateMax'] != null) {
                $search->filterByCreatedAt($data['subscriptionDateMax'], Criteria::LESS_EQUAL);
            }

            //Filter by Customer Family
            if ($data['family'] != null && $data['family'] != "all") {
                $familyCustomerFamilyBoSearch = new Join(
                    CustomerTableMap::ID,
                    CustomerCustomerFamilyTableMap::CUSTOMER_ID,
                    Criteria::INNER_JOIN
                );
                $search->addJoinObject($familyCustomerFamilyBoSearch, 'familyCustomerFamilyBoSearch');
                $search->addJoinCondition(
                    'familyCustomerFamilyBoSearch',
                    CustomerCustomerFamilyTableMap::CUSTOMER_FAMILY_ID.' = ?',
                    $data['family'],
                    null,
                    PDO::PARAM_STR
                );
                $search->groupBy(CustomerTableMap::ID);
            }
        }
    }

    /**
     * @param LoopExtendsBuildModelCriteriaEvent $event
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function extendOrderLoop(LoopExtendsBuildModelCriteriaEvent $event)
    {
        // Get form data
        $data = $this->request->request->get(BoSearch::PARSED_DATA);

        if ($data != null) {

            /** @var \Thelia\Model\OrderQuery $search */
            $search = $event->getModelCriteria();

            // Filter by order reference
            if ($data['ref'] != null) {
                $search->filterByRef("%".$data['ref']."%", Criteria::LIKE);
            }

            // Filter by invoice reference
            if ($data['invoiceRef'] != null) {
                $search->filterByInvoiceRef("%".$data['invoiceRef']."%", Criteria::LIKE);
            }

            // Filter by company
            if ($data['company'] != null) {
                // Join invoice address
                $orderInvoiceAddressJoin = new Join(
                    OrderTableMap::INVOICE_ORDER_ADDRESS_ID,
                    OrderAddressTableMap::ID,
                    Criteria::INNER_JOIN
                );

                $search->addJoinObject($orderInvoiceAddressJoin, 'orderInvoiceAddressJoin');
                $search->addJoinCondition(
                    'orderInvoiceAddressJoin',
                    OrderAddressTableMap::COMPANY." LIKE '%".$data['company'] . "%'"
                );
            }

            // Filter by customer
            if ($data['customer'] != null) {
                // Join customer
                $orderAddressJoin = new Join(
                    OrderTableMap::INVOICE_ORDER_ADDRESS_ID,
                    OrderAddressTableMap::ID,
                    Criteria::INNER_JOIN
                );

                $search->addJoinObject($orderAddressJoin, 'orderAddressJoin');
                $search->addJoinCondition(
                    'orderAddressJoin',
                    '('.OrderAddressTableMap::FIRSTNAME." LIKE '%".$data['customer']."%' OR ".
                    OrderAddressTableMap::LASTNAME." LIKE '%".$data['customer']."%')"
                );

                $search->groupById();
            }

            // Filter by payment module
            if ($data['paymentModule'] != null) {
                $search->filterByPaymentModuleId($data['paymentModule']);
            }

            // Filter by status
            if ($data['status'] != null) {
                $search->filterByStatusId($data['status']);
            }

            // Filter by order date - from
            if ($data['orderDateMin'] != null) {
                $search->filterByCreatedAt($data['orderDateMin'], Criteria::GREATER_EQUAL);
            }

            // Filter by order date - to
            if ($data['orderDateMax'] != null) {
                $search->filterByCreatedAt($data['orderDateMax'], Criteria::LESS_EQUAL);
            }
        }
    }

    public function changeLimit(LoopExtendsInitializeArgsEvent $event)
    {
        $data = $this->request->request->get(BoSearch::PARSED_DATA);
        $parameters = $event->getLoopParameters();

        if ($parameters['limit'] != null && $data != null) {
            $parameters['limit'] = 9999999;
            $event->setLoopParameters($parameters);
        }
    }

    public function extendProductBuildModelCriteria(LoopExtendsBuildModelCriteriaEvent $event)
    {
        // Get form data
        $data = $this->request->request->get(BoSearch::PARSED_DATA);

        if ($data != null) {
            /** @var \Thelia\Model\ProductQuery $search */
            $search = $event->getModelCriteria();


            // Filter by product ID
            if ($data['product_id'] != null) {
                $search->filterById($data['product_id']);
            }

            // Filter by product reference
            if ($data['ref'] != null) {
                $search->filterByRef("%".$data['ref']."%", Criteria::LIKE);
            }

            // Filter by categories
            if ($data['category'] != null) {
                $search
                    ->useProductCategoryQuery('CategorySelect')
                        ->filterByCategoryId($data['category'], Criteria::IN)
                    ->endUse();
            }


            // PSE criteria

            $pseQuery = $search->useProductSaleElementsQuery();

            if ($data['is_new'] != null) {
                if ($data['is_new'] == 'yes') {
                    $pseQuery->filterByNewness(1);
                } elseif ($data['is_new'] == 'no') {
                    $pseQuery->filterByNewness(0);
                }
            }

            if ($data['is_promo'] != null) {
                if ($data['is_promo'] == 'yes') {
                    $pseQuery->filterByPromo(1);
                } elseif ($data['is_promo'] == 'no') {
                    $pseQuery->filterByPromo(0);
                }
            }

            if ($data['stock_min'] != null) {
                $pseQuery->filterByQuantity($data['stock_min'], Criteria::GREATER_EQUAL);
            }

            if ($data['stock_max'] != null) {
                $pseQuery->filterByQuantity($data['stock_max'], Criteria::LESS_EQUAL);
            }

            $pseQuery->endUse();


            // Filter by visible
            $search->remove(ProductTableMap::VISIBLE);

            if ($data['is_visible'] != null) {
                if ($data['is_visible'] == 'yes') {
                    $search->filterByVisible(1);
                } elseif ($data['is_visible'] == 'no') {
                    $search->filterByVisible(0);
                }
            }
        }
    }

    /**
     * @param TheliaFormEvent $event
     */
    public function addCustomerFamilySearchFormField(TheliaFormEvent $event)
    {
        $moduleCustomerFamily = ModuleQuery::create()
            ->filterByCode('CustomerFamily')
            ->filterByActivate(1)
            ->findOne();

        if ($moduleCustomerFamily) {
            $customerFamilies = CustomerFamilyQuery::create()
                ->find();

            $lang = $this->request->getSession()->get('thelia.current.lang');

            /** @var CustomerFamily $customerFamily */
            $familyChoices = ['all' => Translator::getInstance()->trans('All', [], 'bosearch.bo.default')];

            foreach ($customerFamilies as $customerFamily) {
                $familyChoices[$customerFamily->getId()] = $customerFamily
                    ->getTranslation($lang->getLocale())
                    ->getTitle();
            }

            // Building additional fields
            $event->getForm()->getFormBuilder()
            ->add(
                'family',
                'choice',
                array(
                    'choices' => $familyChoices,
                    'required' => false,
                    'label' => Translator::getInstance()->trans(
                        'Customer family',
                        [],
                        \CustomerFamily\CustomerFamily::MESSAGE_DOMAIN
                    ),
                    'label_attr' => array(
                        'for' => 'customerfamily_family_input',
                    ),
                )
            );
        }
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return [
            TheliaEvents::getLoopExtendsEvent(TheliaEvents::LOOP_EXTENDS_BUILD_MODEL_CRITERIA, 'customer') => ['extendCustomerLoop', 128],
            TheliaEvents::getLoopExtendsEvent(TheliaEvents::LOOP_EXTENDS_BUILD_MODEL_CRITERIA, 'order') => ['extendOrderLoop', 128],
            TheliaEvents::getLoopExtendsEvent(TheliaEvents::LOOP_EXTENDS_INITIALIZE_ARGS, 'order') => ['changeLimit', 128],
            TheliaEvents::getLoopExtendsEvent(TheliaEvents::LOOP_EXTENDS_BUILD_MODEL_CRITERIA, 'product') => ['extendProductBuildModelCriteria', 120],

            TheliaEvents::FORM_AFTER_BUILD.'.'.CustomerSearchForm::CUSTOMER_FORM_NAME => ['addCustomerFamilySearchFormField', 128],
        ];
    }
}
