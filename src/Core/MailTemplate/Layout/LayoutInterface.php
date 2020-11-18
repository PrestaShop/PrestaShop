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

namespace PrestaShop\PrestaShop\Core\MailTemplate\Layout;

/**
 * Interface LayoutInterface is used to contain the basic info about a mail layout.
 */
interface LayoutInterface
{
    /**
     * Name of the layout to describe its purpose
     *
     * @return string
     */
    public function getName();

    /**
     * Absolute path of the html layout file
     *
     * @return string
     */
    public function getHtmlPath();

    /**
     * Absolute path of the html layout file
     *
     * @return string
     */
    public function getTxtPath();

    /**
     * Which module this layout is associated to (if any)
     *
     * @return string|null
     */
    public function getModuleName();
}
