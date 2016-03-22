<?php

namespace BoSearch\EventListeners;

use BoSearch\BoSearch;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\Join;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\Loop\LoopExtendsBuildModelCriteriaEvent;
use Thelia\Core\Event\Loop\LoopExtendsInitializeArgsEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Model\Map\AddressTableMap;
use Thelia\Model\Map\CustomerTableMap;
use Thelia\Model\Map\OrderAddressTableMap;
use Thelia\Model\Map\OrderTableMap;

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
        }
    }

    /**
     * @param LoopExtendsBuildModelCriteriaEvent $event
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
            if ($data['paymentModule'] != null ) {
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
            $parameters['limit'] = 200;
            $event->setLoopParameters($parameters);
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
            TheliaEvents::getLoopExtendsEvent(TheliaEvents::LOOP_EXTENDS_INITIALIZE_ARGS, 'order') => ['changeLimit', 128]
        ];
    }
}