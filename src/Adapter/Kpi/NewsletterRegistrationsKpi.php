<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Kpi;

use HelperKpi;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Kpi\KpiInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Renders number of how many customers have registered for newsletter.
 */
final class NewsletterRegistrationsKpi implements KpiInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var ConfigurationInterface
     */
    private $kpiConfiguration;

    /**
     * @var string
     */
    private $sourceUrl;

    /**
     * @param TranslatorInterface $translator
     * @param ConfigurationInterface $kpiConfiguration
     * @param string $sourceUrl
     */
    public function __construct(
        TranslatorInterface $translator,
        ConfigurationInterface $kpiConfiguration,
        $sourceUrl
    ) {
        $this->translator = $translator;
        $this->kpiConfiguration = $kpiConfiguration;
        $this->sourceUrl = $sourceUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $helper = new HelperKpi();
        $helper->id = 'box-newsletter';
        $helper->icon = 'mail_outline';
        $helper->color = 'color3';

        $helper->title = $this->translator->trans('Newsletter Registrations', [], 'Admin.Orderscustomers.Feature');
        $helper->subtitle = $this->translator->trans('All time', [], 'Admin.Global');

        if (false !== $this->kpiConfiguration->get('NEWSLETTER_REGISTRATIONS')) {
            $helper->value = $this->kpiConfiguration->get('NEWSLETTER_REGISTRATIONS');
        }

        $helper->source = $this->sourceUrl;
        $helper->refresh = $this->kpiConfiguration->get('NEWSLETTER_REGISTRATIONS_EXPIRE') < time();

        return $helper->generate();
    }
}
