<?php

/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\DependencyInjection\Provider;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Collects grid definition services ids.
 */
final class GridDefinitionServiceIdsProvider implements ServiceIdsProviderInterface
{
    const SERVICE_STARTS_WITH = 'prestashop.core.grid.definition';

    /**
     * {@inheritdoc}
     */
    public function getServiceIds(ContainerBuilder $containerBuilder)
    {
        $serviceDefinitions = $containerBuilder->getDefinitions();

        $serviceIds = [];
        foreach ($serviceDefinitions as $serviceId => $serviceDefinition) {
            if ($serviceDefinition->isAbstract()  || $serviceDefinition->isPrivate()) {
                continue;
            }

            if (strpos($serviceId, self::SERVICE_STARTS_WITH) === 0) {
                $serviceIds[] = $serviceId;
            }
        }

        return $serviceIds;
    }
}
