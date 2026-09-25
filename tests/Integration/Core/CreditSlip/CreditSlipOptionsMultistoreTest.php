<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\CreditSlip;

use Db;
use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use PrestaShopBundle\Form\Admin\Sell\Order\CreditSlip\CreditSlipOptionsType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * The credit slip prefix is the sibling of the invoice prefix, which is configured through the
 * multistore base and carries the standard per-shop override. These pin the credit slip one to the
 * same treatment, so it cannot drift back to a shop-blind read and write.
 */
class CreditSlipOptionsMultistoreTest extends KernelTestCase
{
    private const SERVICE = 'prestashop.core.credit_slip.credit_slip_options.configuration';

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        global $kernel;
        $kernel = self::$kernel;
    }

    public function testTheConfigurationIsShopAware(): void
    {
        $configuration = self::getContainer()->get(self::SERVICE);

        $this->assertInstanceOf(
            AbstractMultistoreConfiguration::class,
            $configuration,
            'The credit slip options no longer go through the multistore configuration base, so the prefix is read and written without a shop constraint.'
        );
    }

    /**
     * The conversion must not break saving. This asserts the stored row rather than reading the value
     * back through the configuration service, because that read is cached within the request and
     * would be measuring the cache rather than the write.
     */
    public function testThePrefixIsStillPersisted(): void
    {
        $configuration = self::getContainer()->get(self::SERVICE);

        $before = $configuration->getConfiguration();
        $this->assertArrayHasKey('slip_prefix', $before, 'The prefix is no longer exposed by the configuration.');

        try {
            $configuration->updateConfiguration(['slip_prefix' => ['1' => 'ZZTEST']]);

            $stored = Db::getInstance()->getValue(
                'SELECT cl.value FROM ' . _DB_PREFIX_ . 'configuration c'
                . ' JOIN ' . _DB_PREFIX_ . 'configuration_lang cl ON cl.id_configuration = c.id_configuration'
                . " WHERE c.name = 'PS_CREDIT_SLIP_PREFIX'"
            );

            $this->assertSame('ZZTEST', $stored, 'The prefix was not written through the multistore configuration base.');
        } finally {
            $configuration->updateConfiguration(['slip_prefix' => is_array($before['slip_prefix']) ? $before['slip_prefix'] : ['1' => '']]);
        }
    }

    /**
     * Being shop-aware is only half of it: the field has to declare the key, or the form renders no
     * override control and the merchant has no way to say which shop the value belongs to.
     */
    public function testTheFormFieldDeclaresTheMultistoreKey(): void
    {
        /** @var FormFactoryInterface $formFactory */
        $formFactory = self::getContainer()->get('form.factory');

        $form = $formFactory->create(CreditSlipOptionsType::class, null, ['csrf_protection' => false]);

        $options = $form->get('slip_prefix')->getConfig()->getOptions();

        $this->assertArrayHasKey('multistore_configuration_key', $options);
        $this->assertSame('PS_CREDIT_SLIP_PREFIX', $options['multistore_configuration_key']);
    }
}
