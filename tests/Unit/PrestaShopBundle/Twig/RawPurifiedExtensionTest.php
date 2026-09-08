<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Twig;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Twig\RawPurifiedExtension;
use PrestaShopBundle\Utils\HTMLPurifier;

class RawPurifiedExtensionTest extends TestCase
{
    /**
     * Twig resolves an undefined or null variable to null, and the filter is applied to values that
     * are legitimately absent - a flash message that was not set, a hook that rendered nothing. A
     * non-nullable parameter turned that into a TypeError and took the page down with it.
     */
    public function testNullRendersAsAnEmptyStringWithoutPurifying(): void
    {
        $purifier = $this->createMock(HTMLPurifier::class);
        $purifier->expects($this->never())->method('purify');

        $extension = new RawPurifiedExtension($purifier);

        $this->assertSame('', $extension->rawPurifier(null));
    }

    public function testAStringIsStillHandedToThePurifier(): void
    {
        $purifier = $this->createMock(HTMLPurifier::class);
        $purifier
            ->expects($this->once())
            ->method('purify')
            ->with('<b>kept</b><script>dropped</script>')
            ->willReturn('<b>kept</b>')
        ;

        $extension = new RawPurifiedExtension($purifier);

        $this->assertSame('<b>kept</b>', $extension->rawPurifier('<b>kept</b><script>dropped</script>'));
    }
}
