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

namespace PrestaShop\PrestaShop\Core\Grid\Action\Bulk;

/**
 * Interface BulkActionInterface defines contract for single grid bulk action.
 */
interface BulkActionInterface
{
    /**
     * Get unique bulk action identifier for grid.
     *
     * @return string
     */
    public function getId();

    /**
     * Get translated bulk action name.
     *
     * @return string
     */
    public function getName();

    /**
     * Get action type.
     *
     * @return string
     */
    public function getType();

    /**
     * Get action options.
     *
     * @return array
     */
    public function getOptions();

    /**
     * Set options for bulk action.
     *
     * @param array $options
     *
     * @return self
     */
    public function setOptions(array $options);

    /**
     * Set bulk action name.
     *
     * @param string $name
     *
     * @return self
     */
    public function setName(string $name);

    /**
     * Returns action icon name.
     *
     * @return string
     */
    public function getIcon();

    /**
     * Set action icon name.
     *
     * @param string $icon
     *
     * @return self
     */
    public function setIcon(string $icon);
}
