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

namespace PrestaShop\PrestaShop\Adapter\Currency\QueryHandler;

use Currency;
use PrestaShop\PrestaShop\Core\Domain\Currency\DTO\CurrencyForFormEditing;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Query\GetCurrencyForFormEditing;
use PrestaShop\PrestaShop\Core\Domain\Currency\QueryHandler\GetCurrencyForFormEditingHandlerInterface;
use PrestaShopDatabaseException;

/**
 * Class GetCurrencyForFormEditingHandler is responsible for retrieving required data used in currency form.
 *
 * @internal
 */
final class GetCurrencyForFormEditingHandler implements GetCurrencyForFormEditingHandlerInterface
{
    /**
     * @var int
     */
    private $contextShopId;

    /**
     * @param int $contextShopId
     */
    public function __construct($contextShopId)
    {
        $this->contextShopId = $contextShopId;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetCurrencyForFormEditing $query)
    {
        $entity = new Currency(
            $query->getCurrencyId()->getValue(),
            null,
            $this->contextShopId
        );

        if (0 >= $entity->id) {
            throw new CurrencyNotFoundException(
                sprintf(
                    'Currency object with id "%s" was not found for editing',
                    $query->getCurrencyId()->getValue()
                )
            );
        }

        try {
            $result = new CurrencyForFormEditing(
                $entity->iso_code,
                $entity->conversion_rate,
                $entity->active,
                $entity->getAssociatedShops()
            );
        } catch (PrestaShopDatabaseException $exception) {
            throw new CurrencyException(
                sprintf(
                    'An error occurred while trying to get currency with id "%s"',
                    $query->getCurrencyId()->getValue()
                ),
                0,
                $exception
            );
        }

        return $result;
    }
}
