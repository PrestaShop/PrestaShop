<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
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
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\ToggleExchangeRateAutomatizationCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\CommandHandler\ToggleExchangeRateAutomatizationHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\AutomateExchangeRatesUpdateException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShopException;
use Shop;
use Symfony\Component\Translation\TranslatorInterface;
use Tools;

/**
 * Class ToggleExchangeRateAutomatizationHandler is responsible for turning on or off the setting - if its on then
 * in CronJobs module it creates new record with url which points to the script which is being executed at certain time
 * of period. If the setting is off then it removes that record.
 *
 * @todo: an issue with multi-store cron task scheduler.
 *
 * @internal
 */
final class ToggleExchangeRateAutomatizationHandler implements ToggleExchangeRateAutomatizationHandlerInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

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
     * @param Shop $contextShop
     * @param ShopUrlDataProvider $shopUrlDataProvider
     * @param TranslatorInterface $translator
     * @param bool $isCronJobModuleInstalled
     * @param string $adminBaseUrl
     * @param string $dbPrefix
     */
    public function __construct(
        Configuration $configuration,
        Shop $contextShop,
        ShopUrlDataProvider $shopUrlDataProvider,
        TranslatorInterface $translator,
        $isCronJobModuleInstalled,
        $adminBaseUrl,
        $dbPrefix
    ) {
        $this->configuration = $configuration;
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
    public function handle(ToggleExchangeRateAutomatizationCommand $command)
    {
        if (!$this->isCronJobModuleInstalled) {
            throw new AutomateExchangeRatesUpdateException(
                'Live exchange rates feature cannot be modified because "cronjob" module is not installed',
                AutomateExchangeRatesUpdateException::CRON_TASK_MANAGER_MODULE_NOT_INSTALLED
            );
        }

        $this->configuration->restrictUpdatesTo($this->contextShop);

        $cronId = (int) $this->configuration->get('PS_ACTIVE_CRONJOB_EXCHANGE_RATE');
        $thereIsOneCronRunning = ($cronId !== 0);

        try {
            if ($thereIsOneCronRunning && $command->exchangeRateStatus()) {
                $this->removeConfigurationIfNotFoundOrIsDeactivated($cronId);

                return;
            }

            if (!$thereIsOneCronRunning && $command->exchangeRateStatus()) {
                $this->enableExchangeRatesScheduler();

                return;
            }

            if ($thereIsOneCronRunning && !$command->exchangeRateStatus()) {
                $this->disableExchangeRatesScheduler($cronId);

                return;
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
        $protocol = Tools::getShopProtocol();
        $shopDomain = $this->shopUrlDataProvider->getMainShopUrl()->domain;
        $cronFileLink = sprintf(
            'cron_currency_rates.php?secure_key=%s',
            md5($this->configuration->get('_COOKIE_KEY_') . $this->configuration->get('PS_SHOP_NAME'))
        );

        return $protocol . $shopDomain . $this->adminBaseUrl . $cronFileLink;
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
     * It checks if the given cronjob exists or is active. If it does not exist or it is not active when the configura
     * tion value is being reset.
     *
     * @param int $cronId
     *
     * @throws Exception
     */
    private function removeConfigurationIfNotFoundOrIsDeactivated($cronId)
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

    /**
     * Creates new cronjob for exchange rates auto update.
     *
     * @throws PrestaShopException
     * @throws AutomateExchangeRatesUpdateException
     * @throws Exception
     */
    private function enableExchangeRatesScheduler()
    {
        $cronUrl = $this->getCronUrl();

        if (false === $this->createCronJob($cronUrl)) {
            throw new AutomateExchangeRatesUpdateException(
                'Failed to create a cron task for live exchange rate update',
                AutomateExchangeRatesUpdateException::CRON_TASK_CREATION_FAILED
            );
        }
    }

    /**
     * Removes given cronjob from  configuration and also from cronjobs table.
     *
     * @param int $cronId
     *
     * @throws Exception
     */
    private function disableExchangeRatesScheduler($cronId)
    {
        $this->configuration->set('PS_ACTIVE_CRONJOB_EXCHANGE_RATE', 0);

        Db::getInstance()->execute(
            'DELETE FROM ' . $this->dbPrefix . 'cronjobs WHERE `id_cronjob`=' . (int) $cronId
        );
    }
}
