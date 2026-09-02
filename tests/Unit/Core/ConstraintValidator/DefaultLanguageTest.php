<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ConstraintValidator;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;

/**
 * Construction tests for the DefaultLanguage constraint (the validator itself is covered by
 * DefaultLanguageValidatorTest).
 *
 * The constraint declares `fieldName` as its default option so it follows the Symfony convention
 * `new DefaultLanguage($value)`; the constructor must not clobber a value the parent set from that
 * default option.
 */
class DefaultLanguageTest extends TestCase
{
    public function testDefaultOptionIsFieldName(): void
    {
        $this->assertSame('fieldName', (new DefaultLanguage())->getDefaultOption());
    }

    public function testFieldNameSetViaTheDefaultOption(): void
    {
        $this->assertSame('video_link', (new DefaultLanguage('video_link'))->fieldName);
    }

    public function testFieldNameSetViaTheNamedArgumentStillWorks(): void
    {
        // Regression: the constructor no longer resets fieldName to '' when the positional arg is omitted.
        $this->assertSame('video_link', (new DefaultLanguage(fieldName: 'video_link'))->fieldName);
    }

    public function testFieldNameDefaultsToEmptyStringWhenBare(): void
    {
        $this->assertSame('', (new DefaultLanguage())->fieldName);
    }

    public function testAllowNullDefaultsToFalseAndCanBeSet(): void
    {
        $this->assertFalse((new DefaultLanguage())->allowNull);
        $this->assertTrue((new DefaultLanguage(fieldName: 'video_link', allowNull: true))->allowNull);
    }
}
