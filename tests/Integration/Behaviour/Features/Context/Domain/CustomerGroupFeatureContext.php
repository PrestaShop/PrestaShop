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
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Query\GetCustomerGroupForEditing;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\QueryResult\EditableCustomerGroup;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupId;

class CustomerGroupFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When /^I create a Customer Group "(.+)" with the following details:$/
     *
     * @param string $customerGroupReference
     * @param TableNode $tableNode
     *
     * @throws Exception
     */
    public function createCustomerUsingCommand(string $customerGroupReference, TableNode $tableNode)
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
     * @When /^I query Customer Group "(.+)" I should get a Customer Group with properties:$/
     */
    public function assertQueryCustomerProperties($customerGroupReference, EditableCustomerGroup $expectedGroup)
    {
        Assert::assertEquals($this->getCustomerGroupForEditing($customerGroupReference), $expectedGroup);
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
            (int) $data['id'],
            $data['name'],
            new DecimalNumber($data['reduction']),
            (bool) $data['displayPriceTaxExcluded'],
            (bool) $data['showPrice'],
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
