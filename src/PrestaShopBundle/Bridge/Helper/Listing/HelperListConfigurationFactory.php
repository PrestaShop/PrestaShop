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

namespace PrestaShopBundle\Bridge\Helper\Listing;

use PrestaShopBundle\Bridge\AdminController\ControllerConfiguration;

/**
 * Create an instance of the helper configuration object, using controller configuration.
 */
class HelperListConfigurationFactory
{
    /**
     * @param ControllerConfiguration $controllerConfiguration
     * @param string $identifier
     * @param string|null $positionIdentifier
     * @param string|null $defaultOrderBy
     * @param bool $isJoinLanguageTableAuto
     * @param bool $deleted
     * @param bool $explicitSelect
     * @param bool $useFoundRows
     *
     * @return HelperListConfiguration
     */
    public function create(
        ControllerConfiguration $controllerConfiguration,
        string $identifier,
        string $positionIdentifier = null,
        string $defaultOrderBy = null,
        bool $isJoinLanguageTableAuto = false,
        // @todo: investigate what these options actually do and maybe rename them to reflect it better
        bool $deleted = false,
        bool $explicitSelect = false,
        bool $useFoundRows = true
    ): HelperListConfiguration {
        if (empty($defaultOrderBy)) {
            $defaultOrderBy = $identifier;
        }
        $helperListConfiguration = new HelperListConfiguration();

        $helperListConfiguration->table = $controllerConfiguration->tableName;
        $helperListConfiguration->listId = $controllerConfiguration->tableName;
        $helperListConfiguration->objectModelClassName = $controllerConfiguration->objectModelClassName;
        $helperListConfiguration->identifier = $identifier;
        $helperListConfiguration->positionIdentifier = $positionIdentifier;
        $helperListConfiguration->isJoinLanguageTableAuto = $isJoinLanguageTableAuto;
        $helperListConfiguration->deleted = $deleted;
        $helperListConfiguration->defaultOrderBy = $defaultOrderBy;
        $helperListConfiguration->explicitSelect = $explicitSelect;
        $helperListConfiguration->useFoundRows = $useFoundRows;
        $helperListConfiguration->id = $controllerConfiguration->tabId;
        $helperListConfiguration->legacyControllerName = $controllerConfiguration->legacyControllerName;
        $helperListConfiguration->token = $controllerConfiguration->token;
        $helperListConfiguration->bootstrap = $controllerConfiguration->bootstrap;
        $helperListConfiguration->legacyCurrentIndex = $controllerConfiguration->legacyCurrentIndex;
        $helperListConfiguration->multishopContext = $controllerConfiguration->multiShopContext;

        return $helperListConfiguration;
    }
}
