<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Cache\Clearer;

use Doctrine\Common\Cache\Psr6\DoctrineProvider;
use PrestaShop\PrestaShop\Core\Cache\Clearer\CacheClearerInterface;

final class DoctrineCacheClearer implements CacheClearerInterface
{
    /** @var DoctrineProvider */
    private $doctrineProvider;

    public function __construct(
        DoctrineProvider $doctrineProvider
    ) {
        $this->doctrineProvider = $doctrineProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->doctrineProvider->deleteAll();
    }
}
