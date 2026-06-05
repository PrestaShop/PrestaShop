<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes\Controller;

use GetFileControllerCore;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use UploadControllerCore;

class GetFileControllerTest extends TestCase
{
    /**
     * The download links are generated with the "get-file" controller name
     * (see ProductDownload::getTextLink()). Meta::getPages() builds the list of
     * pages available in BO > SEO & URLs from the php_self property, so php_self
     * must match "get-file" for the friendly URL to be applied to download links.
     *
     * @see https://github.com/PrestaShop/PrestaShop/issues/34339
     */
    public function testGetFileControllerExposesHyphenatedPhpSelf(): void
    {
        $properties = (new ReflectionClass(GetFileControllerCore::class))->getDefaultProperties();

        $this->assertSame('get-file', $properties['php_self']);
    }

    /**
     * UploadController extends GetFileController but is reached through its own
     * "upload" route. It must not inherit the "get-file" php_self, otherwise it
     * would collide with get-file in the SEO list and trigger a canonical
     * redirection in FrontController::init().
     */
    public function testUploadControllerDoesNotInheritGetFilePhpSelf(): void
    {
        $properties = (new ReflectionClass(UploadControllerCore::class))->getDefaultProperties();

        $this->assertEmpty($properties['php_self']);
    }
}
