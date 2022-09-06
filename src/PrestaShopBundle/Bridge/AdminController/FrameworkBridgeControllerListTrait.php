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

namespace PrestaShopBundle\Bridge\AdminController;

use PrestaShopBundle\Bridge\Helper\Listing\HelperListConfiguration;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contains the principal methods you need to horizontally migrate a controller which has a list.
 */
trait FrameworkBridgeControllerListTrait
{
    /**
     * @param string $identifierKey @see HelperListConfiguration::$identifierKey
     * @param string $defaultOrderBy @see HelperListConfiguration::$defaultOrderBy
     * @param string $indexRoute route name used to generate url for filters & sorting submissions. @see HelperListConfiguration::$indexUrl
     * @param string|null $positionIdentifierKey @see HelperListConfiguration::$positionIdentifierKey
     * @param bool $autoJoinLangTable @see HelperListConfiguration::$autoJoinLanguageTable
     * @param bool $deleted @see HelperListConfiguration::$deleted
     * @param bool $explicitSelect @see HelperListConfiguration::$explicitSelect
     * @param bool $useFoundRows @see HelperListConfiguration::$useFoundRows
     * @param string|null $listId @see HelperListConfiguration::$listId
     *
     * @return HelperListConfiguration
     */
    protected function buildListConfiguration(
        string $identifierKey,
        string $defaultOrderBy,
        string $indexRoute,
        ?string $positionIdentifierKey = null,
        bool $autoJoinLangTable = true,
        bool $deleted = false,
        bool $explicitSelect = false,
        bool $useFoundRows = true,
        ?string $listId = null
    ): HelperListConfiguration {
        $controllerConfiguration = $this->getControllerConfiguration();

        return $this->get('prestashop.bridge.helper.listing.helper_list_configuration_factory')->create(
            $controllerConfiguration,
            $identifierKey,
            $indexRoute,
            $positionIdentifierKey,
            $defaultOrderBy,
            $autoJoinLangTable,
            $deleted,
            $explicitSelect,
            $useFoundRows,
            $listId
        );
    }

    /**
     * Handles filters submit and reset
     *
     * @param Request $request
     * @param HelperListConfiguration $helperListConfiguration
     */
    protected function processFilters(Request $request, HelperListConfiguration $helperListConfiguration): void
    {
        $this->get('prestashop.bridge.helper.listing.filters_processor')
            ->processFilters($request, $helperListConfiguration)
        ;
    }
}
