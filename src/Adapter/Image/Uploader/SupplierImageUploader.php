<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Image\Uploader;

use Supplier;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Uploads supplier logo image
 */
final class SupplierImageUploader extends AbstractImageUploader
{
    /**
     * {@inheritdoc}
     */
    public function upload($supplierId, UploadedFile $image)
    {
        $this->checkImageIsAllowedForUpload($image);
        $tempImageName = $this->createTemporaryImage($image);
        $this->deleteOldImage($supplierId);

        $destination = _PS_SUPP_IMG_DIR_ . $supplierId . '.jpg';
        $this->uploadFromTemp($tempImageName, $destination);

        if (file_exists($destination)) {
            $this->generateDifferentSize($supplierId, _PS_SUPP_IMG_DIR_, 'suppliers');
        }
    }

    /**
     * Deletes old image
     *
     * @param $id
     */
    private function deleteOldImage($id)
    {
        $supplier = new Supplier($id);
        $supplier->deleteImage();

        $currentLogo = _PS_TMP_IMG_DIR_ . 'supplier_mini_' . $id . '.jpg';

        if (file_exists($currentLogo)) {
            unlink($currentLogo);
        }
    }
}
