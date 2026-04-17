<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Cache\Clearer\Symfony;

use AppKernel;

interface KernelCacheClearerInterface
{
    public function clearKernelCache(AppKernel $kernel, string $environment): bool;
}
