<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Utils;

use HTMLPurifier_Config;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;

class HTMLPurifier
{
    /**
     * @var \HTMLPurifier
     */
    private $instance;

    private readonly string $serializerPath;

    public function __construct(
        private readonly Filesystem $filesystem,
        #[Autowire(param: 'prestashop.legacy_cache_dir')]
        private readonly string $cacheDir,
    ) {
        $config = HTMLPurifier_Config::createDefault();
        // We must keep IDs that are by JS used to target element
        $config->set('Attr.EnableID', true);
        $config->set('Attr.AllowedFrameTargets', ['_blank']);

        $this->serializerPath = $this->cacheDir . 'purifier';
        $this->filesystem->mkdir($this->serializerPath);

        $config->set('Cache.SerializerPath', $this->serializerPath);

        $purifier = new \HTMLPurifier($config);
        $this->instance = $purifier;
    }

    /**
     * Filters an HTML snippet/document to be XSS-free and standards-compliant.
     *
     * @param string $html String of HTML to purify
     *
     * @return string Purified HTML
     */
    public function purify($html)
    {
        // In case of cache clear : the directory is removed; to avoid a race condition we recreate the cache dir for purifier
        $this->filesystem->mkdir($this->serializerPath);

        return $this->instance->purify($html);
    }
}
