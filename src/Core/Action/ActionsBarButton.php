<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Action;

class ActionsBarButton implements ActionsBarButtonInterface
{
    /**
     * @var string
     */
    protected $class;

    /**
     * @var array<string, scalar>
     */
    protected $properties;

    /**
     * @var string
     */
    protected $content;

    /**
     * @param string $class
     * @param array<string, scalar> $properties
     * @param string $content
     */
    public function __construct(string $class = '', array $properties = [], string $content = '')
    {
        $this->class = $class;
        $this->properties = $properties;
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @return array<string, scalar>
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }
}
