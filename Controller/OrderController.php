<?php

namespace BoSearch\Controller;

use BoSearch\BoSearch;
use BoSearch\Form\OrderSearchForm;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Template\ParserContext;
use Thelia\Core\Translation\Translator;
use Thelia\Form\Exception\FormValidationException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/module/bosearch/order-search", name="bosearch_order_search")
 * Class OrderController
 * @package BoSearch\Controller
 * @author Etienne Perriere
 */
class OrderController extends BaseAdminController
{
    /**
     * @Route("", name="", methods="POST")
     */
    public function searchAction(RequestStack $requestStack, ParserContext $parserContext)
    {
        if (null !== $response = $this->checkAuth([AdminResources::MODULE], ["bosearch"], AccessManager::VIEW)) {
            return $response;
        }

        $baseForm = $this->createForm(OrderSearchForm::getName());
        $error_message = false;

        try {
            $form = $this->validateForm($baseForm);

            // Set parsed data in the request to keep Datetime format for dates
            $requestStack->getCurrentRequest()->request->set(BoSearch::PARSED_DATA, $form->getData());

        } catch (FormValidationException $ex) {
            $error_message = $this->createStandardFormValidationErrorMessage($ex);
        } catch (\Exception $ex) {
            $error_message = $ex->getMessage();
        }

        if (false !== $error_message) {
            $this->setupFormErrorContext(
                Translator::getInstance()->trans("Searching orders"),
                $error_message,
                $baseForm,
                null
            );
        }

        $parserContext->addForm($baseForm);

        return $this->render(
            'orders',
            [
                "max_displayed_orders" => 2,
                "orders_order"  => $requestStack->getCurrentRequest()->get("orders_order", "create-date-reverse")
            ]
        );
    }
}