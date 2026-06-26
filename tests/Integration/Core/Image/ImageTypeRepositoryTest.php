<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Image;

use Db;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Image\ImageTypeRepository;

/**
 * Applying a theme's image types must not destroy image types that do not belong to it — neither
 * the ones a merchant created by hand nor the ones required by another shop's theme. image_type is
 * a global (non shop-scoped) table, and it used to be truncated whenever a theme was enabled.
 *
 * @see https://github.com/PrestaShop/PrestaShop/issues/20300
 */
class ImageTypeRepositoryTest extends TestCase
{
    private const CUSTOM_TYPE = 'test_20300_custom';
    private const THEME_TYPE = 'test_20300_theme';

    private ImageTypeRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ImageTypeRepository(Db::getInstance());
        $this->cleanUp();
    }

    protected function tearDown(): void
    {
        $this->cleanUp();
        parent::tearDown();
    }

    public function testApplyingThemeTypesKeepsCustomTypes(): void
    {
        // A type the merchant created by hand (or one required by another shop's theme).
        $this->repository->createType(self::CUSTOM_TYPE, 120, 120, ['products']);
        $this->assertGreaterThan(0, $this->repository->getIdByName(self::CUSTOM_TYPE), 'precondition: custom type exists');

        // Enabling a theme applies its own image types through setTypes().
        $this->repository->setTypes([
            self::THEME_TYPE => ['width' => 200, 'height' => 200, 'scope' => ['products']],
        ]);

        // The theme type is created and the pre-existing custom type is preserved.
        $this->assertGreaterThan(0, $this->repository->getIdByName(self::THEME_TYPE), 'the theme type must be created');
        $this->assertGreaterThan(
            0,
            $this->repository->getIdByName(self::CUSTOM_TYPE),
            'applying a theme must not delete image types that do not belong to it'
        );
    }

    public function testReapplyingAThemeTypeUpdatesItInsteadOfDuplicating(): void
    {
        // image_type.name is unique; re-applying a theme (e.g. switching back) must update the row.
        $this->repository->setTypes([
            self::THEME_TYPE => ['width' => 200, 'height' => 200, 'scope' => ['products']],
        ]);
        $this->repository->setTypes([
            self::THEME_TYPE => ['width' => 350, 'height' => 350, 'scope' => ['categories']],
        ]);

        $rows = Db::getInstance()->executeS(
            'SELECT width, height, products, categories FROM ' . _DB_PREFIX_ . "image_type WHERE name = '" . self::THEME_TYPE . "'"
        );
        $this->assertCount(1, $rows, 're-applying a theme type must not create a duplicate row');
        $this->assertSame('350', (string) $rows[0]['width']);
        $this->assertSame('350', (string) $rows[0]['height']);
        $this->assertSame('0', (string) $rows[0]['products']);
        $this->assertSame('1', (string) $rows[0]['categories']);
    }

    private function cleanUp(): void
    {
        Db::getInstance()->execute(
            'DELETE FROM ' . _DB_PREFIX_ . "image_type WHERE name IN ('" . self::CUSTOM_TYPE . "', '" . self::THEME_TYPE . "')"
        );
    }
}
