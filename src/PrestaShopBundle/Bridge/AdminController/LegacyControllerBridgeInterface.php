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

namespace PrestaShopBundle\Bridge\AdminController;

/**
 * This interface will be shared with legacy and expose legacy methods needed for modules.
 */
interface LegacyControllerBridgeInterface
{
    public const DEFAULT_THEME = 'default';

    /**
     * Sets default media list for this controller.
     *
     * @param bool $isNewTheme
     *
     * @return void
     */
    public function setMedia(bool $isNewTheme = false): void;

    /**
     * Adds a new stylesheet(s) to the page header.
     *
     * @param string|array $cssUri
     * @param string $cssMediaType
     * @param int|null $offset
     * @param bool $checkPath
     *
     * @return void
     */
    public function addCSS($cssUri, $cssMediaType = 'all', $offset = null, $checkPath = true): void;

    /**
     * Adds a new JavaScript file(s) to the page header.
     *
     * @param string|array $jsUri
     * @param bool $checkPath
     *
     * @return void
     */
    public function addJS($jsUri, $checkPath = true): void;

    /**
     * Adds jQuery plugin(s) to queued JS file list.
     *
     * @param array|string $name
     * @param string|null $folder
     * @param bool $css
     *
     * @return void
     */
    public function addJqueryPlugin($name, $folder = null, $css = true): void;

    /**
     * Adds jQuery UI component(s) to queued JS file list.
     *
     * @param array $component
     * @param string $theme
     * @param bool $checkDependencies
     *
     * @return void
     */
    public function addJqueryUI($component, $theme = 'base', $checkDependencies = true): void;
}
