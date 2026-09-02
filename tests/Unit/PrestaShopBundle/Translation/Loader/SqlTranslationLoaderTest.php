<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Translation\Loader;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShopBundle\Translation\Loader\SqlTranslationLoader;

if (!defined('_DB_PREFIX_')) {
    define('_DB_PREFIX_', 'ps_');
}

/**
 * Unit regression tests for SqlTranslationLoader (issue #41232).
 *
 * Tests the WHERE condition built by buildThemeCondition() — the pure-logic part of the
 * loader that has no DB dependency and can be verified without a running database.
 *
 * Two bugs the condition must fix:
 *
 *   Bug A — wrong column: the original IN subquery referenced ps_shop.theme which has never
 *   existed; the correct column is ps_shop.theme_name. MySQL silently returns empty results.
 *
 *   Bug B — split-loader assumption: in PS8 two separate loader instances were registered
 *   (one for theme IS NULL rows, one for theme rows). In PS9 the Symfony container is always
 *   active and only a single loader instance is used, so it must cover both row types in one
 *   query regardless of whether setTheme() was called.
 *
 * The full behavioural suite (with a real DB) lives in
 * tests/Integration/PrestaShopBundle/Translation/Loader/SqlTranslationLoaderTest.php.
 */
final class SqlTranslationLoaderTest extends TestCase
{
    public function testConditionAlwaysIncludesCoreRows(): void
    {
        $condition = (new ExposedSqlTranslationLoader())->getThemeCondition();

        $this->assertStringContainsString(
            'theme IS NULL',
            $condition,
            'Core (theme IS NULL) rows must always be included — they hold admin-customised core translations'
        );
    }

    public function testConditionAlwaysIncludesActiveShopThemeRows(): void
    {
        $condition = (new ExposedSqlTranslationLoader())->getThemeCondition();

        $this->assertStringContainsString(
            '`theme_name`',
            $condition,
            'Active-shop theme rows must always be included — ps_shop.theme_name is the correct column'
        );
        $this->assertStringNotContainsString(
            'SELECT `theme`',
            $condition,
            'ps_shop.theme is not a valid column and causes the query to fail silently'
        );
    }

    public function testConditionIsUnchangedWhenThemeIsSet(): void
    {
        $loader = new ExposedSqlTranslationLoader();
        $loader->setTheme($this->createMock(Theme::class));

        $condition = $loader->getThemeCondition();

        $this->assertStringContainsString('theme IS NULL', $condition,
            'Core rows must still be loaded even when a theme context is available — '
            . 'in PS9 a single loader instance handles both row types'
        );
        $this->assertStringContainsString('`theme_name`', $condition);
    }
}

/**
 * Exposes the protected buildThemeCondition() method for assertion.
 */
final class ExposedSqlTranslationLoader extends SqlTranslationLoader
{
    public function getThemeCondition(): string
    {
        return $this->buildThemeCondition();
    }
}
