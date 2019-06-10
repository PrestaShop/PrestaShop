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

use Behat\Gherkin\Node\TableNode;
use Configuration;
use Manufacturer;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\AddManufacturerCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerId;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

class ManufacturerFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @var string default language id from database configs
     */
    private $defaultLangId;

    /**
     * @var string default shop id from database configs
     */
    private $defaultShopId;

    public function __construct()
    {
        $this->defaultLangId = Configuration::get('PS_LANG_DEFAULT');
        $this->defaultShopId = Configuration::get('PS_SHOP_DEFAULT');
    }

    /**
     * @When I add new manufacturer :reference with following properties:
     */
    public function addManufacturerWithDefaultLangForDefaultShop($reference, TableNode $node)
    {
        $data = $node->getRowsHash();

        $command = new AddManufacturerCommand(
            $data['name'],
            $data['enabled'],
            [$this->defaultLangId => $data['short_description']],
            [$this->defaultLangId => $data['description']],
            [$this->defaultLangId => $data['meta_title']],
            [$this->defaultLangId => $data['meta_description']],
            [$this->defaultLangId => $data['meta_keywords']],
            [$this->defaultShopId]
        );

        /**
         * @var ManufacturerId
         */
        $manufacturerId = $this->getCommandBus()->handle($command);

        SharedStorage::getStorage()->set($reference, new Manufacturer($manufacturerId->getValue()));
    }

    /**
     * @Then manufacturer :reference name should be :name
     */
    public function assertManufacturerName($reference, $name)
    {
        $manufacturer = SharedStorage::getStorage()->get($reference);

        if ($manufacturer->name !== $name) {
            throw new RuntimeException(sprintf(
                'Manufacturer "%s" has "%s" name, but "%s" was expected.',
                $reference,
                $manufacturer->name,
                $name
            ));
        }
    }

    /**
     * @Then manufacturer :reference :field in default language should be :value
     */
    public function assertFieldValue($reference, $field, $value)
    {
        /** @var Manufacturer $manufacturer */
        $manufacturer = SharedStorage::getStorage()->get($reference);

        if ($manufacturer->$field[$this->defaultLangId] !== $value) {
            throw new RuntimeException(sprintf(
                'Manufacturer "%s" has "%s" %s, but "%s" was expected.',
                $reference,
                $manufacturer->$field[$this->defaultLangId],
                $field,
                $value
            ));
        }
    }

    /**
     * @Then manufacturer :reference :field field in default language should be empty
     */
    public function assertFieldIsEmpty($reference, $field)
    {
        $manufacturer = SharedStorage::getStorage()->get($reference);

        if ($manufacturer->$field[$this->defaultLangId] !== '') {
            throw new RuntimeException(sprintf(
                'Manufacturer "%s" has "%s" %s, but it was expected to be empty',
                $reference,
                $manufacturer->$field[$this->defaultLangId],
                $field
            ));
        }
    }

    /**
     * @Then /^manufacturer "(.*)" should be (enabled|disabled)?$/
     */
    public function assertStatus($reference, $expectedStatus)
    {
        /** @var Manufacturer $manufacturer */
        $manufacturer = SharedStorage::getStorage()->get($reference);
        $expectedStatus === 'enabled' ? $expectedStatusBool = true : $expectedStatusBool = false;
        $actualStatusBool = (bool) $manufacturer->active;

        if ($actualStatusBool !== $expectedStatusBool) {
            throw new RuntimeException(sprintf(
                'Manufacturer "%s" is %s, but it was expected to be %s',
                $reference,
                $actualStatusBool ? 'enabled' : 'disabled',
                $expectedStatus
            ));
        }
    }
}
