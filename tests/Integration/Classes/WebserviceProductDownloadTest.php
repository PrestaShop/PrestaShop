<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use PHPUnit\Framework\TestCase;
use ProductDownload;
use WebserviceRequest;

/**
 * The file attached to a virtual product had no representation in the web service at all, so there
 * was no way to read it through the API even though the product itself was exposed.
 */
class WebserviceProductDownloadTest extends TestCase
{
    public function testVirtualProductFilesAreExposedAsAWebserviceResource(): void
    {
        $resources = WebserviceRequest::getResources();

        self::assertArrayHasKey('product_downloads', $resources);
        self::assertSame('ProductDownload', $resources['product_downloads']['class']);
    }

    public function testTheResourceIsNamedConsistentlyWithItsEntity(): void
    {
        $parameters = (new ProductDownload())->getWebserviceParameters();

        self::assertSame('product_downloads', $parameters['objectsNodeName']);
        self::assertSame('product_download', $parameters['objectNodeName']);
    }

    public function testTheResourceLinksBackToItsProduct(): void
    {
        $parameters = (new ProductDownload())->getWebserviceParameters();

        self::assertSame('products', $parameters['fields']['id_product']['xlink_resource']);
    }

    /**
     * filename is the obfuscated name the file is stored under in download/. Writing it through the
     * API would repoint a record at a file uploaded for another product, so it is readable only -
     * same treatment as Customer::$secure_key.
     */
    public function testTheStoredFilenameIsExposedButNotSettable(): void
    {
        $parameters = (new ProductDownload())->getWebserviceParameters();

        self::assertArrayHasKey('filename', $parameters['fields']);
        self::assertFalse($parameters['fields']['filename']['setter']);
    }

    /**
     * @dataProvider provideExpectedFields
     */
    public function testTheFieldsAMerchantNeedsAreReadable(string $field): void
    {
        $parameters = (new ProductDownload())->getWebserviceParameters();

        self::assertArrayHasKey($field, $parameters['fields']);
        // Everything but filename stays writable, so the API can manage an existing file's settings.
        self::assertArrayNotHasKey('setter', $parameters['fields'][$field]);
    }

    public function provideExpectedFields(): iterable
    {
        yield 'display name shown to the customer' => ['display_filename'];
        yield 'expiration date' => ['date_expiration'];
        yield 'days the link stays valid' => ['nb_days_accessible'];
        yield 'allowed number of downloads' => ['nb_downloadable'];
        yield 'active' => ['active'];
        yield 'shareable' => ['is_shareable'];
    }
}
