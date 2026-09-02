<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Shop\Url;

use PrestaShop\PrestaShop\Core\Shop\Url\UrlProviderInterface;

/**
 * Test class used to replace ProductImageFolderProvider in test environment
 */
class TestProductImageFolderProvider implements UrlProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function getUrl()
    {
        return 'http://myshop.com/img/p';
    }
}
