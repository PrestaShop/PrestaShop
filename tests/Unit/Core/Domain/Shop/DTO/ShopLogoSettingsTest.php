<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Shop\DTO;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Shop\DTO\ShopLogoSettings;

class ShopLogoSettingsTest extends TestCase
{
    public function testGetLogoImageExtensionsWithDot(): void
    {
        $shopLogoSettings = new ShopLogoSettings();

        self::assertSame(
            ['.gif', '.jpg', '.jpeg', '.jpe', '.png', '.webp', '.svg'],
            $shopLogoSettings->getLogoImageExtensionsWithDot()
        );
    }
}
