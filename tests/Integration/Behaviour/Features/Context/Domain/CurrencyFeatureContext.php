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

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;
use Currency;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\AddCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\ShopFeatureContext;

class CurrencyFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @var ShopFeatureContext
     */
    private $shopFeatureContext;

    /**
     * @var Currency[]
     */
    private $currencyRegistry = [];

    /** @BeforeScenario */
    public function before(BeforeScenarioScope $scope)
    {
        $this->shopFeatureContext = $scope->getEnvironment()->getContext(ShopFeatureContext::class);
    }

    /**
     * @When I add new currency :reference with following properties:
     */
    public function addNewCurrency($reference, TableNode $node)
    {
        $data = $node->getRowsHash();
        $shop = $this->shopFeatureContext->getShopFromRegistry($data['shop_association']);

        $command = new AddCurrencyCommand(
            $data['iso_code'],
            (float) $data['exchange_rate'],
            (bool) $data['is_enabled']
        );

        $command->setShopIds([
            (int) $shop->id,
        ]);

        /** @var CurrencyId $currencyId */
        $currencyId = $this->getCommandBus()->handle($command);

        $this->currencyRegistry[$reference]= new Currency($currencyId->getValue());
    }

    /**
     * @param string $reference
     *
     * @return Currency
     */
    public function getCurrencyFromRegistry($reference)
    {
        if (!isset($this->currencyRegistry[$reference])) {
            throw new RuntimeException(sprintf('Currency "%s" does not exist in registry', $reference));
        }

        return $this->currencyRegistry[$reference];
    }
}
