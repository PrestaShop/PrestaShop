<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Integration\Classes;

use Image;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ImageDeleteCoverTest extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
    }

    /**
     * Deleting a product cover must also clear the temporary product_mini_{id}_{attributeId}.jpg
     * thumbnails, otherwise BO order/cart views keep showing the previous image.
     */
    public function testDeleteCoverRemovesTheTemporaryProductMiniThumbnails(): void
    {
        $idProduct = 999999;
        $thumbnails = [
            _PS_TMP_IMG_DIR_ . 'product_mini_' . $idProduct . '_1.jpg',
            _PS_TMP_IMG_DIR_ . 'product_mini_' . $idProduct . '_5.jpg',
        ];

        foreach ($thumbnails as $thumbnail) {
            file_put_contents($thumbnail, 'fake-thumbnail');
        }

        try {
            Image::deleteCover($idProduct);

            foreach ($thumbnails as $thumbnail) {
                $this->assertFileDoesNotExist($thumbnail);
            }
        } finally {
            foreach ($thumbnails as $thumbnail) {
                @unlink($thumbnail);
            }
        }
    }
}
