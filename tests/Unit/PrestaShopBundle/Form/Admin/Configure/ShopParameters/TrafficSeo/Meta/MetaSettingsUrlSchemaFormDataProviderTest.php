<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Form\Admin\Configure\ShopParameters\TrafficSeo\Meta;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Routes\RouteValidator;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShopBundle\Form\Admin\Configure\ShopParameters\TrafficSeo\Meta\MetaSettingsUrlSchemaFormDataProvider;
use Symfony\Contracts\Translation\TranslatorInterface;

class MetaSettingsUrlSchemaFormDataProviderTest extends TestCase
{
    /**
     * Routes are now translatable, so each route value is a [langId => pattern] array. The form also
     * adds top-level "multistore_<route>" checkbox helper fields which are plain booleans. Those must
     * be skipped before iterating the per-language patterns, otherwise validation raises
     * "foreach() argument must be of type array|object, bool given".
     */
    public function testSetDataSkipsMultistoreCheckboxHelpersWithoutFailing(): void
    {
        $provider = new MetaSettingsUrlSchemaFormDataProvider(
            $this->mockDataConfiguration([]),
            $this->mockTranslator(),
            $this->mockRouteValidator(true, ['missing' => [], 'unknown' => []])
        );

        $data = [
            // Multistore checkbox helper fields (top-level booleans) added by the form.
            'multistore_product_rule' => true,
            'multistore_category_rule' => false,
            // Actual per-language route patterns.
            'product_rule' => [1 => '{id}-{rewrite}.html', 2 => '{id}-{rewrite}.html'],
            'category_rule' => [1 => '{id}-{rewrite}', 2 => '{id}-{rewrite}'],
        ];

        // If the checkbox helpers are not skipped, iterating their boolean value raises
        // "foreach() argument must be of type array|object, bool given", which becomes a fatal
        // error in the form context. Capture warnings to assert it does not happen.
        $warnings = [];
        set_error_handler(static function (int $severity, string $message) use (&$warnings): bool {
            $warnings[] = $message;

            return false;
        }, E_WARNING);

        try {
            $result = $provider->setData($data);
        } finally {
            restore_error_handler();
        }

        $foreachWarnings = array_filter($warnings, static fn (string $m): bool => str_contains($m, 'foreach()'));
        $this->assertEmpty($foreachWarnings, 'Multistore checkbox helper fields must be skipped before iterating route patterns.');
        // No validation errors: setData returns whatever updateConfiguration returns (here []).
        $this->assertSame([], $result);
    }

    public function testSetDataValidatesEveryLanguageOfEveryRoute(): void
    {
        $routeValidator = $this->createMock(RouteValidator::class);
        $routeValidator->method('isRouteValid')->willReturn(['missing' => [], 'unknown' => []]);
        // Only the second language of the product route is an invalid pattern.
        $routeValidator->method('isRoutePattern')->willReturnCallback(
            static fn ($pattern): bool => $pattern !== 'invalid pattern'
        );

        $provider = new MetaSettingsUrlSchemaFormDataProvider(
            $this->mockDataConfiguration([]),
            $this->mockTranslator(),
            $routeValidator
        );

        $data = [
            'product_rule' => [1 => '{id}-{rewrite}.html', 2 => 'invalid pattern'],
        ];

        $result = $provider->setData($data);

        $this->assertNotEmpty($result, 'An invalid pattern in any language must produce a validation error.');
    }

    private function mockDataConfiguration(array $updateResult): DataConfigurationInterface
    {
        $mock = $this->createMock(DataConfigurationInterface::class);
        $mock->method('updateConfiguration')->willReturn($updateResult);

        return $mock;
    }

    private function mockTranslator(): TranslatorInterface
    {
        $mock = $this->createMock(TranslatorInterface::class);
        $mock->method('trans')->willReturnArgument(0);

        return $mock;
    }

    private function mockRouteValidator(bool $isPattern, array $isValid): RouteValidator
    {
        $mock = $this->createMock(RouteValidator::class);
        $mock->method('isRoutePattern')->willReturn($isPattern);
        $mock->method('isRouteValid')->willReturn($isValid);

        return $mock;
    }
}
