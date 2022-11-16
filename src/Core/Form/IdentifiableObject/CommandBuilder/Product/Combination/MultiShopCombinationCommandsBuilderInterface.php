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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * This interface is used by CombinationCommandsBuilder each object which implements must build
 * a combination command based on the input form data.
 *
 * @todo: since not all builders are migrated yet we need two interfaces but in the this is the only
 *        one that should remain, so it will be merged back or renamed as the initial one CombinationCommandsBuilderInterface
 *        and the shop constraint parameter will always be mandatory (there might a few builders which won't need it
 *        though, but it doesn't matter) So this interface is a temporary one just like @see CombinationMultiShopRepository
 */
interface MultiShopCombinationCommandsBuilderInterface
{
    /**
     * @param CombinationId $combinationId
     * @param array $formData
     *
     * @return array Returns empty array if the required data for the command is absent
     */
    public function buildCommands(CombinationId $combinationId, array $formData, ShopConstraint $singleShopConstraint): array;
}
