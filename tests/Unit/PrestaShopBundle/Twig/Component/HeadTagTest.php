<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Twig\Component;

use Generator;
use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Twig\Component\HeadTag;
use PrestaShopBundle\Twig\Layout\TemplateVariables;
use ReflectionClass;
use ReflectionProperty;

final class HeadTagTest extends TestCase
{
    /**
     * @dataProvider isoUserForEditorProvider
     */
    public function testGetIsoUserForEditorFallsBackWhenLanguagePackIsMissing(string $isoUser, string $expected): void
    {
        $templateVariables = $this->createMock(TemplateVariables::class);
        $templateVariables->method('getIsoUser')->willReturn($isoUser);

        $headTag = (new ReflectionClass(HeadTag::class))->newInstanceWithoutConstructor();
        $property = new ReflectionProperty(HeadTag::class, 'templateVariables');
        $property->setValue($headTag, $templateVariables);

        $this->assertSame($expected, $headTag->getIsoUserForEditor());
    }

    public static function isoUserForEditorProvider(): Generator
    {
        yield 'pack exists (en)' => ['en', 'en'];
        yield 'pack exists (fr)' => ['fr', 'fr'];
        yield 'no pack (gb)' => ['gb', 'en'];
        yield 'no pack (mk)' => ['mk', 'en'];
    }
}
