<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
            $this->translator->trans('Specific delivery time to this product', [], 'Admin.Catalog.Feature') => DeliveryTimeNoteType::TYPE_SPECIFIC,
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
