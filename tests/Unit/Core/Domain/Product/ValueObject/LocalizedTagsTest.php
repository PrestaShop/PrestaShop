<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Product\ValueObject;

use Generator;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\LocalizedTags;

/**
 * LocalizedTags only enforces the shape of a tag (VALID_TAG_PATTERN, i.e. no
 * <, >, { or }). The searchability rule - "the indexer must yield at least one
 * keyword" - is enforced in SetProductTagsHandler because it needs a booted
 * shop; see tests/Integration/Classes/ValidateSearchableNameTest.
 */
class LocalizedTagsTest extends TestCase
{
    public function testItIsEmptyWhenConstructedWithoutAnyTag(): void
    {
        $localizedTags = new LocalizedTags(1, []);

        Assert::assertTrue($localizedTags->isEmpty());
        Assert::assertSame([], $localizedTags->getTags());
    }

    public function testItExposesTheLanguageIdItWasBuiltWith(): void
    {
        $localizedTags = new LocalizedTags(3, []);

        Assert::assertSame(3, $localizedTags->getLanguageId()->getValue());
    }

    /**
     * @dataProvider getShapeValidTags
     *
     * @param string[] $tags
     * @param string[] $expectedTags
     */
    public function testItAcceptsTagsWithValidShape(array $tags, array $expectedTags): void
    {
        $localizedTags = new LocalizedTags(1, $tags);
        Assert::assertSame($expectedTags, $localizedTags->getTags());
    }

    /**
     * @dataProvider getShapeInvalidTags
     */
    public function testItRejectsTagsWithForbiddenCharacters(string $tag): void
    {
        $this->expectException(ProductConstraintException::class);
        $this->expectExceptionCode(ProductConstraintException::INVALID_TAG);

        new LocalizedTags(1, [$tag]);
    }

    public function getShapeValidTags(): Generator
    {
        // Empty values are filtered out before validation, not rejected.
        yield 'empty values are skipped' => [['', 'summer'], ['summer']];
        yield 'plain word' => [['summer'], ['summer']];
        yield 'digits' => [['2024'], ['2024']];
        yield 'accented letters' => [['été'], ['été']];
        yield 'cjk letters' => [['中文'], ['中文']];
        yield 'hyphenated word' => [['t-shirt'], ['t-shirt']];
        // Would be rejected downstream by the searchability check in
        // SetProductTagsHandler, but LocalizedTags itself accepts them.
        yield 'punctuation only (shape ok)' => [['++++'], ['++++']];
    }

    public function getShapeInvalidTags(): Generator
    {
        yield 'contains <' => ['<script>'];
        yield 'contains >' => ['tag>'];
        yield 'contains {' => ['tag{x'];
        yield 'contains }' => ['tag}'];
    }
}
