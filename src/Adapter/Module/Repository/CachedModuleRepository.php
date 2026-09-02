<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Module\Repository;

use Symfony\Contracts\Cache\CacheInterface;

class CachedModuleRepository
{
    private $decorated;
    private $cache;

    public function __construct(ModuleRepository $decorated, CacheInterface $cache)
    {
        $this->decorated = $decorated;
        $this->cache = $cache;
    }

    public function getInstalledModules(): array
    {
        return $this->cache->get('installed_modules', function () {
            return $this->decorated->getInstalledModules();
        });
    }

    public function getPresentModules(): array
    {
        return $this->cache->get('present_modules', function () {
            return $this->decorated->getPresentModules();
        });
    }

    public function getActiveModules(): array
    {
        return $this->cache->get('active_modules', function () {
            return $this->decorated->getActiveModules();
        });
    }
}
