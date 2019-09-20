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

use Configuration;
use Currency;
use Language;
use PrestaShop\PrestaShop\Adapter\Entity\Db;
use PrestaShop\PrestaShop\Adapter\Entity\DbQuery;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\EditCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\CommandHandler\EditCurrencyHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotDisableDefaultCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\DefaultCurrencyInMultiShopException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotUpdateCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyNotFoundException;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleRepository;
use PrestaShopException;
use Shop;

/**
 * Class EditCurrencyHandler is responsible for updating currencies.
 *
 * @internal
 */
final class EditCurrencyHandler extends AbstractCurrencyHandler implements EditCurrencyHandlerInterface
{
    /**
     * @var int
     */
    private $defaultCurrencyId;

    /**
     * @var LocaleRepository
     */
    private $localeRepository;

    /**
     * @var string
     */
    private $contextLocale;

    /**
     * @param int $defaultCurrencyId
     * @param LocaleRepository $localeRepository
     * @param string $contextLocale
     */
    public function __construct($defaultCurrencyId, LocaleRepository $localeRepository, $contextLocale)
    {
        $this->defaultCurrencyId = (int) $defaultCurrencyId;
        $this->localeRepository = $localeRepository;
        $this->contextLocale = $contextLocale;
    }

    /**
     * {@inheritdoc}
     *
     * @throws CurrencyException
     */
    public function handle(EditCurrencyCommand $command)
    {
        try {
            $entity = new Currency($command->getCurrencyId()->getValue());

            if (0 >= $entity->id) {
                throw new CurrencyNotFoundException(
                    sprintf(
                        'Currency object with id "%s" was not found for currency update',
                        $command->getCurrencyId()->getValue()
                    )
                );
            }

            if (null !== $command->getIsoCode()) {
                $this->updateNameAndSymbol(
                    $entity,
                    $command->getIsoCode()->getValue()
                );

                $entity->iso_code = $command->getIsoCode()->getValue();
            }

            $entity->active = $command->isEnabled();
            $entity->conversion_rate = $command->getExchangeRate()->getValue();

            $this->assertCurrencyWithIsoCodeDoesNotExist(
                $command->getCurrencyId()->getValue(),
                $entity->iso_code,
                $command->getIsoCode()->getValue()
            );
            $this->assertDefaultCurrencyIsNotBeingDisabled($command);
            $this->assertDefaultCurrencyIsNotBeingRemovedOrDisabledFromShop(
                $entity,
                $command->getShopIds()
            );

            if (false === $entity->update()) {
                throw new CannotUpdateCurrencyException(
                    sprintf(
                        'An error occurred when updating currency object with id "%s"',
                        $command->getCurrencyId()->getValue()
                    )
                );
            }

            $this->associateWithShops($entity, $command->getShopIds());
            $this->associateConversionRateToShops($entity, $command->getShopIds());
        } catch (PrestaShopException $exception) {
            throw new CurrencyException(
                sprintf(
                    'An error occurred when updating currency object with id "%s"',
                    $command->getCurrencyId()->getValue()
                ),
                0,
                $exception
            );
        }
    }

    /**
     * When editing the currency it checks if new iso code does not exist
     * so it will not create multiple currencies with same iso codes,
     *
     * @param int $currencyId
     * @param string $currentIsoCode
     * @param string $newIsoCode
     *
     * @throws CurrencyConstraintException
     */
    private function assertCurrencyWithIsoCodeDoesNotExist($currencyId, $currentIsoCode, $newIsoCode)
    {
        if ($currentIsoCode === $newIsoCode) {
            return;
        }

        $qb = new DbQuery();
        $qb
            ->select('id_currency')
            ->from('currency')
            ->where('id_currency !=' . (int) $currencyId)
            ->where('iso_code = "' . pSQL($newIsoCode) . '"')
        ;

        $result = Db::getInstance()->getValue($qb);

        if (is_numeric($result)) {
            throw new CurrencyConstraintException(
                sprintf(
                    'Currency with iso code "%s" already exist and cannot be created',
                    $newIsoCode
                ),
                CurrencyConstraintException::CURRENCY_ALREADY_EXISTS
            );
        }
    }

    /**
     * Prevents from default currency being disabled.
     *
     * @param EditCurrencyCommand $command
     *
     * @throws CannotDisableDefaultCurrencyException
     */
    private function assertDefaultCurrencyIsNotBeingDisabled(EditCurrencyCommand $command)
    {
        if (!$command->isEnabled() && $command->getCurrencyId()->getValue() === $this->defaultCurrencyId) {
            throw new CannotDisableDefaultCurrencyException(
                sprintf(
                    'Currency with id "%s" is the default currency and cannot be disabled.',
                    $command->getCurrencyId()->getValue()
                )
            );
        }
    }

    /**
     * On each shop there might be different default currency. This function prevents from removing shop association
     * from each shop and checks that the shop is not being disabled as well.
     *
     * @param Currency $currency
     * @param array $shopIds
     *
     * @throws DefaultCurrencyInMultiShopException
     */
    private function assertDefaultCurrencyIsNotBeingRemovedOrDisabledFromShop(Currency $currency, array $shopIds)
    {
        $allShopIds = Shop::getShops(false, null, true);

        foreach ($allShopIds as $shopId) {
            $shopDefaultCurrencyId = (int) Configuration::get(
                'PS_CURRENCY_DEFAULT',
                null,
                null,
                $shopId
            );

            if ((int) $currency->id !== $shopDefaultCurrencyId) {
                continue;
            }

            if (!in_array($shopId, $shopIds)) {
                $shop = new Shop($shopId);
                throw new DefaultCurrencyInMultiShopException(
                    $currency->getName(),
                    $shop->name,
                    sprintf(
                        'Currency with id %s cannot be unassigned from shop with id %s because its the default currency.',
                        $currency->id,
                        $shopId
                    ),
                    DefaultCurrencyInMultiShopException::CANNOT_REMOVE_CURRENCY
                );
            }

            if (!$currency->active) {
                $shop = new Shop($shopId);
                throw new DefaultCurrencyInMultiShopException(
                    $currency->getName(),
                    $shop->name,
                    sprintf(
                        'Currency with id %s cannot be disabled from shop with id %s because its the default currency.',
                        $currency->id,
                        $shopId
                    ),
                    DefaultCurrencyInMultiShopException::CANNOT_DISABLE_CURRENCY
                );
            }
        }
    }

    /**
     * @param Currency $entity
     * @param string $newIsoCode
     */
    private function updateNameAndSymbol(Currency $entity, $newIsoCode)
    {
        $locale = $this->localeRepository->getLocale($this->contextLocale);

        if (null !== $locale) {
            $currency = $locale->getCurrency($newIsoCode);

            if (null !== $currency) {
                $langIds = Language::getLanguages(true, false, true);
                $entity->name = [];
                $entity->symbol = [];
                $entity->numeric_iso_code = $currency->getNumericIsoCode();

                foreach ($langIds as $langId) {
                    $entity->name[$langId] = $currency->getDisplayName();
                    $entity->symbol[$langId] = $currency->getSymbol();
                }
            }
        }
    }
}
