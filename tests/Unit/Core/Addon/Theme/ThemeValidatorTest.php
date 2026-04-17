<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Addon\Theme;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeValidator;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use RuntimeException;
use Symfony\Component\Yaml\Parser;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

class ThemeValidatorTest extends TestCase
{
    private const NOTICE = '[ThemeValidator] ';

    /**
     * @var ThemeValidator|null
     */
    private $validator;

    protected function setUp(): void
    {
        $translator = $this->createMock(TranslatorInterface::class);

        $configurationMock = $this->getMockBuilder(ConfigurationInterface::class)
            ->getMock();
        $configurationMock->method('get')
            ->will($this->returnValueMap([
                ['_PS_ALL_THEMES_DIR_', null, null, '/themes/'],
            ]));

        $this->validator = new ThemeValidator($translator, $configurationMock);
    }

    protected function tearDown(): void
    {
        $this->validator = null;
    }

    public function testIsValidWithValidTheme(): void
    {
        $isValid = $this->validator->isValid($this->getTheme());
        $this->assertTrue($isValid, self::NOTICE . sprintf('expected isValid to return true when theme is valid, got %s', gettype($isValid)));
    }

    public function testIsValidWithInvalidThemeMissingFiles(): void
    {
        $isValid = $this->validator->isValid($this->getTheme('missfiles'));
        $this->assertFalse($isValid, self::NOTICE . sprintf('expected isValid to return false when theme is invalid, got %s', gettype($isValid)));
    }

    public function testIsValidWithInvalidThemeMissingProperties()
    {
        $isValid = $this->validator->isValid($this->getTheme('missconfig'));
        $this->assertFalse($isValid, self::NOTICE . sprintf('expected isValid to return false when theme is invalid, got %s', gettype($isValid)));
    }

    private function getTheme(string $name = 'valid'): Theme
    {
        $options = ['valid', 'missfiles', 'missconfig'];

        if (!in_array($name, $options)) {
            throw new InvalidArgumentException(self::NOTICE . 'getTheme($name) only accepts specified arguments');
        }
        $themeDir = __DIR__ . '/../../../../Resources/themes/minimal-' . $name . '-theme/';
        $themeConfigFile = $themeDir . 'config/theme.yml';

        try {
            $themeConfigContent = file_get_contents($themeConfigFile);
        } catch (Throwable $exception) {
            throw new RuntimeException(sprintf('Unable to read theme config file %s', $themeConfigFile));
        }

        $config = (new Parser())->parse(file_get_contents($themeConfigFile));
        $config['directory'] = $themeDir;
        $config['physical_uri'] = '/';

        $theme = new Theme($config);

        return $theme;
    }
}
