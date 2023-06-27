<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Toolbar;

use PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Shop\Url\HelpProvider;
use PrestaShop\PrestaShop\Core\Link\LinkInterface;
use PrestaShopBundle\Form\Admin\Type\IconButtonType;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * This provider generates the list of buttons available in the toolbar on the product form page, these buttons
 * are displayed in the header part of the tab navigation component.
 */
class ProductToolbarButtonsProvider implements ToolbarButtonsProviderInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var HelpProvider
     */
    private $helpUrlProvider;

    /**
     * @var ModuleDataProvider
     */
    private $moduleDataProvider;

    public function __construct(
        TranslatorInterface $translator,
        RouterInterface $router,
        HelpProvider $helpUrlProvider,
        ModuleDataProvider $moduleDataProvider,
        private readonly LinkInterface $link,
    ) {
        $this->translator = $translator;
        $this->router = $router;
        $this->helpUrlProvider = $helpUrlProvider;
        $this->moduleDataProvider = $moduleDataProvider;
    }

    public function getToolbarButtonsOptions(array $parameters): array
    {
        $toolbarButtons = [
            'product_list' => [
                'type' => IconButtonType::class,
                'options' => [
                    'type' => 'button',
                    'icon' => 'list',
                    'label' => $this->translator->trans('Product list', [], 'Admin.Catalog.Feature'),
                    'attr' => [
                        'title' => $this->translator->trans('Product list', [], 'Admin.Catalog.Feature'),
                        'class' => 'toolbar-button btn-quicknav btn-sidebar',
                        'data-toggle' => 'sidebar',
                        'data-target' => '#right-sidebar',
                        'data-url' => $this->router->generate('admin_products_light_list'),
                    ],
                ],
            ],
            'help' => [
                'type' => IconButtonType::class,
                'options' => [
                    'type' => 'button',
                    'icon' => 'help',
                    'label' => $this->translator->trans('Help', [], 'Admin.Global'),
                    'attr' => [
                        'title' => $this->translator->trans('Help', [], 'Admin.Global'),
                        'class' => 'toolbar-button btn-quicknav btn-sidebar',
                        'data-toggle' => 'sidebar',
                        'data-target' => '#right-sidebar',
                        'data-url' => $this->helpUrlProvider->getUrl('AdminProducts'),
                    ],
                ],
            ],
        ];

        if (!empty($parameters['productId'])) {
            $statsModule = $this->moduleDataProvider->findByName('statsproduct');
            if (!empty($statsModule['active'])) {
                $statsLink = $this->link->getAdminLink('AdminStats', true, ['module' => 'statsproduct', 'id_product' => $parameters['productId']]);

                $toolbarButtons = array_merge([
                    'stats_link' => [
                        'type' => IconButtonType::class,
                        'options' => [
                            'type' => 'link',
                            'icon' => 'assessment',
                            'label' => $this->translator->trans('Sales', [], 'Admin.Global'),
                            'attr' => [
                                'title' => $this->translator->trans('Sales', [], 'Admin.Global'),
                                'href' => $statsLink,
                                'class' => 'toolbar-button btn-sales',
                                'target' => '_blank',
                            ],
                        ],
                    ],
                ], $toolbarButtons);
            }
        }

        return $toolbarButtons;
    }
}
