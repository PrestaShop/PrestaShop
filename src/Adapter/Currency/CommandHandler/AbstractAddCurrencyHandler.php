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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Currency\CommandHandler;

use Currency;
use Language;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\AddCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\AddOfficialCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyConstraintException;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleRepository;

/**
 * Class AbstractAddCurrencyHandler
 */
class AbstractAddCurrencyHandler extends AbstractCurrencyHandler
{
    /**
     * @var Language
     */
    protected $defaultLanguage;

    /**
     * @param LocaleRepository $localeRepoCLDR
     * @param int $defaultLanguageId
     */
    public function __construct(LocaleRepository $localeRepoCLDR, $defaultLanguageId)
    {
        parent::__construct($localeRepoCLDR);
        $this->defaultLanguage = new Language((int) $defaultLanguageId);
    }

    /**
     * @param AddCurrencyCommand $command
     *
     * @throws CurrencyConstraintException
     */
    protected function assertCurrencyWithIsoCodeDoesNotExist(AddCurrencyCommand $command)
    {
        $isoCode = $command->getIsoCode()->getValue();
        if (Currency::exists($isoCode)) {
            throw new CurrencyConstraintException(
                sprintf(
                    'Currency with iso code "%s" already exists and cannot be created',
                    $isoCode
                ),
                CurrencyConstraintException::CURRENCY_ALREADY_EXISTS
            );
        }
    }

    /**
     * @param AddOfficialCurrencyCommand $command
     *
     * @throws CurrencyConstraintException
     */
    protected function assertCurrencyWithNumericIsoCodeDoesNotExist(AddOfficialCurrencyCommand $command)
    {
        if (null === $command->getNumericIsoCode()) {
            return;
        }

        $numericIsoCode = $command->getNumericIsoCode()->getValue();
        if (Currency::getIdByNumericIsoCode($numericIsoCode)) {
            throw new CurrencyConstraintException(
                sprintf(
                    'Currency with numeric iso code "%s" already exists and cannot be created',
                    $numericIsoCode
                ),
                CurrencyConstraintException::CURRENCY_ALREADY_EXISTS
            );
        }
    }
}
