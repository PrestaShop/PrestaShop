<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Proxy;

use PrestaShop\PrestaShop\Core\File\FileFinderInterface;

/**
 * Class CachedFileFinderProxy is a local cache proxy of file finder.
 */
final class CachedFileFinderProxy implements FileFinderInterface
{
    /**
     * @var FileFinderInterface
     */
    private $delegate;

    /**
     * @var array
     */
    private $filesCache;

    /**
     * @param FileFinderInterface $delegate instance of file finder
     */
    public function __construct(FileFinderInterface $delegate)
    {
        $this->delegate = $delegate;
    }

    /**
     * {@inheritdoc}
     */
    public function find()
    {
        if (null === $this->filesCache) {
            $this->filesCache = $this->delegate->find();
        }

        return $this->filesCache;
    }
}
