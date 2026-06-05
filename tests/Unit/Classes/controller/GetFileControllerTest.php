<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes\Controller;

use Context;
use GetFileControllerCore;
use Link;
use PHPUnit\Framework\TestCase;
use ProductDownload;
use ReflectionClass;
use UploadControllerCore;

class GetFileControllerTest extends TestCase
{
    /**
     * Virtual product download links are generated for the "get-file" controller
     * (ProductDownload::getTextLink), while the friendly URL configured in
     * BO > SEO & URLs is registered under the controller php_self (Meta::getPages).
     * If those two names drift apart, the friendly URL stops being applied and the
     * link falls back to index.php?controller=get-file.
     *
     * This test pins the download link to GetFileController::$php_self so both sides
     * cannot diverge again.
     *
     * @see https://github.com/PrestaShop/PrestaShop/issues/34339
     */
    public function testDownloadLinkTargetsGetFileControllerPhpSelf(): void
    {
        $capturedController = null;
        $link = $this->getMockBuilder(Link::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getPageLink'])
            ->getMock();
        $link
            ->method('getPageLink')
            ->willReturnCallback(function ($controller) use (&$capturedController) {
                $capturedController = $controller;

                return '';
            });

        $context = Context::getContext();
        $previousLink = $context->link;
        $context->link = $link;

        try {
            $productDownload = new ProductDownload();
            $productDownload->filename = 'a1b2c3';
            $productDownload->getTextLink('orderhash');
        } finally {
            $context->link = $previousLink;
        }

        // getPageLink() receives "<controller>&key=..." so we only compare the controller part.
        $linkController = explode('&', (string) $capturedController)[0];
        $phpSelf = (new ReflectionClass(GetFileControllerCore::class))->getDefaultProperties()['php_self'];

        $this->assertSame($phpSelf, $linkController);
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
