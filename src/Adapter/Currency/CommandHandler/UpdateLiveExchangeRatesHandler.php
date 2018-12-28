<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Currency\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Shop\ShopUrlDataProvider;
use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\UpdateLiveExchangeRatesCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\CommandHandler\UpdateLiveExchangeRatesHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\DisabledLiveExchangeRatesException;
use PrestaShopException;
use Shop;

/**
 * Class UpdateLiveExchangeRatesHandler
 *
 * @internal
 */
final class UpdateLiveExchangeRatesHandler implements UpdateLiveExchangeRatesHandlerInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var Tools
     */
    private $tools;

    /**
     * @var Shop
     */
    private $contextShop;

    /**
     * @var bool
     */
    private $isCronJobModuleInstalled;

    /**
     * @var ShopUrlDataProvider
     */
    private $shopUrlDataProvider;

    /**
     * @var string
     */
    private $adminBaseUrl;

    /**
     * @param Configuration $configuration
     * @param Tools $tools
     * @param Shop $contextShop
     * @param ShopUrlDataProvider $shopUrlDataProvider
     * @param bool $isCronJobModuleInstalled
     * @param string $adminBaseUrl
     */
    public function __construct(
        Configuration $configuration,
        Tools $tools,
        Shop $contextShop,
        ShopUrlDataProvider $shopUrlDataProvider,
        $isCronJobModuleInstalled,
        $adminBaseUrl
    ) {
        $this->configuration = $configuration;
        $this->tools = $tools;
        $this->contextShop = $contextShop;
        $this->isCronJobModuleInstalled = $isCronJobModuleInstalled;
        $this->shopUrlDataProvider = $shopUrlDataProvider;
        $this->adminBaseUrl = $adminBaseUrl;
    }

    /**
     * {@inheritdoc}
     *
     * @throws CurrencyException
     */
    public function handle(UpdateLiveExchangeRatesCommand $command)
    {
        if (!$this->isCronJobModuleInstalled) {
            throw new DisabledLiveExchangeRatesException(
                'Live exchange rates feature cannot be modified due to cronjob module is uninstalled'
            );
        }

        $this->configuration->restrictUpdatesTo($this->contextShop);

        $exchangeRateValue = $this->configuration->get('PS_ACTIVE_CRONJOB_EXCHANGE_RATE');

        $cronUrl = $this->getCronUrl();
    }

    /**
     * @return string
     *
     * @throws PrestaShopException
     */
    private function getCronUrl()
    {
        $protocol = $this->tools->getShopProtocol();
        $shopDomain = $this->shopUrlDataProvider->getMainShopUrl()->domain;
        $cronFileLink = sprintf(
            'cron_currency_rates.php?secure_key=%s',
            md5($this->configuration->get('_COOKIE_KEY_') . $this->configuration->get('PS_SHOP_NAME'))
        );

        return $protocol . $shopDomain . $this->adminBaseUrl . $cronFileLink;
    }
}
