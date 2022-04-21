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

namespace PrestaShop\PrestaShop\Core\Action;

/**
 * This class represents buttons to be rendered in Twig
 *
 * They will be rendered following this structure:
 *
 * If this is the 1st button or there are only 2 buttons displayed:
 *
 * <a class="btn {{ button.class }}"
 *   {% for tagName, tagContent in button.tags %}
 *     {{ tagName }}="{{ tagContent }}"
 *   {% endfor %}
 * >
 *   {{ button.content|raw }}
 * </button>
 *
 * However if there is more than 2 buttons displayed, then they are rendered into a drop-down list:
 *
 * <a class="dropdown-item btn {{ button.class }}"
 *   {% for tagName, tagContent in button.tags %}
 *     {{ tagName }}="{{ tagContent }}"
 *   {% endfor %}
 * >
 *   {{ button.content|raw }}
 * </a>
 */
interface ActionsBarButtonInterface
{
    /**
     * This function will provide content to add extra CSS classes
     *
     * @return string
     */
    public function getClass(): string;

    /**
     * Use this property to add extra properties to <button> tag
     * Each item of the array will be created as a tag
     *
     * Example: if $tags is ['href' => '/a/b', 'alt' => 'link']
     * Then following tags will be added: href="/a/b", alt="link"
     *
     * @return string[]
     */
    public function getProperties(): array;

    /**
     * This function will provide content rendered raw inside the <button>
     *
     * @return string
     */
    public function getContent(): string;
}
