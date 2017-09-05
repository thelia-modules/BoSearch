# Bo Search

Add search forms to filter customers, orders and products

## Installation

### Manually

* Copy the module into ```<thelia_root>/local/modules/``` directory and be sure that the name of the module is BoSearch.
* Activate it in your Thelia administration panel

### Composer

Add it in your main Thelia composer.json file

```
composer require thelia/bo-search-module:~1.0
```

## Usage

Once activated, inputs will appear on the top of the customer and the order list pages. Use them to filter displayed results.
You will find the product search page in the back-office menu "Search products".

## Hook

Two hooks are added by this module, allowing you to extend forms.

- `bosearch.customer-search.form`: between the last customer's form input and the submit button.
- `bosearch.order-search.form`: between the last order's form input and the submit button.


Then extend the forms in your extension module's EventListener class:

```
TheliaEvents::FORM_AFTER_BUILD . '.' . \BoSearch\Form\OrderSearchForm::ORDER_FORM_NAME => ['yourFunction', 128],
TheliaEvents::FORM_AFTER_BUILD . '.' . \BoSearch\Form\CustomerSearchForm::CUSTOMER_FORM_NAME => ['yourFunction', 128]
```
