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

use CronJobs;
use Db;
use Exception;
use Module;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Entity\DbQuery;
use PrestaShop\PrestaShop\Adapter\Shop\ShopUrlDataProvider;
use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\UpdateLiveExchangeRatesCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\CommandHandler\UpdateLiveExchangeRatesHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotCreateLiveExchangeUpdateCronTask;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\DisabledLiveExchangeRatesException;
use PrestaShopException;
use Shop;
use Symfony\Component\Translation\TranslatorInterface;

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
     * @var string
     */
    private $dbPrefix;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param Configuration $configuration
     * @param Tools $tools
     * @param Shop $contextShop
     * @param ShopUrlDataProvider $shopUrlDataProvider
     * @param TranslatorInterface $translator
     * @param bool $isCronJobModuleInstalled
     * @param string $adminBaseUrl
     * @param string $dbPrefix
     */
    public function __construct(
        Configuration $configuration,
        Tools $tools,
        Shop $contextShop,
        ShopUrlDataProvider $shopUrlDataProvider,
        TranslatorInterface $translator,
        $isCronJobModuleInstalled,
        $adminBaseUrl,
        $dbPrefix
    ) {
        $this->configuration = $configuration;
        $this->tools = $tools;
        $this->contextShop = $contextShop;
        $this->isCronJobModuleInstalled = $isCronJobModuleInstalled;
        $this->shopUrlDataProvider = $shopUrlDataProvider;
        $this->adminBaseUrl = $adminBaseUrl;
        $this->dbPrefix = $dbPrefix;
        $this->translator = $translator;
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

        $cronId = $this->configuration->get('PS_ACTIVE_CRONJOB_EXCHANGE_RATE');

        try {
            $cronUrl = $this->getCronUrl();

            if ($cronId && !$command->isExchangeRateEnabled()) {
                $this->removeCronJob($cronId);

                return;
            }

            if (!$cronId && $command->isExchangeRateEnabled() && false === $this->createCronJob($cronUrl)) {
                throw new CannotCreateLiveExchangeUpdateCronTask(
                    sprintf(
                        'Failed to create a cron task for live exchange rate update with given link "%s"',
                        $cronUrl
                    )
                );
            }

            if ($cronId) {
                $this->validateCronJob($cronId);
            }
        } catch (Exception $exception) {
            throw new CurrencyException(
                'An unexpected error occurred when trying to update live exchange rates',
                0,
                $exception
            );
        }
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

    /**
     * @param int $cronId
     *
     * @throws Exception
     */
    private function removeCronJob($cronId)
    {
        $this->configuration->set('PS_ACTIVE_CRONJOB_EXCHANGE_RATE', 0);

        Db::getInstance()->execute(
            'DELETE FROM ' . $this->dbPrefix . 'cronjobs WHERE `id_cronjob`=' . (int) $cronId
        );
    }

    /**
     * @param string $cronUrl
     *
     * @return bool
     *
     * @throws Exception
     */
    private function createCronJob($cronUrl)
    {
        /** @var CronJobs $cronJobsModule */
        $cronJobsModule = Module::getInstanceByName('cronjobs');

        $isCronAdded = $cronJobsModule->addOneShotTask(
            $cronUrl,
            $this->translator->trans(
                'Live exchange Rate for %shop_name%',
                [
                    '%shop_name%' => $this->configuration->get('PS_SHOP_NAME'),
                ],
                'Admin.International.Feature'
            )
        );

        $this->configuration->set('PS_ACTIVE_CRONJOB_EXCHANGE_RATE', Db::getInstance()->Insert_ID());

        return $isCronAdded;
    }

    /**
     * @param int $cronId
     *
     * @throws Exception
     */
    private function validateCronJob($cronId)
    {
        $query = new DbQuery();
        $query
            ->select('*')
            ->from('cronjobs')
            ->where('`id_cronjob`=' . (int) $cronId)
        ;

        /** @var array $row */
        $row = Db::getInstance()->getRow($query);

        if (!is_array($row) || empty($row['active'])) {
            $this->configuration->set('PS_ACTIVE_CRONJOB_EXCHANGE_RATE', 0);
        }
    }
}
