<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Core\Module\Parser;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Module\Parser\ModuleParser;
use PrestaShop\PrestaShop\Core\Module\Parser\ModuleParserException;
use PrestaShop\PrestaShop\Core\Version;

class ModuleParserTest extends TestCase
{
    private const PARSED_MODULES_FOLDER = __DIR__ . '/../../../Resources/parsed-modules/';

    /**
     * @dataProvider getModules
     */
    public function testParseModule(string $moduleClassPath, array $expectedInfos, ?array $extractedModuleProperties = null): void
    {
        if (null !== $extractedModuleProperties) {
            $parser = new ModuleParser($extractedModuleProperties);
        } else {
            $parser = new ModuleParser();
        }
        $moduleInfos = $parser->parseModule($moduleClassPath);
        $this->assertEquals($expectedInfos, $moduleInfos);
    }

    public static function getModules(): iterable
    {
        yield 'all hard-coded in constructor' => [
            self::PARSED_MODULES_FOLDER . 'all-hard-coded.php',
            [
                'name' => 'bankwire',
                'tab' => 'payments_gateways',
                'version' => '2.0.0',
                'ps_versions_compliancy' => [
                    'min' => '1.7',
                    'max' => '8.2.0',
                ],
                'author' => 'PrestaShop',
                'displayName' => 'Bank wire',
                'description' => 'Accept payments for your products via bank wire transfer.',
                'author_uri' => '',
                'dependencies' => [],
                'description_full' => '',
                'is_configurable' => false,
                'need_instance' => true,
                'limited_countries' => [],
                'hooks' => [
                    'paymentReturn',
                    'paymentOptions',
                    'displayHome',
                ],
            ],
        ];

        yield 'core version from _PS_VERSION_ defined const' => [
            self::PARSED_MODULES_FOLDER . 'defined-const.php',
            [
                'name' => 'bankwire',
                'tab' => 'payments_gateways',
                'version' => '2.0.0',
                'ps_versions_compliancy' => [
                    'min' => '1.7',
                    'max' => _PS_VERSION_,
                ],
                'author' => 'PrestaShop',
                'displayName' => 'Bank wire',
                'description' => 'Accept payments for your products via bank wire transfer.',
                'author_uri' => '',
                'dependencies' => [],
                'description_full' => '',
                'is_configurable' => false,
                'need_instance' => true,
                'limited_countries' => [],
                'hooks' => [
                    'paymentReturn',
                    'paymentOptions',
                    'displayHome',
                ],
            ],
        ];

        yield 'core version from FQCN \PrestaShop\PrestaShop\Core\Version::VERSION' => [
            self::PARSED_MODULES_FOLDER . 'fqcn-const.php',
            [
                'name' => 'bankwire',
                'tab' => 'payments_gateways',
                'version' => '2.0.0',
                'ps_versions_compliancy' => [
                    'min' => '1.7',
                    'max' => Version::VERSION,
                ],
                'author' => 'PrestaShop',
                'displayName' => 'Bank wire',
                'description' => 'Accept payments for your products via bank wire transfer.',
                'author_uri' => '',
                'dependencies' => [],
                'description_full' => '',
                'is_configurable' => false,
                'need_instance' => true,
                'limited_countries' => [],
                'hooks' => [
                    'paymentReturn',
                    'paymentOptions',
                    'displayHome',
                ],
            ],
        ];

        yield 'core version from FQCN PrestaShop\PrestaShop\Core\Version::VERSION, no initial backslash' => [
            self::PARSED_MODULES_FOLDER . 'cs-fixed-fqcn-const.php',
            [
                'name' => 'bankwire',
                'tab' => 'payments_gateways',
                'version' => '2.0.0',
                'ps_versions_compliancy' => [
                    'min' => '1.7',
                    'max' => Version::VERSION,
                ],
                'author' => 'PrestaShop',
                'displayName' => 'Bank wire',
                'description' => 'Accept payments for your products via bank wire transfer.',
                'author_uri' => '',
                'dependencies' => [],
                'description_full' => '',
                'is_configurable' => false,
                'need_instance' => true,
                'limited_countries' => [],
                'hooks' => [
                    'paymentReturn',
                    'paymentOptions',
                    'displayHome',
                ],
            ],
        ];

        yield 'core version from Version object with use statement' => [
            self::PARSED_MODULES_FOLDER . 'use-statement-const.php',
            [
                'name' => 'bankwire',
                'tab' => 'payments_gateways',
                'version' => '2.0.0',
                'ps_versions_compliancy' => [
                    'min' => '1.7',
                    'max' => Version::VERSION,
                ],
                'author' => 'PrestaShop',
                'displayName' => 'Bank wire',
                'description' => 'Accept payments for your products via bank wire transfer.',
                'author_uri' => '',
                'dependencies' => [],
                'description_full' => '',
                'is_configurable' => false,
                'need_instance' => true,
                'limited_countries' => [],
                'hooks' => [
                    'paymentReturn',
                    'paymentOptions',
                    'displayHome',
                ],
            ],
        ];

        yield 'module values from module const accessed via self' => [
            self::PARSED_MODULES_FOLDER . 'module-self-const.php',
            [
                'name' => 'bankwire',
                'tab' => 'payments_gateways',
                'version' => '2.1.0',
                'ps_versions_compliancy' => [
                    'min' => '1.7',
                    'max' => '8.2.0',
                ],
                'author' => 'PrestaShop',
                'displayName' => 'Bank wire',
                'description' => 'Accept payments for your products via bank wire transfer.',
                'author_uri' => '',
                'dependencies' => [],
                'description_full' => '',
                'is_configurable' => false,
                'need_instance' => true,
                'limited_countries' => [],
                'hooks' => [
                    'paymentReturn',
                    'paymentOptions',
                    'displayHome',
                ],
            ],
        ];

        yield 'module values from module const accessed via static' => [
            self::PARSED_MODULES_FOLDER . 'module-static-const.php',
            [
                'name' => 'bankwire',
                'tab' => 'payments_gateways',
                'version' => '2.1.0',
                'ps_versions_compliancy' => [
                    'min' => '1.7',
                    'max' => '8.2.0',
                ],
                'author' => 'PrestaShop',
                'displayName' => 'Bank wire',
                'description' => 'Accept payments for your products via bank wire transfer.',
                'author_uri' => '',
                'dependencies' => [],
                'description_full' => '',
                'is_configurable' => false,
                'need_instance' => true,
                'limited_countries' => [],
                'hooks' => [
                    'paymentReturn',
                    'paymentOptions',
                    'displayHome',
                ],
            ],
        ];

        yield 'translated values' => [
            self::PARSED_MODULES_FOLDER . 'translated-values.php',
            [
                'name' => 'bankwire',
                'tab' => 'payments_gateways',
                'version' => '2.0.0',
                'ps_versions_compliancy' => [
                    'min' => '1.7',
                    'max' => '8.2.0',
                ],
                'author' => 'PrestaShop',
                'displayName' => 'Bank wire',
                'description' => 'Accept payments for your products via bank wire transfer.',
                'author_uri' => '',
                'dependencies' => [],
                'description_full' => '',
                'is_configurable' => false,
                'need_instance' => true,
                'limited_countries' => [],
                'hooks' => [
                    'paymentReturn',
                    'paymentOptions',
                    'displayHome',
                ],
            ],
        ];

        yield 'module with const arrays' => [
            self::PARSED_MODULES_FOLDER . 'module-const-array.php',
            [
                'name' => 'bankwire',
                'tab' => 'payments_gateways',
                'version' => '2.0.0',
                'ps_versions_compliancy' => [
                    'min' => '1.7',
                    'max' => '8.2.0',
                ],
                'author' => 'PrestaShop',
                'displayName' => 'Bank wire',
                'description' => 'Accept payments for your products via bank wire transfer.',
                'author_uri' => '',
                'dependencies' => [],
                'description_full' => '',
                'is_configurable' => false,
                'need_instance' => true,
                'limited_countries' => [],
                'hooks' => [
                    'paymentReturn',
                    'paymentOptions',
                    'displayHome',
                ],
            ],
        ];

        yield 'all hard-coded in constructor, limit extracted properties, without hooks' => [
            self::PARSED_MODULES_FOLDER . 'all-hard-coded.php',
            [
                'name' => 'bankwire',
                'tab' => 'payments_gateways',
                'version' => '2.0.0',
                'ps_versions_compliancy' => [
                    'min' => '1.7',
                    'max' => '8.2.0',
                ],
            ],
            [
                'name',
                'tab',
                'version',
                'ps_versions_compliancy',
            ],
        ];

        yield 'all hard-coded in constructor, limit extracted properties with hooks' => [
            self::PARSED_MODULES_FOLDER . 'all-hard-coded.php',
            [
                'name' => 'bankwire',
                'tab' => 'payments_gateways',
                'version' => '2.0.0',
                'ps_versions_compliancy' => [
                    'min' => '1.7',
                    'max' => '8.2.0',
                ],
                'hooks' => [
                    'paymentReturn',
                    'paymentOptions',
                    'displayHome',
                ],
            ],
            [
                'name',
                'tab',
                'version',
                'ps_versions_compliancy',
                'hooks',
            ],
        ];

        yield 'all hard-coded in constructor, no limitations with empty array' => [
            self::PARSED_MODULES_FOLDER . 'all-hard-coded.php',
            [
                'name' => 'bankwire',
                'tab' => 'payments_gateways',
                'version' => '2.0.0',
                'ps_versions_compliancy' => [
                    'min' => '1.7',
                    'max' => '8.2.0',
                ],
                'author' => 'PrestaShop',
                'displayName' => 'Bank wire',
                'description' => 'Accept payments for your products via bank wire transfer.',
                'author_uri' => '',
                'dependencies' => [],
                'description_full' => '',
                'is_configurable' => false,
                'need_instance' => true,
                'limited_countries' => [],
                'hooks' => [
                    'paymentReturn',
                    'paymentOptions',
                    'displayHome',
                ],
                // Additional properties
                'is_eu_compatible' => 1,
                'currencies' => true,
                'currencies_mode' => 'checkbox',
                'bootstrap' => true,
                'confirmUninstall' => 'Are you sure about removing these details?',
                'controllers' => [
                    'payment',
                    'validation',
                ],
            ],
            // Empty array means all the properties set in the constructor are extracted
            [],
        ];
    }

