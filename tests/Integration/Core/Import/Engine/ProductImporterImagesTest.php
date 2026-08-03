<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Import\Engine;

use Tests\Resources\Resetter\ProductResetter;
use Tests\Resources\ResourceResetter;

class ProductImporterImagesTest extends AbstractImportEngineTestCase
{
    private const IMAGE_FIELDS = ['name', 'reference', 'image', 'image_alt'];
    private const IMAGE_REPLACE_FIELDS = ['name', 'reference', 'image', 'image_alt', 'delete_existing_images'];
    private const VIRTUAL_FIELDS = ['name', 'reference', 'is_virtual', 'file_url', 'nb_downloadable', 'nb_days_accessible', 'date_expiration'];

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        (new ResourceResetter())->backupImages();
        (new ResourceResetter())->backupDownloads();
        ProductResetter::resetProducts();
    }

    public static function tearDownAfterClass(): void
    {
        ProductResetter::resetProducts();
        (new ResourceResetter())->resetImages();
        (new ResourceResetter())->resetDownloads();
        parent::tearDownAfterClass();
    }

    public function testImagesFromLocalPathsAreImportedWithLegends(): void
    {
        [, $messages] = $this->runImport('product_images.csv', self::IMAGE_FIELDS);
        $this->assertNoErrors($messages);

        $productId = $this->getProductIdByReference('IMG-P1');
        $this->assertNotNull($productId);

        $images = $this->fetchAll('SELECT * FROM {p}image WHERE id_product = :id ORDER BY position', ['id' => $productId]);
        $this->assertCount(2, $images);
        $this->assertSame('1', (string) $images[0]['cover'], 'The first image must be the cover');

        $legends = array_column($this->fetchAll('SELECT il.legend FROM {p}image_lang il WHERE il.id_image IN (:first, :second) AND il.id_lang = 1 ORDER BY il.id_image', ['first' => $images[0]['id_image'], 'second' => $images[1]['id_image']]), 'legend');
        $this->assertSame(['Front view', 'Back view'], $legends);

        foreach ($images as $image) {
            $this->assertNotFalse($this->fetchOne('SELECT 1 FROM {p}image_shop WHERE id_image = :id AND id_shop = 1', ['id' => $image['id_image']]));
        }
    }

    public function testDeleteExistingImagesReplacesThePreviousOnes(): void
    {
        $this->runImport('product_images.csv', self::IMAGE_FIELDS);
        $productId = $this->getProductIdByReference('IMG-P1');
        $this->assertCount(2, $this->fetchAll('SELECT id_image FROM {p}image WHERE id_product = :id', ['id' => $productId]));

        [, $messages] = $this->runImport('product_images_replace.csv', self::IMAGE_REPLACE_FIELDS, ['matchRef' => true]);
        $this->assertNoErrors($messages);

        $images = $this->fetchAll('SELECT * FROM {p}image WHERE id_product = :id', ['id' => $productId]);
        $this->assertCount(1, $images, 'delete_existing_images must remove the previous images');
        $this->assertSame('Only view', (string) $this->fetchOne('SELECT legend FROM {p}image_lang WHERE id_image = :id AND id_lang = 1', ['id' => $images[0]['id_image']]));
    }

    public function testVirtualProductWithFile(): void
    {
        [, $messages] = $this->runImport('product_virtual.csv', self::VIRTUAL_FIELDS);
        $this->assertNoErrors($messages);

        $productId = $this->getProductIdByReference('VIRT-1');
        $this->assertNotNull($productId);

        $this->assertSame('virtual', (string) $this->fetchOne('SELECT product_type FROM {p}product WHERE id_product = :id', ['id' => $productId]));
        $this->assertSame('1', (string) $this->fetchOne('SELECT is_virtual FROM {p}product WHERE id_product = :id', ['id' => $productId]));

        $download = $this->fetchRow('SELECT * FROM {p}product_download WHERE id_product = :id AND active = 1', ['id' => $productId]);
        $this->assertNotFalse($download, 'product_download row missing');
        $this->assertSame('test_text_file.txt', $download['display_filename']);
        $this->assertSame('5', (string) $download['nb_downloadable']);
        $this->assertSame('30', (string) $download['nb_days_accessible']);
        $this->assertStringStartsWith('2027-01-01', (string) $download['date_expiration']);
    }

    public function testNonVirtualReimportDoesNotTouchTheVirtualFile(): void
    {
        $this->runImport('product_virtual.csv', self::VIRTUAL_FIELDS);
        $productId = $this->getProductIdByReference('VIRT-1');
        $downloadId = $this->fetchOne('SELECT id_product_download FROM {p}product_download WHERE id_product = :id AND active = 1', ['id' => $productId]);
        $this->assertNotFalse($downloadId);

        // same file re-imported while mapping ONLY name and reference: the
        // virtual columns are unmapped, so the file association must survive
        // (legacy destroyed ProductDownload on every product row)
        [, $messages] = $this->runImport('product_virtual.csv', ['name', 'reference'], ['matchRef' => true]);
        $this->assertNoErrors($messages);

        $this->assertSame(
            (string) $downloadId,
            (string) $this->fetchOne('SELECT id_product_download FROM {p}product_download WHERE id_product = :id AND active = 1', ['id' => $productId]),
            'Re-importing without virtual columns must not touch product_download'
        );
    }
}
