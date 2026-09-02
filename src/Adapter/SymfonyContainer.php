<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter;

use LogicException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class SymfonyContainer.
 *
 * This is a TEMPORARY class for quick access to the Symfony Container
 */
final class SymfonyContainer
{
    /** @var ContainerInterface|null */
    private static $instance = null;

    /**
     * Get a singleton instance of SymfonyContainer.
     *
     * @return ContainerInterface|null
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            global $kernel;

            if (null !== $kernel && $kernel instanceof KernelInterface) {
                try {
                    self::$instance = $kernel->getContainer();
                } catch (LogicException) {
                    self::$instance = null;
                }
            }
        }

        return self::$instance;
    }

    public static function resetStaticCache()
    {
        self::$instance = null;
    }
}
