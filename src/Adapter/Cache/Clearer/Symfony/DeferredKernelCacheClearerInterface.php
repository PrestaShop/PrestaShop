<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Cache\Clearer\Symfony;

/**
 * For clearers that defer part of their work until all the kernels of an environment are cleared, since cache:clear
 * also removes the legacy cache shared by the kernels.
 */
interface DeferredKernelCacheClearerInterface extends KernelCacheClearerInterface
{
    public function finishKernelCacheClear(): void;
}
