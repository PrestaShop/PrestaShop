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

namespace Tests\Unit\Core\Addon\Theme;

use LegacyTests\TestCase\FakeConfiguration;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeValidator;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Yaml\Parser;

class ThemeValidatorTest extends TestCase
{
    const NOTICE = '[ThemeValidator] ';

    private $validator;

    protected function setUp()
    {
        $translator = $this->createMock(TranslatorInterface::class);

        /* @var \PrestaShop\PrestaShop\Core\Addon\Theme\ThemeValidator */
        $this->validator = new ThemeValidator($translator, new FakeConfiguration(
            [
                '_PS_ALL_THEMES_DIR_' => '/themes/',
            ]
        ));
    }

    protected function tearDown()
    {
        $this->validator = null;
    }

    public function testIsValidWithValidTheme()
    {
        $isValid = $this->validator->isValid($this->getTheme());
        $this->assertTrue($isValid, self::NOTICE . sprintf('expected isValid to return true when theme is valid, got %s', gettype($isValid)));
    }

    public function testIsValidWithInvalidThemeMissingFiles()
    {
        $isValid = $this->validator->isValid($this->getTheme('missfiles'));
        $this->assertFalse($isValid, self::NOTICE . sprintf('expected isValid to return false when theme is invalid, got %s', gettype($isValid)));
    }

    public function testIsValidWithInvalidThemeMissingProperties()
    {
        $isValid = $this->validator->isValid($this->getTheme('missconfig'));
        $this->assertFalse($isValid, self::NOTICE . sprintf('expected isValid to return false when theme is invalid, got %s', gettype($isValid)));
    }

    private function getTheme($name = 'valid')
    {
        $options = ['valid', 'missfiles', 'missconfig'];

        if (!in_array($name, $options)) {
            throw new \InvalidArgumentException(self::NOTICE . 'getTheme($name) only accepts specified arguments');
        }
        $themeDir = __DIR__ . '/../../../../Resources/themes/minimal-' . $name . '-theme/';
        $themeConfigFile = $themeDir . 'config/theme.yml';

        $config = (new Parser())->parse(file_get_contents($themeConfigFile));
        $config['directory'] = $themeDir;
        $config['physical_uri'] = '/';

        $theme = new Theme($config);

        return $theme;
    }
}
