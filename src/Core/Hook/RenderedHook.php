<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Hook;

/**
 * Class RenderingHook defines rendered hook.
 */
final class RenderedHook implements RenderedHookInterface
{
    /**
     * @var HookInterface
     */
    private $hook;

    /**
     * @var array ['module_name' => 'rendered_content', ...]
     */
    private $content;

    /**
     * @param HookInterface $hook
     * @param array $content
     */
    public function __construct(HookInterface $hook, array $content = [])
    {
        $this->hook = $hook;
        $this->content = $content;
    }

    /**
     * {@inheritdoc}
     */
    public function getHook()
    {
        return $this->hook;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * {@inheritdoc}
     */
    public function outputContent()
    {
        return implode('', $this->content);
    }
}
