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

namespace PrestaShopBundle\Controller\Admin\Sell\Order;

/**
 * This class represents buttons to be rendered in Twig
 *
 * They will be rendered following this structure:
 * <button class="btn btn-action ml-3 {{ button.class }}"
 *   {% for tagName, tagContent in button.tags %}
 *     {{ tagName }}="{{ tagContent }}"
 *   {% endfor %}
 * >
 *   {{ button.content|raw }}
 * </button>
 *
 */
class ActionsBarButton
{
    /**
     * Complete this property to add extra CSS classes
     *
     * @var string
     */
    public $class;

    /**
     * Complete this property to add extra properties to <button> tag
     * Each item of the array will be created as a tag
     *
     * Example: if $tags is ['href' => '/a/b', 'alt' => 'link']
     * Then following tags will be added: href="/a/b", alt="link"
     *
     * @var string[]
     */
    public $tags;

    /**
     * This property content will be rendered raw inside the <button>
     *
     * @var string
     */
    public $content;

    /**
     * @param string $class
     * @param array $tags
     * @param string $content
     */
    public function __construct(string $class = '', array $tags = [], string $content = '')
    {
        $this->class = $class;
        $this->tags = $tags;
        $this->content = $content;
    }
}
