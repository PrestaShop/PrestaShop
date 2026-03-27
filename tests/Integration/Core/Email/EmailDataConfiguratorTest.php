<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Email;

use Configuration as LegacyConfiguration;
use PrestaShop\PrestaShop\Adapter\Configuration as ConfigurationAdapter;
use PrestaShop\PrestaShop\Core\Email\EmailDataConfigurator;
use Tests\Resources\DatabaseDump;
use Tests\TestCase\AbstractConfigurationTestCase;

/**
 * @group email
 */
class EmailDataConfiguratorTest extends AbstractConfigurationTestCase
{
    private EmailDataConfigurator $emailDataConfigurator;
    private ConfigurationAdapter $configuration;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::resetConfiguration();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        static::resetConfiguration();
    }

    protected static function resetConfiguration(): void
    {
        DatabaseDump::restoreTables([
            'configuration',
            'configuration_lang',
        ]);

        LegacyConfiguration::resetStaticCache();
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->configuration = new ConfigurationAdapter();
        $this->emailDataConfigurator = new EmailDataConfigurator($this->configuration);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        // Clean up the test password
        LegacyConfiguration::deleteByName('PS_MAIL_PASSWD');
        LegacyConfiguration::resetStaticCache();
    }

    /**
     * Test that SMTP password with special characters is saved correctly.
     *
     * @dataProvider passwordWithSpecialCharactersProvider
     *
     * @see https://github.com/PrestaShop/PrestaShop/issues/22540
     */
    public function testSmtpPasswordWithSpecialCharactersIsSavedCorrectly(string $password): void
    {
        // Arrange
        $config = $this->getValidConfiguration($password);

        // Act
        $this->emailDataConfigurator->updateConfiguration($config);

        // Assert
        $savedPassword = LegacyConfiguration::get('PS_MAIL_PASSWD');
        $this->assertSame(
            $password,
            $savedPassword,
            sprintf(
                'Password should be saved exactly as provided. Expected "%s", got "%s"',
                $password,
                $savedPassword
            )
        );
    }

    /**
     * Data provider for passwords with special characters.
     */
    public function passwordWithSpecialCharactersProvider(): array
    {
        return [
            'password with less than sign' => ['abcd<efgh'],
            'password with greater than sign' => ['abcd>efgh'],
            'password with both signs' => ['<password>'],
            'password with HTML-like content' => ['my<script>password'],
            'password with ampersand' => ['pass&word'],
            'password with quotes' => ['pass"word\'test'],
            'complex password' => ['P@ss<w0rd>&"test\'123'],
            'simple password' => ['simplepassword'],
            'empty password' => [''],
        ];
    }

    /**
     * Get a valid configuration array for testing.
     */
    private function getValidConfiguration(string $password): array
    {
        return [
            'send_emails_to' => 1,
            'mail_method' => 2,
            'subject_prefix' => true,
            'mail_type' => 1,
            'log_emails' => false,
            'dkim_enable' => false,
            'dkim_config' => [
                'domain' => '',
                'selector' => '',
                'key' => '',
            ],
            'smtp_config' => [
                'domain' => 'example.com',
                'server' => 'smtp.example.com',
                'username' => 'user@example.com',
                'password' => $password,
                'encryption' => 'tls',
                'port' => '587',
            ],
        ];
    }
}
