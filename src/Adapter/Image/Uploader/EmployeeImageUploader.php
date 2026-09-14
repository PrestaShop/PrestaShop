<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Image\Uploader;

use Employee;
use PrestaShop\PrestaShop\Core\Domain\Employee\EmployeeImageUploaderInterface;

/**
 * Uploads employee logo image
 */
final class EmployeeImageUploader extends AbstractImageUploader implements EmployeeImageUploaderInterface
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
    public function upload(int $employeeId, string $imagePath): void
    {
        $this->deleteOldImage($employeeId);

        $destination = $this->employeeImageDir . $employeeId . '.jpg';
        $this->uploadFromTemp($imagePath, $destination);
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
