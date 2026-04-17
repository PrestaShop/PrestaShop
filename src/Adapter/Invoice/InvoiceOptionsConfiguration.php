<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Invoice;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class InvoiceOptionsConfiguration is responsible for saving & loading invoice configuration.
 */
final class InvoiceOptionsConfiguration extends AbstractMultistoreConfiguration
{
    /**
     * @var FormChoiceProviderInterface
     */
    private $invoiceModelByNameChoiceProvider;

    /**
     * AbstractMultistoreConfiguration constructor.
     *
     * @param Configuration $configuration
     * @param Context $shopContext
     * @param FeatureInterface $multistoreFeature
     * @param FormChoiceProviderInterface $invoiceModelByNameChoiceProvider
     */
    public function __construct(
        Configuration $configuration,
        Context $shopContext,
        FeatureInterface $multistoreFeature,
        FormChoiceProviderInterface $invoiceModelByNameChoiceProvider
    ) {
        parent::__construct($configuration, $shopContext, $multistoreFeature);
        $this->invoiceModelByNameChoiceProvider = $invoiceModelByNameChoiceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            'enable_invoices' => (bool) $this->configuration->get('PS_INVOICE', true, $shopConstraint),
            'enable_tax_breakdown' => (bool) $this->configuration->get('PS_INVOICE_TAXES_BREAKDOWN', false, $shopConstraint),
            'enable_product_images' => (bool) $this->configuration->get('PS_PDF_IMG_INVOICE', false, $shopConstraint),
            'invoice_prefix' => $this->configuration->get('PS_INVOICE_PREFIX', ['#IN', '#FA'], $shopConstraint),
            'add_current_year' => (bool) $this->configuration->get('PS_INVOICE_USE_YEAR', false, $shopConstraint),
            'reset_number_annually' => (bool) $this->configuration->get('PS_INVOICE_RESET', false, $shopConstraint),
            'year_position' => (int) $this->configuration->get('PS_INVOICE_YEAR_POS', 0, $shopConstraint),
            'invoice_number' => (int) $this->configuration->get('PS_INVOICE_START_NUMBER', 0, $shopConstraint),
            'legal_free_text' => $this->configuration->get('PS_INVOICE_LEGAL_FREE_TEXT', null, $shopConstraint),
            'footer_text' => $this->configuration->get('PS_INVOICE_FREE_TEXT', null, $shopConstraint),
            'invoice_model' => $this->configuration->get('PS_INVOICE_MODEL', 'invoice', $shopConstraint),
            'use_disk_cache' => (bool) $this->configuration->get('PS_PDF_USE_CACHE', false, $shopConstraint),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        if ($this->validateConfiguration($configuration)) {
            $shopConstraint = $this->getShopConstraint();

            $this->updateConfigurationValue('PS_INVOICE', 'enable_invoices', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_INVOICE_TAXES_BREAKDOWN', 'enable_tax_breakdown', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_PDF_IMG_INVOICE', 'enable_product_images', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_INVOICE_PREFIX', 'invoice_prefix', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_INVOICE_USE_YEAR', 'add_current_year', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_INVOICE_RESET', 'reset_number_annually', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_INVOICE_YEAR_POS', 'year_position', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_INVOICE_START_NUMBER', 'invoice_number', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_INVOICE_LEGAL_FREE_TEXT', 'legal_free_text', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_INVOICE_FREE_TEXT', 'footer_text', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_INVOICE_MODEL', 'invoice_model', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_PDF_USE_CACHE', 'use_disk_cache', $configuration, $shopConstraint);
        }

        return [];
    }

    /**
     * @return OptionsResolver
     */
    protected function buildResolver(): OptionsResolver
    {
        $resolver = (new OptionsResolver())
            ->setDefined(
                [
                    'enable_invoices',
                    'enable_tax_breakdown',
                    'enable_product_images',
                    'invoice_prefix',
                    'add_current_year',
                    'reset_number_annually',
                    'year_position',
                    'invoice_number',
                    'legal_free_text',
                    'footer_text',
                    'invoice_model',
                    'use_disk_cache',
                ]
            )
            ->setAllowedTypes('enable_invoices', ['bool'])
            ->setAllowedTypes('enable_tax_breakdown', ['bool'])
            ->setAllowedTypes('enable_product_images', ['bool'])
            ->setAllowedTypes('invoice_prefix', ['array'])
            ->setAllowedTypes('add_current_year', ['bool'])
            ->setAllowedTypes('reset_number_annually', ['bool'])
            ->setAllowedTypes('year_position', ['integer'])
            ->setAllowedValues('year_position', [0, 1])
            ->setAllowedTypes('invoice_number', ['integer'])
            ->setAllowedValues('invoice_number', function (int $value) {
                return $value >= 0;
            })
            ->setAllowedTypes('legal_free_text', ['array'])
            ->setAllowedTypes('footer_text', ['array'])
            ->setAllowedTypes('invoice_model', ['string'])
            ->setAllowedValues('invoice_model', array_keys($this->invoiceModelByNameChoiceProvider->getChoices()))
            ->setAllowedTypes('use_disk_cache', ['bool']);

        return $resolver;
    }
}
