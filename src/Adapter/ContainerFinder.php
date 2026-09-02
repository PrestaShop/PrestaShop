<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter;

use Context;
use PrestaShop\PrestaShop\Core\Exception\ContainerNotFoundException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Find the container
 */
class ContainerFinder
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * ContainerFinder constructor.
     *
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    /**
     * @return ContainerBuilder|ContainerInterface
     *
     * @throws ContainerNotFoundException
     */
    public function getContainer()
    {
        if (isset($this->context->container)) {
            return $this->context->container;
        }
        if (isset($this->context->controller)
            && method_exists($this->context->controller, 'getContainer')
            && ($container = $this->context->controller->getContainer())
            && null !== $container
        ) {
            return $container;
        }
        $container = SymfonyContainer::getInstance();
        if (null !== $container) {
            return $container;
        }

        throw new ContainerNotFoundException('Kernel Container is not available');
    }
}
