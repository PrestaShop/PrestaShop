<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\DependencyInjection;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Scheb\TwoFactorBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;

class TwoFactorConfigurationTest extends TestCase
{
    #[DataProvider('kernelEnvironments')]
    public function testProvidersAreOnlyEnabledInTheAdminKernel(string $kernel, string $environment): void
    {
        if (!class_exists(Configuration::class)) {
            self::markTestSkipped('The locked Scheb dependencies must be installed to process their configuration.');
        }

        $file = dirname(__DIR__, 4) . '/app/config/' . $kernel . '/config_' . $environment . '.yml';
        $configuration = (new Processor())->processConfiguration(new Configuration(), $this->loadConfigurations($file));

        foreach (['totp', 'email', 'backup_codes', 'trusted_device'] as $feature) {
            self::assertSame($kernel === 'admin', $configuration[$feature]['enabled'], $feature);
        }
        if ('admin' === $kernel) {
            self::assertSame('PrestaShopBundle\SchebTwoFactor\TrustedDeviceTokenFactory', $configuration['two_factor_token_factory']);
            self::assertSame('PrestaShopBundle\SchebTwoFactor\EmployeeTrustedDeviceManager', $configuration['trusted_device']['manager']);
        } else {
            self::assertNotSame('PrestaShopBundle\SchebTwoFactor\TrustedDeviceTokenFactory', $configuration['two_factor_token_factory']);
            self::assertNotSame('PrestaShopBundle\SchebTwoFactor\EmployeeTrustedDeviceManager', $configuration['trusted_device']['manager']);
        }
    }

    public static function kernelEnvironments(): iterable
    {
        foreach (['admin', 'admin-api', 'front'] as $kernel) {
            foreach (['dev', 'prod', 'test'] as $environment) {
                yield $kernel . '/' . $environment => [$kernel, $environment];
            }
        }
    }

    /**
     * Collect extension configuration in import order, without booting the database-dependent kernels.
     *
     * @return array<int, array<string, mixed>>
     */
    private function loadConfigurations(string $file): array
    {
        $data = Yaml::parseFile($file, Yaml::PARSE_CONSTANT | Yaml::PARSE_CUSTOM_TAGS);
        $configurations = [];
        foreach ($data['imports'] ?? [] as $import) {
            foreach (glob(dirname($file) . '/' . $import['resource']) ?: [] as $resource) {
                if (in_array(pathinfo($resource, PATHINFO_EXTENSION), ['yml', 'yaml'], true)) {
                    array_push($configurations, ...$this->loadConfigurations($resource));
                }
            }
        }
        if (isset($data['scheb_two_factor'])) {
            $configurations[] = $data['scheb_two_factor'];
        }

        return $configurations;
    }
}
