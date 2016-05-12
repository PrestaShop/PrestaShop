<?php
/**
 * 2007-2015 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Tests\Core\Addon;

use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeValidator;
use Symfony\Component\Yaml\Parser;

class ThemeValidatorTest extends \PHPUnit_Framework_TestCase
{
    const NOTICE = '[ThemeValidator] ';

    private $validator;

    protected function setUp()
    {
        /* @var \PrestaShop\PrestaShop\Core\Addon\Theme\ThemeValidator */
        $this->validator = new ThemeValidator();
    }

    protected function tearDown()
    {
        $this->validator = null;
    }

    public function testIsValidWithValidTheme()
    {
        $isValid = $this->validator->isValid($this->getTheme());
        $this->assertTrue($isValid, self::NOTICE.sprintf('expected isValid to return true when theme is valid, got %s', gettype($isValid)));
    }

    public function testIsValidWithInvalidThemeMissingFiles()
    {
        $isValid = $this->validator->isValid($this->getTheme('missfiles'));
        $this->assertFalse($isValid, self::NOTICE.sprintf('expected isValid to return false when theme is invalid, got %s', gettype($isValid)));
    }

    public function testIsValidWithInvalidThemeMissingProperties()
    {
        $isValid = $this->validator->isValid($this->getTheme('missconfig'));
        $this->assertFalse($isValid, self::NOTICE.sprintf('expected isValid to return false when theme is invalid, got %s', gettype($isValid)));
    }

    private function getTheme($name = 'valid')
    {
        $options = ['valid', 'missfiles', 'missconfig'];

        if (!in_array($name, $options)) {
            throw new \InvalidArgumentException(self::NOTICE.'getTheme($name) only accepts specified arguments');
        }
        $themeDir = __DIR__. '/../../../../resources/minimal-'.$name.'-theme/';
        $themeConfigFile = $themeDir.'config/theme.yml';

        $config = (new Parser())->parse(file_get_contents($themeConfigFile));
        $config['directory'] = $themeDir;
        $config['physical_uri'] = '/prestashop.unit.test';

        $theme = new Theme($config);

        return $theme;
    }
}
