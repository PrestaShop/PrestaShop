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

namespace Tests\Core\Domain\ImageSettings\QueryResult;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\QueryResult\EditableImageSettings;

class EditableImageSettingsTest extends TestCase
{
    public function testConstruct(): void
    {
        $instance = new EditableImageSettings(
            'jpg,avif',
            'jpg',
            90,
            90,
            8,
            90,
            2,
            499,
            123,
            456,
        );

        $this->assertSame(['jpg', 'avif'], $instance->getFormats());
        $this->assertSame('jpg', $instance->getBaseFormat());
        $this->assertSame(90, $instance->getAvifQuality());
        $this->assertSame(90, $instance->getJpegQuality());
        $this->assertSame(8, $instance->getPngQuality());
        $this->assertSame(90, $instance->getWebpQuality());
        $this->assertSame(2, $instance->getGenerationMethod());
        $this->assertSame(499, $instance->getPictureMaxSize());
        $this->assertSame(123, $instance->getPictureMaxWidth());
        $this->assertSame(456, $instance->getPictureMaxHeight());
    }
}
