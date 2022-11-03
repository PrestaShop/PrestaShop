<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Image\Uploader;

use Employee;
use PrestaShop\PrestaShop\Core\Image\Uploader\ImageUploaderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Uploads employee logo image
 */
final class EmployeeImageUploader extends AbstractImageUploader implements ImageUploaderInterface
{
    /**
     * @var string
     */
    private $employeeImageDir;

    /**
     * @var string
     */
    private $tmpImageDir;

    /**
     * @param string $employeeImageDir
     * @param string $tmpImageDir
     */
    public function __construct(
        string $employeeImageDir = _PS_EMPLOYEE_IMG_DIR_,
        string $tmpImageDir = _PS_TMP_IMG_DIR_
    ) {
        $this->employeeImageDir = $employeeImageDir;
        $this->tmpImageDir = $tmpImageDir;
    }

    /**
     * {@inheritdoc}
     */
    public function upload($employeeId, UploadedFile $image)
    {
        $this->checkImageIsAllowedForUpload($image);
        $tempImageName = $this->createTemporaryImage($image);
        $this->deleteOldImage($employeeId);

        $destination = $this->employeeImageDir . $employeeId . '.jpg';
        $this->uploadFromTemp($tempImageName, $destination);
    }

    /**
     * Deletes old image
     *
     * @param int $id
     */
    private function deleteOldImage($id)
    {
        $employee = new Employee($id);
        $employee->deleteImage();

        $currentImage = $this->tmpImageDir . 'employee_mini_' . $id . '.jpg';

        if (file_exists($currentImage)) {
            unlink($currentImage);
        }
    }
}
