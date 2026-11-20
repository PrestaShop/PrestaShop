<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Security;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Security\PasswordPolicyConfiguration;

/**
 * @see https://github.com/PrestaShop/PrestaShop/issues/29309
 */
class PasswordPolicyConfigurationTest extends TestCase
{
    public function testGetConfigurationReadsFromAllShopsScope(): void
    {
        $configuration = $this->createMock(Configuration::class);
        $configuration
            ->method('get')
            ->willReturnCallback(function ($key, $default = null, $shopConstraint = null) {
                $this->assertEquals(ShopConstraint::allShops(), $shopConstraint);

                return [
                    PasswordPolicyConfiguration::CONFIGURATION_MINIMUM_LENGTH => 8,
                    PasswordPolicyConfiguration::CONFIGURATION_MAXIMUM_LENGTH => 72,
                    PasswordPolicyConfiguration::CONFIGURATION_MINIMUM_SCORE => 3,
                ][$key];
            });

        $result = (new PasswordPolicyConfiguration($configuration))->getConfiguration();

        $this->assertSame(
            [
                'minimum_length' => 8,
                'maximum_length' => 72,
                'minimum_score' => 3,
            ],
            $result
        );
    }

    public function testUpdateConfigurationWritesToAllShopsScope(): void
    {
        $configuration = $this->createMock(Configuration::class);

        $calls = [];
        $configuration
            ->method('remove')
            ->willReturnCallback(function ($key) use (&$calls, $configuration) {
                $calls[] = ['remove', $key];

                return $configuration;
            });
        $configuration
            ->method('set')
            ->willReturnCallback(function ($key, $value, $shopConstraint = null) use (&$calls, $configuration) {
                $calls[] = ['set', $key, $value, $shopConstraint];

                return $configuration;
            });

        (new PasswordPolicyConfiguration($configuration))->updateConfiguration([
            'minimum_length' => 10,
            'maximum_length' => 50,
            'minimum_score' => 4,
        ]);

        $expected = [
            ['remove', PasswordPolicyConfiguration::CONFIGURATION_MINIMUM_SCORE],
            ['set', PasswordPolicyConfiguration::CONFIGURATION_MINIMUM_SCORE, 4, ShopConstraint::allShops()],
            ['remove', PasswordPolicyConfiguration::CONFIGURATION_MINIMUM_LENGTH],
            ['set', PasswordPolicyConfiguration::CONFIGURATION_MINIMUM_LENGTH, 10, ShopConstraint::allShops()],
            ['remove', PasswordPolicyConfiguration::CONFIGURATION_MAXIMUM_LENGTH],
            ['set', PasswordPolicyConfiguration::CONFIGURATION_MAXIMUM_LENGTH, 50, ShopConstraint::allShops()],
        ];
        $this->assertEquals($expected, $calls);
    }
}
