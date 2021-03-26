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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

function scandir($directory, $sorting_order = null, $context = null)
{
    return [
        '1-0_thumb.jpg',
        '1-1_thumb.jpg',
    ];
}

namespace Tests\Unit\Core\Form\IdentifiableObject\DataHandler;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\AbstractCategoryFormDataHandler;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\CategoryFormDataHandler;
use PrestaShop\PrestaShop\Core\Image\Uploader\ImageUploaderInterface;

class CategoryFormDataHandlerTest extends TestCase
{
    public function testGetAvailableKeys(): void
    {
        $commandBusMock = $this->createMock(CommandBusInterface::class);
        $imageUploaderMock = $this->createMock(ImageUploaderInterface::class);
        define('_PS_CAT_IMG_DIR_', '');

        $categoryFormDataHandler = new CategoryFormDataHandler(
            $commandBusMock,
            $imageUploaderMock,
            $imageUploaderMock,
            $imageUploaderMock
        );

        $reflectionClass = new \ReflectionClass(AbstractCategoryFormDataHandler::class);

        $reflectionMethod = $reflectionClass->getMethod('getAvailableKeys');
        $reflectionMethod->setAccessible(true);
        $result = $reflectionMethod->invoke($categoryFormDataHandler, 1);
        self::assertEquals(
            [
                2 => 2,
            ],
            $result
        );
    }
}
