<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
