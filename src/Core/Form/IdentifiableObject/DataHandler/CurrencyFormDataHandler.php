<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\Cache\Clearer\CacheClearerInterface;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\AddOfficialCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\AddUnofficialCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\EditCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\EditUnofficialCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;

/**
 * Class CurrencyFormDataHandler
 */
final class CurrencyFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @var CacheClearerInterface[]
     */
    private $cacheClearerCollection;

    /**
     * @param CommandBusInterface $commandBus
     * @param CacheClearerInterface[] $cacheClearerCollection
     */
    public function __construct(
        CommandBusInterface $commandBus,
        array $cacheClearerCollection
    ) {
        $this->commandBus = $commandBus;
        $this->cacheClearerCollection = $cacheClearerCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        if ($data['unofficial']) {
            $command = new AddUnofficialCurrencyCommand(
                $data['iso_code'],
                (float) $data['exchange_rate'],
                $data['active']
            );
        } else {
            $command = new AddOfficialCurrencyCommand(
                $data['iso_code'],
                (float) $data['exchange_rate'],
                $data['active']
            );
        }

        $command
            ->setPrecision((int) $data['precision'])
            ->setLocalizedNames($data['names'])
            ->setLocalizedSymbols($data['symbols'])
            ->setLocalizedTransformations($data['transformations'])
            ->setShopIds(is_array($data['shop_association']) ? $data['shop_association'] : [])
        ;

        /** @var CurrencyId $currencyId */
        $currencyId = $this->commandBus->handle($command);
        $this->clearCache();

        return $currencyId->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data)
    {
        if ($data['unofficial']) {
            $command = new EditUnofficialCurrencyCommand((int) $id);
            $command
                ->setIsoCode($data['iso_code'])
            ;
        } else {
            $command = new EditCurrencyCommand((int) $id);
        }

        $command
            ->setLocalizedNames($data['names'])
            ->setLocalizedSymbols($data['symbols'])
            ->setLocalizedTransformations($data['transformations'])
            ->setExchangeRate((float) $data['exchange_rate'])
            ->setPrecision((int) $data['precision'])
            ->setIsEnabled($data['active'])
            ->setShopIds(is_array($data['shop_association']) ? $data['shop_association'] : [])
        ;

        $this->commandBus->handle($command);
        $this->clearCache();
    }

    /**
     * Clear the cache provided
     */
    private function clearCache()
    {
        foreach ($this->cacheClearerCollection as $cacheClearer) {
            $cacheClearer->clear();
        }
    }
}
