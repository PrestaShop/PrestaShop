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

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product;

use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductStatusCommand;
use Tests\Integration\Behaviour\Features\Transform\StringToBoolTransformContext;

class UpdateStatusFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @When /^I (enable|disable) product "(.*)"$/
     *
     * status transformation handled by @see StringToBoolTransformContext
     *
     * @param bool $status
     * @param string $productReference
     */
    public function updateStatus(bool $status, string $productReference): void
    {
        $this->getCommandBus()->handle(new UpdateProductStatusCommand(
            $this->getSharedStorage()->get($productReference),
            $status
        ));
    }

    /**
     * @Then /^product "(.*)" should be (enabled|disabled)$/
     *
     * status transformation handled by @see StringToBoolTransformContext
     *
     * @param string $productReference
     * @param bool $expectedStatus
     */
    public function assertStatus(string $productReference, bool $expectedStatus): void
    {
        $actualStatus = $this->extractValueFromProductForEditing($this->getProductForEditing($productReference), 'active');
        Assert::assertSame($expectedStatus, $actualStatus, 'Unexpected product status');
    }
}
