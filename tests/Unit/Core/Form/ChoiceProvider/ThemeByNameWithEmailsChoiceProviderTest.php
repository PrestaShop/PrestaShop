<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Form\ChoiceProvider;

use Generator;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeCollection;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\ThemeByNameWithEmailsChoiceProvider;

class ThemeByNameWithEmailsChoiceProviderTest extends ChoiceProviderTestCase
{
    private const DIR_THEMES = _PS_ROOT_DIR_ . '/themes';

    /**
     * @dataProvider getExpectedChoices
     *
     * @param ThemeCollection $themeCollection
     * @param array $expectedChoices
     */
    public function testItProvidesChoicesAsExpected(
        ThemeCollection $themeCollection,
        array $expectedChoices
    ): void {
        $choiceProvider = new ThemeByNameWithEmailsChoiceProvider(
            $themeCollection
        );

        $this->assertEquals($expectedChoices, $choiceProvider->getChoices());
    }

    /**
     * @return Generator
     */
    public function getExpectedChoices(): Generator
    {
        $themeDir = realpath(self::DIR_THEMES);

        // Empty Theme Collection
        $themeCollection = new ThemeCollection();
        yield [
            $themeCollection,
            [],
        ];

        // Theme Collection
        $themeCollection = new ThemeCollection();
        $themeCollection->add(
            new Theme([
                'name' => Theme::getDefaultTheme(),
                'directory' => $themeDir . '/' . Theme::getDefaultTheme() . '/',
            ])
        );
        yield [
            $themeCollection,
            [
                Theme::getDefaultTheme() => $themeDir . '/' . Theme::getDefaultTheme() . '/',
            ],
        ];

        // Theme Collection (but directory themes not found)
        $themeDirNotFound = $themeDir . '/doesntexist';
        $themeCollection = new ThemeCollection();
        $themeCollection->add(
            new Theme([
                'name' => Theme::getDefaultTheme(),
                'directory' => $themeDirNotFound . '/' . Theme::getDefaultTheme() . '/',
            ])
        );
        yield [
            $themeCollection,
            [],
        ];
    }
}
