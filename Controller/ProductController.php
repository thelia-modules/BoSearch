<?php

namespace BoSearch\Controller;

use BoSearch\BoSearch;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Form\Exception\FormValidationException;

/**
 * Class ProductController
 * @package BoSearch\Controller
 * @author Etienne Perriere <eperriere@openstudio.fr>
 */
class ProductController extends BaseAdminController
{
    public function viewProductSearchAction()
    {
        if (null !== $response = $this->checkAuth([AdminResources::MODULE], ["bosearch"], AccessManager::VIEW)) {
            return $response;
        }

        return $this->render('product-search');
    }

    public function searchAction(Request $request)
    {
        if (null !== $response = $this->checkAuth([AdminResources::MODULE], ["bosearch"], AccessManager::VIEW)) {
            return $response;
        }

        $baseForm = $this->createForm("product-search-form");
        $error_message = false;

        try {
            $form = $this->validateForm($baseForm);

            // Set parsed data in the request to keep Datetime format for dates
            $request->request->set(BoSearch::PARSED_DATA, $form->getData());
        } catch (FormValidationException $ex) {
            $error_message = $this->createStandardFormValidationErrorMessage($ex);
        } catch (\Exception $ex) {
            $error_message = $ex->getMessage();
        }

        if (false !== $error_message) {
            $this->setupFormErrorContext(
                $this->getTranslator()->trans("Searching products"),
                $error_message,
                $baseForm,
                null
            );
        }

        $this->getParserContext()->addForm($baseForm);

        return $this->render('product-search', ['search' => true]);
    }
}
