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

namespace PrestaShopBundle\Bridge\Smarty;

use PrestaShopBundle\Bridge\AdminController\ControllerConfiguration;

/**
 * This class hydrates modals information needed for legacy modals.
 */
class ModalConfigurator implements ConfiguratorInterface
{
    /**
     * @param ControllerConfiguration $controllerConfiguration
     *
     * @return void
     */
    public function configure(ControllerConfiguration $controllerConfiguration): void
    {
        $controllerConfiguration->templateVars['img_base_path'] = __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_) . '/';
        $controllerConfiguration->templateVars['check_url_fopen'] = (ini_get('allow_url_fopen') ? 'ok' : 'ko');
        $controllerConfiguration->templateVars['check_openssl'] = (extension_loaded('openssl') ? 'ok' : 'ko');
        $controllerConfiguration->templateVars['add_permission'] = 1;
    }
}
