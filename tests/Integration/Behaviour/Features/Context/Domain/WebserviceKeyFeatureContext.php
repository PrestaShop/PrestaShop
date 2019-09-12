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

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use Exception;
use PrestaShop\PrestaShop\Core\Domain\Webservice\Command\AddWebserviceKeyCommand;
use PrestaShop\PrestaShop\Core\Domain\Webservice\Exception\DuplicateWebserviceKeyException;
use PrestaShop\PrestaShop\Core\Domain\Webservice\ValueObject\WebserviceKeyId;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;
use WebserviceKey;

class WebserviceKeyFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @Given I specify following properties for new webservice key :reference:
     */
    public function specifyPropertiesForWebserviceKey($reference, TableNode $node)
    {
        $data = $node->getRowsHash();

        $data['is_enabled'] = (bool) $data['is_enabled'];
        $data['shop_association'] = [
            SharedStorage::getStorage()->get($data['shop_association'])->id,
        ];

        SharedStorage::getStorage()->set(sprintf('%s_properties', $reference), $data);
    }

    /**
     * @Then /^I specify "(View|Add|Modify|Delete|Fast view)" permission for "([^"]*)" resources for new webservice key "(.*)"$/
     */
    public function specifyResourcePermissions($permission, $resources, $reference)
    {
        $propertiesKey = sprintf('%s_properties', $reference);

        $data = SharedStorage::getStorage()->get($propertiesKey);

        $permissionsMap = [
            'View' => 'GET',
            'Add' => 'POST',
            'Modify' => 'PUT',
            'Delete' => 'DELETE',
            'Fast view' => 'HEAD',
        ];

        $data['permissions'][$permissionsMap[$permission]] = PrimitiveUtils::castStringArrayIntoArray($resources);

        SharedStorage::getStorage()->set($propertiesKey, $data);
    }

    /**
     * @When I add webservice key :reference with specified properties
     */
    public function addWebserviceKeyFromSpecifiedProperties($reference)
    {
        $propertiesKey = sprintf('%s_properties', $reference);

        $data = SharedStorage::getStorage()->get($propertiesKey);

        $command = new AddWebserviceKeyCommand(
            $data['key'],
            $data['description'],
            $data['is_enabled'],
            $data['permissions'],
            $data['shop_association']
        );

        try {
            /** @var WebserviceKeyId $webserviceKeyId */
            $webserviceKeyId = $this->getCommandBus()->handle($command);

            SharedStorage::getStorage()->set($reference, new WebserviceKey($webserviceKeyId->getValue()));
        } catch (Exception $e) {
            $this->lastException = $e;
        }

        SharedStorage::getStorage()->clear($propertiesKey);
    }

    /**
     * @Then I should get error that webservice key is duplicate
     */
    public function assertLastErrorIsDuplicateWebserviceKey()
    {
        $this->assertLastErrorIs(DuplicateWebserviceKeyException::class);
    }
}
