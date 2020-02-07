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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Feature\QueryHandler;

use Feature;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Query\GetFeatureForEditing;
use PrestaShop\PrestaShop\Core\Domain\Feature\QueryHandler\GetFeatureForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Feature\QueryResult\EditableFeature;

/**
 * Handles get feature for editing query.
 */
final class GetFeatureForEditingHandler implements GetFeatureForEditingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetFeatureForEditing $query)
    {
        $feature = new Feature($query->getFeatureId()->getValue());

        if (empty($feature->id)) {
            throw new FeatureNotFoundException('Feature could not be found.');
        }

        return new EditableFeature(
            $query->getFeatureId(),
            $feature->name,
            $feature->getAssociatedShops()
        );
    }
}
