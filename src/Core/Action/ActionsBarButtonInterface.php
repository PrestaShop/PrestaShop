<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
 *   {% for tagName, tagContent in button.properties %}
 *     {{ tagName }}="{{ tagContent }}"
 *   {% endfor %}
 * >
 *   {{ button.content|raw }}
 * </button>
 *
 * However if there is more than 2 buttons displayed, then they are rendered into a drop-down list:
 *
 * <a class="dropdown-item btn {{ button.class }}"
 *   {% for tagName, tagContent in button.properties %}
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
     * Example: if $properties is ['href' => '/a/b', 'alt' => 'link']
     * Then following tags will be added: href="/a/b", alt="link"
     *
     * @return array<string, scalar>
     */
    public function getProperties(): array;

    /**
     * This function will provide content rendered raw inside the <button>
     *
     * @return string
     */
    public function getContent(): string;
}
