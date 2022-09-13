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

namespace Tests\Integration\Behaviour\Features\Context;

use Behat\Gherkin\Node\TableNode;
use Country;
use Store;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class StoreFeatureContext extends AbstractPrestaShopFeatureContext
{
    /**
     * @var int default lang id from configs
     */
    private $defaultLangId;

    public function __construct()
    {
        $configuration = CommonFeatureContext::getContainer()->get('prestashop.adapter.legacy.configuration');
        $this->defaultLangId = (int) $configuration->get('PS_LANG_DEFAULT');
    }

    /**
     * @When I add new store :storeReference with following properties:
     *
     * @param string $storeReference
     * @param TableNode $table
     */
    public function createStore(string $storeReference, TableNode $table): void
    {
        $data = $table->getRowsHash();

        $store = new Store();
        $store->name = [$this->defaultLangId => (string) $data['name']];
        $store->active = PrimitiveUtils::castStringBooleanIntoBoolean($data['enabled']);
        $store->address1 = [$this->defaultLangId => (string) $data['address1']];
        $store->city = $data['city'];
        $store->latitude = (float) $data['latitude'];
        $store->longitude = (float) $data['longitude'];
        $store->id_country = (int) Country::getIdByName($this->defaultLangId, $data['country']);
        $store->add();

        SharedStorage::getStorage()->set($storeReference, new Store($store->id));
    }
}
