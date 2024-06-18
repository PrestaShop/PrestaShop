<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use Carrier;
use Exception;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Adapter\Carrier\Repository\CarrierRangeRepository;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Command\SetCarrierRangesCommand;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Query\GetCarrierRanges;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

class CarrierRangesFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @Then /^I set ranges for carrier "(.+)" with specified properties for "(.+)" shops?:$/
     */
    public function setCarrierRanges(string $reference, string $shop, TableNode $node): void
    {
        try {
            $ranges = CarrierRangeRepository::formatRangesFromData($node);

            $command = new SetCarrierRangesCommand(
                $this->referenceToId($reference),
                $ranges,
                $this->getShopConstraint($shop),
            );

            $this->getCommandBus()->handle($command);
        } catch (Exception $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then /^carrier "(.+)" should have the following ranges for "(.+)" shops?:$/
     */
    public function getCarrierRanges(string $reference, string $shop, TableNode $node): void
    {
        try {
            $command = new GetCarrierRanges(
                $this->referenceToId($reference),
                $this->getShopConstraint($shop),
            );

            $rangesDatabase = $this->getCommandBus()->handle($command);
            $rangesExpected = CarrierRangeRepository::formatRangesFromData($node);

            Assert::assertEquals($rangesExpected, $rangesDatabase);
        } catch (Exception $e) {
            $this->setLastException($e);
        }
    }

    private function getShopConstraint(string $shop): ShopConstraint
    {
        if ('all' === $shop) {
            return ShopConstraint::allShops();
        }

        return ShopConstraint::shop((int) $shop);
    }
}
