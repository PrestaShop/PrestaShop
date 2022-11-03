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

namespace Tests\Unit\Adapter\File\Validator;

use Generator;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\File\Validator\VirtualProductFileValidator;
use PrestaShop\PrestaShop\Core\File\Exception\FileNotFoundException;
use PrestaShop\PrestaShop\Core\File\Exception\InvalidFileException;
use Tests\Resources\DummyFileUploader;

class VirtualProductFileValidatorTest extends TestCase
{
    /**
     * @dataProvider getInvalidPaths
     *
     * @param string $filePath
     */
    public function testItThrowsExceptionWhenProvidedPathIsNotLeadingToAFile(string $filePath): void
    {
        $this->expectException(FileNotFoundException::class);
        $validator = new VirtualProductFileValidator('1');
        $validator->validate($filePath);
    }

    public function testItThrowsExceptionWhenFileSizeIsTooBig(): void
    {
        $this->expectException(InvalidFileException::class);
        $this->expectExceptionCode(InvalidFileException::INVALID_SIZE);

        $validator = new VirtualProductFileValidator('0.000019');
        $validator->validate(DummyFileUploader::getDummyFilesPath() . 'app_icon.png');
    }

    /**
     * @return Generator
     */
    public function getInvalidPaths(): Generator
    {
        yield [__DIR__];
        yield [__DIR__ . '/' . 'notexistingfile.csv'];
    }
}
