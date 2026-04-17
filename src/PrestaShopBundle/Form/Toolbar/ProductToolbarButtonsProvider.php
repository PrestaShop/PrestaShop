<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Toolbar;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider;
use PrestaShop\PrestaShop\Adapter\Shop\Url\HelpProvider;
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

    /**
     * @var LegacyContext
     */
    private $legacyContext;

    public function __construct(
        TranslatorInterface $translator,
        RouterInterface $router,
        HelpProvider $helpUrlProvider,
        ModuleDataProvider $moduleDataProvider,
        LegacyContext $legacyContext
    ) {
        $this->translator = $translator;
        $this->router = $router;
        $this->helpUrlProvider = $helpUrlProvider;
        $this->moduleDataProvider = $moduleDataProvider;
        $this->legacyContext = $legacyContext;
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
                $statsLink = $this->legacyContext->getAdminLink('AdminStats', true, ['module' => 'statsproduct', 'id_product' => $parameters['productId']]);

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
