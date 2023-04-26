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

namespace PrestaShopBundle\Controller\Admin;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use Symfony\Component\HttpFoundation\Response;

/**
 * This controller is used to render the missing parts of the layout while the migration of legacy layout
 * is under development. It renders smarty components that have not been migrated yet. As such this controller
 * is bound to be removed once the layout is fully handled by Symfony.
 */
class LegacyController extends FrameworkBundleAdminController
{
    /**
     * This action is used to render a smarty component template.
     *
     * {{ render(controller('PrestaShopBundle\\Controller\\Admin\\LegacyController::smartyComponentAction', {
     *   'smartyTemplate': 'components/layout/search_form.tpl',
     *   'smartyVariables': {
     *     'baseAdminUrl': baseAdminUrl,
     *     'bo_query': bo_query,
     *   }
     * })) }}
     *
     * @return Response
     */
    public function smartyComponentAction(string $smartyTemplate, array $smartyVariables): Response
    {
        global $smarty;
        $legacyContext = $this->get(LegacyContext::class);
        $contextSmarty = $legacyContext->getContext()->smarty;

        $contextSmarty->setTemplateDir([
            _PS_BO_ALL_THEMES_DIR_ . 'new-theme' . DIRECTORY_SEPARATOR . 'template',
            _PS_OVERRIDE_DIR_ . 'controllers' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'templates',
        ]);
        $contextSmarty->assign($smartyVariables);

        return new Response($contextSmarty->fetch($smartyTemplate));
    }
}
