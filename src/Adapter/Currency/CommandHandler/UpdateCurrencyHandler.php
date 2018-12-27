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

use Currency;
use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelLegacyHandler;
use PrestaShop\PrestaShop\Adapter\Entity\Db;
use PrestaShop\PrestaShop\Adapter\Entity\DbQuery;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\UpdateCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\CommandHandler\UpdateCurrencyHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotUpdateCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShopException;

/**
 * Class UpdateCurrencyHandler
 *
 * @internal
 */
final class UpdateCurrencyHandler extends AbstractObjectModelLegacyHandler implements UpdateCurrencyHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CurrencyException
     */
    public function handle(UpdateCurrencyCommand $command)
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

            $this->assertNewIsoCodeAlreadyExists(
                $command->getCurrencyId()->getValue(),
                $entity->iso_code,
                $command->getIsoCode()->getValue()
            );

            $entity->iso_code = $command->getIsoCode()->getValue();
            $entity->active = $command->isEnabled();
            $entity->conversion_rate = $command->getExchangeRate()->getValue();

            if (false === $entity->update()) {
                throw new CannotUpdateCurrencyException(
                    sprintf(
                        'An error occurred when updating currency object with id "%s"',
                        $command->getCurrencyId()->getValue()
                    )
                );
            }

            $this->associateWithShops($entity, $command->getShopIds());

            $columnsToUpdate = [];
            foreach ($command->getShopIds() as $shopId) {
                $columnsToUpdate[$shopId] = [
                    'conversion_rate' => $entity->conversion_rate,
                ];
            }
            $this->updateMultiStoreColumns($entity, $columnsToUpdate);

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

        return new CurrencyId((int) $entity->id);
    }

    /**
     * @param int $currencyId
     * @param string $currentIsoCode
     * @param string $newIsoCode
     *
     * @throws CurrencyConstraintException
     */
    private function assertNewIsoCodeAlreadyExists($currencyId, $currentIsoCode, $newIsoCode)
    {
        if ($currentIsoCode === $newIsoCode) {
            return;
        }

        $qb = new DbQuery();
        $qb
            ->select('id_currency')
            ->from('currency')
            ->where('id_currency !=' . $currencyId)
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
}
