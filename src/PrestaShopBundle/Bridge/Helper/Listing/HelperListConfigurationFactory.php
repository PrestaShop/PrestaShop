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
use Symfony\Component\Routing\RouterInterface;

/**
 * Create an instance of the helper configuration object, using controller configuration.
 */
class HelperListConfigurationFactory
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param RouterInterface $router
     */
    public function __construct(
        RouterInterface $router
    ) {
        $this->router = $router;
    }

    /**
     * @param ControllerConfiguration $controllerConfiguration
     * @param string $identifierKey
     * @param string $postSubmitRoute
     * @param string|null $positionIdentifier
     * @param string|null $defaultOrderBy
     * @param bool $isJoinLanguageTableAuto
     * @param bool $deleted
     * @param bool $explicitSelect
     * @param bool $useFoundRows
     * @param string|null $listId
     *
     * @return HelperListConfiguration
     */
    public function create(
        ControllerConfiguration $controllerConfiguration,
        string $identifierKey,
        string $postSubmitRoute,
        string $positionIdentifier = null,
        string $defaultOrderBy = null,
        bool $isJoinLanguageTableAuto = false,
        bool $deleted = false,
        bool $explicitSelect = false,
        bool $useFoundRows = true,
        ?string $listId = null
    ): HelperListConfiguration {
        if (empty($defaultOrderBy)) {
            $defaultOrderBy = $identifierKey;
        }

        return new HelperListConfiguration(
            $controllerConfiguration->tabId,
            $controllerConfiguration->tableName,
            $listId ?: $controllerConfiguration->tableName,
            $controllerConfiguration->objectModelClassName,
            $identifierKey,
            $positionIdentifier,
            $isJoinLanguageTableAuto,
            $deleted,
            $defaultOrderBy,
            $explicitSelect,
            $useFoundRows,
            $controllerConfiguration->legacyControllerName,
            $controllerConfiguration->token,
            $controllerConfiguration->bootstrap,
            $controllerConfiguration->legacyCurrentIndex,
            $controllerConfiguration->multiShopContext,
            $this->router->generate($postSubmitRoute)
        );
    }
}
