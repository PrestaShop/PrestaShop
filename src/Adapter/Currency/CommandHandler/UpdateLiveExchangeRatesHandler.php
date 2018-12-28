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
use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\UpdateLiveExchangeRatesCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\CommandHandler\UpdateLiveExchangeRatesHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\DisabledLiveExchangeRatesException;
use Shop;

/**
 * Class UpdateLiveExchangeRatesHandler
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
     * @param Configuration $configuration
     * @param Tools $tools
     * @param Shop $contextShop
     * @param bool $isCronJobModuleInstalled
     */
    public function __construct(
        Configuration $configuration,
        Tools $tools,
        Shop $contextShop,
        $isCronJobModuleInstalled
    ) {
        $this->configuration = $configuration;
        $this->tools = $tools;
        $this->contextShop = $contextShop;
        $this->isCronJobModuleInstalled = $isCronJobModuleInstalled;
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
        
    }
}
