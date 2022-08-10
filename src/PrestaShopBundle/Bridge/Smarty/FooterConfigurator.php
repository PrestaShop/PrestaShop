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

use Language;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShopBundle\Bridge\AdminController\ControllerConfiguration;

/**
 * This class assign css files, js files, prestashop version,
 * and if the language is FR to the controller configuration.
 */
class FooterConfigurator implements ConfiguratorInterface
{
    /**
     * @var Language
     */
    private $language;

    /**
     * @param LegacyContext $legacyContext
     */
    public function __construct(LegacyContext $legacyContext)
    {
        $this->language = $legacyContext->getLanguage();
    }

    /**
     * @param ControllerConfiguration $controllerConfiguration
     *
     * @return void
     */
    public function configure(ControllerConfiguration $controllerConfiguration): void
    {
        $controllerConfiguration->templateVars['css_files'] = $controllerConfiguration->cssFiles;
        $controllerConfiguration->templateVars['js_files'] = array_unique($controllerConfiguration->jsFiles);
        $controllerConfiguration->templateVars['ps_version'] = _PS_VERSION_;
        $controllerConfiguration->templateVars['iso_is_fr'] = strtoupper($this->language->iso_code) == 'FR';
    }
}
