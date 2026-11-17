<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Email;

use Configuration as LegacyConfiguration;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Email\EmailDataConfigurator;
use PrestaShop\PrestaShop\Core\Email\MailOption;
use PrestaShopBundle\Entity\Shop;
use PrestaShopBundle\Entity\ShopGroup;
use Shop as LegacyShop;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

class EmailDataConfiguratorTest extends KernelTestCase
{
    private Configuration $configuration;
    private int $shopId;
    private int $shopGroupId;
    private int $otherShopId;

    protected function setUp(): void
    {
        parent::setUp();
        self::resetConfiguration();
        self::bootKernel();

        $container = self::$kernel->getContainer();
        $this->configuration = $container->get('prestashop.adapter.legacy.configuration');
        $this->shopId = (int) LegacyConfiguration::getGlobalValue('PS_SHOP_DEFAULT');
        $this->shopGroupId = (int) LegacyShop::getGroupIdFromShopId($this->shopId);
        $this->configuration->set('PS_MULTISHOP_FEATURE_ACTIVE', 1, ShopConstraint::allShops());

        $entityManager = $container->get('doctrine.orm.entity_manager');
        $shop = new Shop();
        $shop->setActive(true);
        $shop->setIdCategory(2);
        $shop->setName('email_configuration_test_shop');
        $shop->setShopGroup($entityManager->find(ShopGroup::class, $this->shopGroupId));
        $shop->setColor('red');
        $shop->setThemeName(Theme::getDefaultTheme());
        $shop->setDeleted(false);
        $entityManager->persist($shop);
        $entityManager->flush();
        $this->otherShopId = $shop->getId();
        LegacyShop::resetStaticCache();

        foreach (['PS_MAIL_SERVER' => 'global-server', 'PS_MAIL_PASSWD' => 'global-password', 'PS_MAIL_DKIM_KEY' => 'global-key'] as $key => $value) {
            $this->configuration->set($key, $value, ShopConstraint::allShops());
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        self::resetConfiguration();
    }

    /**
     * @dataProvider provideContexts
     */
    public function testSaveConfiguration(bool $groupContext): void
    {
        $configurator = $this->createConfigurator($groupContext);
        $expected = [
            'send_emails_to' => '1',
            'mail_method' => MailOption::METHOD_SMTP,
            'subject_prefix' => true,
            'mail_type' => MailOption::TYPE_BOTH,
            'log_emails' => true,
            'smtp_config' => [
                'domain' => 'mail.example.com',
                'server' => 'local-server',
                'username' => 'smtp-user',
                'password' => 'local-password',
                'encryption' => 'tls',
                'port' => '587',
            ],
            'dkim_enable' => true,
            'dkim_config' => [
                'domain' => 'example.com',
                'selector' => 'mail',
                'key' => 'local-key',
            ],
        ];
        $input = $expected;
        foreach ($expected as $field => $value) {
            if (is_array($value)) {
                foreach (array_keys($value) as $nestedField) {
                    $input[$field]['multistore_' . $nestedField] = true;
                }
            } else {
                $input['multistore_' . $field] = true;
            }
        }

        self::assertSame([], $configurator->updateConfiguration($input));
        LegacyConfiguration::resetStaticCache();
        self::assertSame($expected, $configurator->getConfiguration());
        foreach (['PS_MAIL_METHOD', 'PS_MAIL_SERVER', 'PS_MAIL_PASSWD', 'PS_MAIL_DKIM_KEY'] as $key) {
            self::assertTrue($this->configuration->has($key, $configurator->getShopConstraint()->clone(true)));
        }
        self::assertSame('global-password', LegacyConfiguration::getGlobalValue('PS_MAIL_PASSWD'));
        self::assertSame('global-server', LegacyConfiguration::getGlobalValue('PS_MAIL_SERVER'));

        // The sibling shop sees a group update, but must not see a single-shop update.
        self::assertSame(
            $groupContext ? 'local-server' : 'group-server',
            LegacyConfiguration::get('PS_MAIL_SERVER', null, $this->shopGroupId, $this->otherShopId)
        );
    }

    /**
     * @dataProvider provideOverrideResets
     */
    public function testRemoveOverridesRestoresInheritedValues(bool $groupContext, array $flag): void
    {
        $configurator = $this->createConfigurator($groupContext);
        $scope = $configurator->getShopConstraint();
        foreach (['PS_MAIL_SERVER' => 'local-server', 'PS_MAIL_PASSWD' => 'local-password', 'PS_MAIL_DKIM_KEY' => 'local-key'] as $key => $value) {
            $this->configuration->set($key, $value, $scope);
            self::assertTrue($this->configuration->has($key, $scope->clone(true)));
        }
        self::assertSame('local-password', $configurator->getConfiguration()['smtp_config']['password']);

        self::assertSame([], $configurator->updateConfiguration([
            'smtp_config' => [
                'server' => 'local-server',
                'password' => '',
            ] + $flag,
            'dkim_config' => ['key' => 'local-key'],
        ]));
        LegacyConfiguration::resetStaticCache();

        $settings = $configurator->getConfiguration();
        foreach (['PS_MAIL_SERVER', 'PS_MAIL_PASSWD', 'PS_MAIL_DKIM_KEY'] as $key) {
            self::assertFalse($this->configuration->has($key, $scope->clone(true)));
        }
        $prefix = $groupContext ? 'global' : 'group';
        self::assertSame($prefix . '-server', $settings['smtp_config']['server']);
        self::assertSame($prefix . '-password', $settings['smtp_config']['password']);
        self::assertSame($prefix . '-key', $settings['dkim_config']['key']);
        self::assertSame('global-password', LegacyConfiguration::getGlobalValue('PS_MAIL_PASSWD'));
    }

    /**
     * @dataProvider provideExistingPasswords
     */
    public function testBlankPasswordPreservesExistingOverride(bool $groupContext, string $password): void
    {
        $configurator = $this->createConfigurator($groupContext);
        $scope = $configurator->getShopConstraint();
        $this->configuration->set('PS_MAIL_PASSWD', $password, $scope);

        self::assertSame([], $configurator->updateConfiguration([
            'smtp_config' => ['password' => '', 'multistore_password' => true],
        ]));
        LegacyConfiguration::resetStaticCache();

        $settings = $configurator->getConfiguration();
        self::assertTrue($this->configuration->has('PS_MAIL_PASSWD', $scope->clone(true)));
        self::assertSame($password, $settings['smtp_config']['password']);
        self::assertSame('global-password', LegacyConfiguration::getGlobalValue('PS_MAIL_PASSWD'));
    }

    /**
     * @dataProvider provideContexts
     */
    public function testBlankPasswordCreatesEmptyOverride(bool $groupContext): void
    {
        $configurator = $this->createConfigurator($groupContext);
        $scope = $configurator->getShopConstraint();
        self::assertFalse($this->configuration->has('PS_MAIL_PASSWD', $scope->clone(true)));
        self::assertSame(
            $groupContext ? 'global-password' : 'group-password',
            $configurator->getConfiguration()['smtp_config']['password']
        );

        self::assertSame([], $configurator->updateConfiguration([
            'smtp_config' => ['password' => '', 'multistore_password' => true],
        ]));
        LegacyConfiguration::resetStaticCache();

        $settings = $configurator->getConfiguration();
        self::assertTrue($this->configuration->has('PS_MAIL_PASSWD', $scope->clone(true)));
        self::assertSame('', $settings['smtp_config']['password']);
        self::assertSame('global-password', LegacyConfiguration::getGlobalValue('PS_MAIL_PASSWD'));
    }

    /**
     * @dataProvider provideContexts
     */
    public function testUpdatePassword(bool $groupContext): void
    {
        $configurator = $this->createConfigurator($groupContext);
        $scope = $configurator->getShopConstraint();
        $this->configuration->set('PS_MAIL_PASSWD', 'previous-password', $scope);

        self::assertSame([], $configurator->updateConfiguration([
            'smtp_config' => ['password' => 'new+password%42', 'multistore_password' => true],
        ]));
        LegacyConfiguration::resetStaticCache();

        $settings = $configurator->getConfiguration();
        self::assertTrue($this->configuration->has('PS_MAIL_PASSWD', $scope->clone(true)));
        self::assertSame('new+password%42', $settings['smtp_config']['password']);
        self::assertSame('global-password', LegacyConfiguration::getGlobalValue('PS_MAIL_PASSWD'));
    }

    public function provideContexts(): iterable
    {
        yield 'shop' => [false];
        yield 'shop group' => [true];
    }

    public function provideOverrideResets(): iterable
    {
        foreach ($this->provideContexts() as $context => [$groupContext]) {
            yield $context . ', absent flag' => [$groupContext, []];
            yield $context . ', false flag' => [$groupContext, ['multistore_password' => false]];
            yield $context . ', null flag' => [$groupContext, ['multistore_password' => null]];
        }
    }

    public function provideExistingPasswords(): iterable
    {
        foreach ($this->provideContexts() as $context => [$groupContext]) {
            yield $context . ', nonempty password' => [$groupContext, 'local-password'];
            yield $context . ', zero password' => [$groupContext, '0'];
        }
    }

    private function createConfigurator(bool $groupContext): EmailDataConfigurator
    {
        if ($groupContext) {
            LegacyShop::setContext(LegacyShop::CONTEXT_GROUP, $this->shopGroupId);
        } else {
            foreach (['PS_MAIL_SERVER' => 'group-server', 'PS_MAIL_PASSWD' => 'group-password', 'PS_MAIL_DKIM_KEY' => 'group-key'] as $key => $value) {
                $this->configuration->set($key, $value, ShopConstraint::shopGroup($this->shopGroupId));
            }
            LegacyShop::setContext(LegacyShop::CONTEXT_SHOP, $this->shopId);
        }

        return new EmailDataConfigurator(
            $this->configuration,
            new Context(),
            self::$kernel->getContainer()->get('prestashop.adapter.multistore_feature')
        );
    }

    private static function resetConfiguration(): void
    {
        DatabaseDump::restoreTables(['configuration', 'configuration_lang', 'shop']);
        LegacyConfiguration::resetStaticCache();
        LegacyShop::resetStaticCache();
        LegacyShop::setContext(LegacyShop::CONTEXT_ALL);
    }
}
