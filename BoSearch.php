<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace BoSearch;

use Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator;
use Thelia\Core\Template\TemplateDefinition;
use Thelia\Module\BaseModule;

class BoSearch extends BaseModule
{
    const DOMAIN_NAME = 'bosearch';
    const PARSED_DATA = 'parsedData';

    public function getHooks()
    {
        return [
            [
                'code' => 'bosearch.customer-search.form',
                'type' => TemplateDefinition::BACK_OFFICE,
                'active' => true,
                'title' => [
                    'en_US' => 'Extend customer search form',
                    'fr_FR' => 'Étend le formulaire de recherche de clients'
                ]
            ],
            [
                'code' => 'bosearch.order-search.form',
                'type' => TemplateDefinition::BACK_OFFICE,
                'active' => true,
                'title' => [
                    'en_US' => 'Extend order search form',
                    'fr_FR' => 'Étend le formulaire de recherche de commandes'
                ]
            ]
        ];
    }

    public static function configureServices(ServicesConfigurator $servicesConfigurator): void
    {
        $servicesConfigurator->load(self::getModuleCode().'\\', __DIR__)
            ->exclude([THELIA_MODULE_DIR . ucfirst(self::getModuleCode()). "/I18n/*"])
            ->autowire(true)
            ->autoconfigure(true);
    }
}
