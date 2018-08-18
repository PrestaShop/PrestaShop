<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Backup\Modifier;

use PrestaShop\PrestaShop\Core\Grid\Modifier\ModifierInterface;

/**
 * Class HumanReadableBackupSizeModifier modifies backup size to be human readable
 */
final class HumanReadableBackupFileSizeModifier implements ModifierInterface
{
    /**
     * {@inheritdoc}
     */
    public function modify(array $record)
    {
        $record['file_size'] = $this->getHumanReadableSize($record['file_size']);
    }

    /**
     * Get size that is human readable
     *
     * @param int $sizeInBytes
     *
     * @return string
     */
    private function getHumanReadableSize($sizeInBytes)
    {
        return sprintf('%s Kb', number_format($sizeInBytes / 1000, 2));
    }
}
