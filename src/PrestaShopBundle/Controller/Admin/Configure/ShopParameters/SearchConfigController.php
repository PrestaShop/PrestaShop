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

namespace PrestaShopBundle\Controller\Admin\Configure\ShopParameters;

use PrestaShopBundle\Bridge\AdminController\AdminControllerTrait;
use PrestaShopBundle\Bridge\AdminController\Field\FormField;
use PrestaShopBundle\Bridge\AdminController\LegacyControllerBridgeInterface;
use PrestaShopBundle\Bridge\Smarty\SmartyTrait;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Response;

class SearchConfigController extends FrameworkBundleAdminController implements LegacyControllerBridgeInterface
{
    use SmartyTrait;
    use AdminControllerTrait;

    public function getTable(): string
    {
        return 'alias';
    }

    public function getClassName(): string
    {
        return 'Alias';
    }

    /**
     * @todo: its actually now for search aliases crud. Need separate controller for it SearchAliasController instead of this one?
     * @todo: separate create/edit action, but keep allowing to define common form
     *
     * @param int|null $searchConfigId
     *
     * @return Response
     */
    public function formAction(?int $searchConfigId): Response
    {
        $formConfigFactory = $this->get('prestashop.core.bridge.helper.form.helper_form_configuration_factory');
        $formConfig = $formConfigFactory->create($searchConfigId, $this->getClassName(), [
            new FormField('legend', [
                'title' => $this->trans('Aliases', 'Admin.Shopparameters.Feature'),
                'icon' => 'icon-search',
            ]),
            new FormField('input', [
                'type' => 'text',
                'label' => $this->trans('Alias', 'Admin.Shopparameters.Feature'),
                'name' => 'alias',
                'required' => true,
                'hint' => [
                    $this->trans('Enter each alias separated by a comma (e.g. \'prestshop,preztashop,prestasohp\').', 'Admin.Shopparameters.Help'),
                    $this->trans('Forbidden characters: &lt;&gt;;=#{}', 'Admin.Shopparameters.Help'),
                ],
            ]),
            new FormField('input', [
                'type' => 'text',
                'label' => $this->trans('Result', 'Admin.Shopparameters.Feature'),
                'name' => 'search',
                'required' => true,
                'hint' => $this->trans('Search this word instead.', 'Admin.Shopparameters.Help'),
            ]),
            new FormField('submit', [
                'title' => $this->trans('Save', 'Admin.Actions'),
            ]),
        ]);

        return $this->renderSmarty($this->get('prestashop.core.bridge.helper.form.helper_form_bridge')->generate($formConfig));
    }
}
