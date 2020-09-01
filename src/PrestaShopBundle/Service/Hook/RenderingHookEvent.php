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

namespace PrestaShopBundle\Service\Hook;

/**
 * RenderingHookEvent is used in HookDispatcher for rendering hooks.
 *
 * A HookEvent can contains parameters to give to the listeners through getHookParameters,
 * but can also contains responses from subscribers, to deliver HTML or other data to the caller.
 */
class RenderingHookEvent extends HookEvent
{
    /**
     * @var array
     */
    private $currentContent = [];
    /**
     * @var string
     */
    private $currentListener = '';

    /**
     * Sets the response from the listener.
     *
     * Should be called by the listener to store its response.
     * This content will be pushed in a stack between each listener call.
     * Every response is kept, but a given listener cannot see the previous listeners' responses.
     *
     * @param array $content The rendering content returned by the listener
     * @param string $fromListener The listener that sets the content
     *
     * @return $this for fluent use
     */
    public function setContent(array $content, $fromListener = '')
    {
        $this->currentContent = $content;
        $this->currentListener = $fromListener;

        return $this;
    }

    /**
     * Gets the last pushed content (for the current listener).
     *
     * @return array
     */
    public function getContent()
    {
        return $this->currentContent;
    }

    /**
     * Retrieves the last pushed content (and cleans the corresponding attribute).
     *
     * @return array
     */
    public function popContent()
    {
        $content = $this->currentContent;
        $this->currentContent = [];

        return $content;
    }

    /**
     * Gets the current listener that put the response (and cleans the corresponding attribute).
     *
     * @return string a listener
     */
    public function popListener()
    {
        $listener = $this->currentListener;
        $this->currentListener = '';

        return $listener;
    }
}