    public function testNoConstructor(): void
    {
        $this->expectException(ModuleParserException::class);
        $this->expectExceptionMessage('Module constructor not found');

        $parser = new ModuleParser();
        $parser->parseModule(self::PARSED_MODULES_FOLDER . 'no-constructor.php');
    }

    public function testPropertiesStringExpected(): void
    {
        $this->expectException(ModuleParserException::class);
        $this->expectExceptionMessage('List of extracted properties is expected to be an array of string');

        new ModuleParser([1, 2]);
    }

    public function testModuleFileNotFound(): void
    {
        $moduleClassPath = self::PARSED_MODULES_FOLDER . 'no-found.php';
        $this->expectException(ModuleParserException::class);
        $this->expectExceptionMessage('Module file not found: ' . $moduleClassPath);

        $parser = new ModuleParser();
        $parser->parseModule($moduleClassPath);
    }

    public function testCannotParseInvalid(): void
    {
        // This one was not named with php extension or PHPSTan goes crazy
        $moduleClassPath = self::PARSED_MODULES_FOLDER . 'cannot-parse.php.txt';
        $this->expectException(ModuleParserException::class);
        $this->expectExceptionMessage('Could not parse module file: ' . $moduleClassPath);

        $parser = new ModuleParser();
        $parser->parseModule($moduleClassPath);
    }

    public function testCannotParseEmpty(): void
    {
        $moduleClassPath = self::PARSED_MODULES_FOLDER . 'empty.php';
        $this->expectException(ModuleParserException::class);
        $this->expectExceptionMessage('Could not parse module file: ' . $moduleClassPath);

        $parser = new ModuleParser();
        $parser->parseModule($moduleClassPath);
    }
}
