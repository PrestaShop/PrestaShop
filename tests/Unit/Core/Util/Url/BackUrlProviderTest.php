<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Util\Url;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Util\Url\BackUrlProvider;
use Symfony\Component\HttpFoundation\Request;

class BackUrlProviderTest extends TestCase
{
    public function testItReturnsDecodedUrl()
    {
        $backUrlProvider = new BackUrlProvider();

        $actualResult = $backUrlProvider->getBackUrl(
            new Request([
                'back' => 'http%3A%2F%2Flocalhost',
            ])
        );

        $this->assertEquals('http://localhost', $actualResult);
    }
}
