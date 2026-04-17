<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\DeliveryTimeNoteType;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Provides choices of additional delivery time notes types
 */
final class DeliveryTimeNoteTypesProvider implements FormChoiceProviderInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var int
     */
    private $langId;

    /**
     * @param TranslatorInterface $translator
     * @param RouterInterface $router
     * @param ConfigurationInterface $configuration
     * @param int $langId
     */
    public function __construct(
        TranslatorInterface $translator,
        RouterInterface $router,
        ConfigurationInterface $configuration,
        int $langId
    ) {
        $this->translator = $translator;
        $this->router = $router;
        $this->configuration = $configuration;
        $this->langId = $langId;
    }

    /**
     * {@inheritDoc}
     */
    public function getChoices()
    {
        $linkOpeningTag = sprintf(
            '&nbsp;<a target="_blank" href="%s"><i class="material-icons">open_in_new</i>',
            $this->router->generate('admin_product_preferences') . '#stock_delivery_time'
        );
        $linkClosingTag = '</a>';

        $deliveryTimeLabel = $this->getConfigurationLabel('PS_LABEL_DELIVERY_TIME_AVAILABLE');
        $outOfStockDeliveryTimeLabel = $this->getConfigurationLabel('PS_LABEL_DELIVERY_TIME_OOSBOA');

        return [
            $this->translator->trans('None', [], 'Admin.Catalog.Feature') => DeliveryTimeNoteType::TYPE_NONE,
            $this->translator->trans('Default delivery time: [1]%delivery_time% - %oos_delivery_time%[/1] [2]Edit delivery time[/2]', [
                '%delivery_time%' => $deliveryTimeLabel,
                '%oos_delivery_time%' => $outOfStockDeliveryTimeLabel,
                '[1]' => '&nbsp;<strong>',
                '[/1]' => '</strong>',
                '[2]' => $linkOpeningTag,
                '[/2]' => $linkClosingTag,
            ], 'Admin.Catalog.Feature') => DeliveryTimeNoteType::TYPE_DEFAULT,
            $this->translator->trans('Specific delivery time for this product', [], 'Admin.Catalog.Feature') => DeliveryTimeNoteType::TYPE_SPECIFIC,
        ];
    }

    private function getConfigurationLabel(string $configurationName): string
    {
        $config = $this->configuration->get($configurationName);
        if (!empty($config[$this->langId])) {
            return $config[$this->langId];
        }

        return $this->translator->trans('N/A', [], 'Admin.Global');
    }
}
