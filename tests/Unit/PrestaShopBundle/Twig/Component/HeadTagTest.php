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

class HeadTagTest extends TestCase
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
        $property->setAccessible(true);
        $property->setValue($headTag, $templateVariables);

        $this->assertSame($expected, $headTag->getIsoUserForEditor());
    }

    public static function isoUserForEditorProvider(): Generator
    {
        // Languages that ship a TinyMCE pack in js/tiny_mce/langs are kept as-is.
        yield 'pack exists (en)' => ['en', 'en'];
        yield 'pack exists (fr)' => ['fr', 'fr'];
        // Languages without a matching pack fall back to English to avoid a 404 on langs/<iso>.js.
        yield 'no pack (mk)' => ['mk', 'en'];
        yield 'no pack (ne)' => ['ne', 'en'];
    }
}
