<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Shop;

use PrestaShop\PrestaShop\Adapter\Cache\Clearer\MediaCacheClearer;
use PrestaShop\PrestaShop\Adapter\Cache\Clearer\SmartyCacheClearer;
use PrestaShop\PrestaShop\Adapter\File\RobotsTextFileGenerator;
use PrestaShop\PrestaShop\Adapter\Tools;

/**
 * Refreshes the files and caches that depend on the shop URLs.
 */
final class ShopUrlChangeHandler
{
    public function __construct(
        private readonly Tools $tools,
        private readonly RobotsTextFileGenerator $robotsTextFileGenerator,
        private readonly SmartyCacheClearer $smartyCacheClearer,
        private readonly MediaCacheClearer $mediaCacheClearer,
    ) {
    }

    public function handle(): void
    {
        $this->tools->generateHtaccess();
        $this->robotsTextFileGenerator->generateFile();
        $this->smartyCacheClearer->clear();
        $this->mediaCacheClearer->clear();
    }
}
