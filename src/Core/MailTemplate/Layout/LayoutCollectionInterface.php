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
 * Interface MailLayoutCollectionInterface contains a list of layouts used to generate
 * mail templates. Modules can add/remove their own layouts to this collection through
 * the hook:
 *  ThemeCatalogInterface::LIST_MAIL_THEMES_HOOK = actionListMailThemes
 */
interface LayoutCollectionInterface extends \IteratorAggregate, \Countable
{
    /**
     * @param LayoutInterface $layout
     *
     * @return bool
     */
    public function contains($layout);

    /**
     * @param LayoutInterface $layout
     */
    public function add($layout);

    /**
     * @param LayoutInterface $layout
     */
    public function remove($layout);

    /**
     * @param LayoutInterface $oldLayout
     * @param LayoutInterface $newLayout
     *
     * @return bool
     */
    public function replace(LayoutInterface $oldLayout, LayoutInterface $newLayout);

    /**
     * @param string $layoutName
     * @param string $moduleName
     *
     * @return LayoutInterface|null
     */
    public function getLayout($layoutName, $moduleName);

    /**
     * @param LayoutCollectionInterface $collection
     */
    public function merge(LayoutCollectionInterface $collection);
}
