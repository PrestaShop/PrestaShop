<?php
/*
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
use Exception;
use PHPUnit\Framework\Assert;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\AddCustomerGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\EditCustomerGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Query\GetCustomerGroupForEditing;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\QueryResult\EditableCustomerGroup;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupId;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class CustomerGroupFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I create a customer group :customerGroupReference with the following details:
     *
     * @param string $customerGroupReference
     * @param TableNode $tableNode
     *
     * @throws Exception
     */
    public function createCustomerGroupUsingCommand(string $customerGroupReference, TableNode $tableNode)
    {
        $data = $this->localizeByRows($tableNode);

        $command = new AddCustomerGroupCommand(
            $data['name'],
            new DecimalNumber($data['reduction']),
            (bool) $data['displayPriceTaxExcluded'],
            (bool) $data['showPrice'],
            $this->referencesToIds($data['shopIds'])
        );

        /** @var GroupId $id */
        $id = $this->getCommandBus()->handle($command);
        $this->getSharedStorage()->set($customerGroupReference, $id->getValue());
    }

    /**
     * @When I update customer group :customerGroupReference with the following details:
     *
     * @param string $customerGroupReference
     * @param TableNode $tableNode
     *
     * @throws Exception
     */
    public function updateCustomerGroupUsingCommand(string $customerGroupReference, TableNode $tableNode)
    {
        $data = $this->localizeByRows($tableNode);

        $command = new EditCustomerGroupCommand($this->referenceToId($customerGroupReference));
        if (!empty($data['name'])) {
            $command->setLocalizedNames($data['name']);
        }
        if (!empty($data['reduction'])) {
            $command->setReductionPercent(new DecimalNumber($data['reduction']));
        }
        if (!empty($data['displayPriceTaxExcluded'])) {
            $command->setDisplayPriceTaxExcluded(PrimitiveUtils::castStringBooleanIntoBoolean($data['displayPriceTaxExcluded']));
        }
        if (!empty($data['showPrice'])) {
            $command->setShowPrice(PrimitiveUtils::castStringBooleanIntoBoolean($data['showPrice']));
        }
        if (!empty($data['shopIds'])) {
            $command->setShopIds($this->referencesToIds($data['shopIds']));
        }

        $this->getCommandBus()->handle($command);
    }

    /**
     * @Then customer group :customerGroupReference have the following values:
     */
    public function assertQueryCustomerGroupProperties($customerGroupReference, EditableCustomerGroup $expectedGroup)
    {
        Assert::assertEquals($expectedGroup, $this->getCustomerGroupForEditing($customerGroupReference));
    }

    /**
     * @Transform table:customer group,value
     *
     * @param TableNode $tableNode
     *
     * @return EditableCustomerGroup
     */
    public function transformEditableCustomerGroup(TableNode $tableNode): EditableCustomerGroup
    {
        $data = $this->localizeByRows($tableNode);

        return new EditableCustomerGroup(
            $this->referenceToId($data['reference_id']),
            $data['name'],
            new DecimalNumber($data['reduction']),
            PrimitiveUtils::castStringBooleanIntoBoolean($data['displayPriceTaxExcluded']),
                PrimitiveUtils::castStringBooleanIntoBoolean($data['showPrice']),
            $this->referencesToIds($data['shopIds'])
        );
    }

    /**
     * @param string $customerGroupReference
     *
     * @return EditableCustomerGroup
     */
    private function getCustomerGroupForEditing(string $customerGroupReference): EditableCustomerGroup
    {
        return $this->getQueryBus()->handle(new GetCustomerGroupForEditing($this->getSharedStorage()->get($customerGroupReference)));
    }
}
