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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\Update\Filler;

use Combination;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationCommand;

/**
 * Responsible for filling up the Combination with the properties which have to be updated
 */
interface CombinationFillerInterface
{
    /**
     * Fill combination properties from the command and return an array of the properties to update.
     *
     * Returns a list of properties that were filled.
     * Simple (not multilingual) fields will be provided in a simple array as a values, while for
     * multilingual ones the array key will be the field name and the value will be an array of language ids.
     *
     * @return array<int, string|array<string, int>>
     *
     * e.g.:
     * [
     *     'reference',
     *     'visibility',
     *     'name' => [1, 2],
     * ]
     */
    public function fillUpdatableProperties(Combination $combination, UpdateCombinationCommand $command): array;
}
