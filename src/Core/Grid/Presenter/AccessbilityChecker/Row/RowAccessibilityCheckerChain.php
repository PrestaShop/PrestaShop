<?php
/**
 * 2007-2019 PrestaShop and Contributors
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

namespace PrestaShop\PrestaShop\Core\Grid\Presenter\AccessbilityChecker\Row;

use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionInterface;
use RuntimeException;

final class RowAccessibilityCheckerChain implements RowAccessibilityCheckerChainInterface
{
    /**
     * @var RowAccessibilityCheckerInterface[]
     */
    private $accessibilityCheckers = [];

    /**
     * {@inheritdoc}
     */
    public function check(RowActionInterface $action, array $record, $gridId)
    {
        foreach ($this->accessibilityCheckers as $accessibilityChecker) {
            if ($accessibilityChecker->supports($action, $gridId)) {
                return $accessibilityChecker->isGranted($record);
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function addChecker(RowAccessibilityCheckerInterface $checker)
    {
        $type = get_class($checker);

        if (isset($this->accessibilityCheckers[$type])) {
            throw new RuntimeException(
                sprintf('Row accessibility checker "%s" already exists in the chain', $type)
            );
        }

        $this->accessibilityCheckers[$type] = $checker;
    }
}
