<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Employee;

/**
 * Uploads the avatar image of an employee from a file path.
 */
interface EmployeeImageUploaderInterface
{
    /**
     * @param int $employeeId
     * @param string $imagePath path to the source image file
     */
    public function upload(int $employeeId, string $imagePath): void;
}
