<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Kpi;

use HelperKpi;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Kpi\KpiInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @internal
 */
final class ConversionRateKpi implements KpiInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly ConfigurationInterface $configuration,
        private readonly LegacyContext $contextAdapter
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $helper = new HelperKpi();
        $helper->id = 'box-conversion-rate';
        $helper->icon = 'assessment';
        $helper->color = 'color1';
        $helper->title = $this->translator->trans('Conversion Rate', [], 'Admin.Global');
        $helper->subtitle = $this->translator->trans('30 days', [], 'Admin.Global');

        if ($this->configuration->get('CONVERSION_RATE') !== false) {
            $helper->value = $this->configuration->get('CONVERSION_RATE');
        }

        if ($this->configuration->get('CONVERSION_RATE_CHART') !== false) {
            $helper->data = $this->configuration->get('CONVERSION_RATE_CHART');
        }

        $helper->source = $this->contextAdapter->getAdminLink('AdminStats', true, [
            'ajax' => 1,
            'action' => 'getKpi',
            'kpi' => 'conversion_rate',
        ]);
        $helper->refresh = $this->configuration->get('CONVERSION_RATE_EXPIRE') < time();

        return $helper->generate();
    }
}
