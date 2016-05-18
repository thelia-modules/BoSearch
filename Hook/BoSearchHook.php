<?php

namespace BoSearch\Hook;

use BoSearch\BoSearch;
use Symfony\Component\Routing\RouterInterface;
use Thelia\Core\Event\Hook\HookRenderBlockEvent;
use Thelia\Core\Hook\BaseHook;

/**
 * Class BoSearchHook
 * @package BoSearch\Hook
 * @author Etienne Perriere <eperriere@openstudio.fr>
 */
class BoSearchHook extends BaseHook
{
    /** @var RouterInterface */
    protected $router;

    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onMainTopMenuTools(HookRenderBlockEvent $event)
    {
        $event->add(
            [
                'title' => $this->trans('Search product', [], BoSearch::DOMAIN_NAME),
                'url' => $this->router->generate('bosearch.product.view')
            ]
        );
    }
}