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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\File\Validator;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\Decimal\Exception\DivisionByZeroException;
use PrestaShop\PrestaShop\Core\File\Exception\FileNotFoundException;
use PrestaShop\PrestaShop\Core\File\Exception\InvalidFileException;

/**
 * Validates virtual product file
 *
 * @todo: homogenize - add file validator interface ?
 */
class VirtualProductFileValidator
{
    /**
     * @var string
     */
    private $maxFileSizeInMegabytes;

    /**
     * @param string $maxFileSizeInMegabytes
     */
    public function __construct(
        string $maxFileSizeInMegabytes
    ) {
        $this->maxFileSizeInMegabytes = new DecimalNumber($maxFileSizeInMegabytes);
    }

    /**
     * @param string $filePath
     *
     * @throws DivisionByZeroException
     */
    public function validate(string $filePath): void
    {
        $this->assertIsFile($filePath);

        $million = new DecimalNumber('1000000');
        $maxFileSizeInBytes = $this->maxFileSizeInMegabytes->dividedBy($million);
        $actualSizeInBytes = new DecimalNumber((string) filesize($filePath));

        if ($maxFileSizeInBytes->isLowerThan($actualSizeInBytes)) {
            throw new InvalidFileException(
                sprintf(
                    'Maximum allowed file size "%s" exceeded. Given "%s"',
                    (string) $maxFileSizeInBytes,
                    (string) $actualSizeInBytes
                ),
                InvalidFileException::INVALID_SIZE
            );
        }
    }

    /**
     * @param string $filePath
     *
     * @throws InvalidFileException
     */
    private function assertIsFile(string $filePath): void
    {
        if (!is_file($filePath)) {
            throw new FileNotFoundException(sprintf('"%s" is not a file', $filePath));
        }
    }
}
