<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Behaviour\Features\Context;

use Behat\Gherkin\Node\TableNode;
use Country;
use Store;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class StoreFeatureContext extends AbstractPrestaShopFeatureContext
{
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
        $store->name = [$this->getDefaultLangId() => (string) $data['name']];
        $store->active = PrimitiveUtils::castStringBooleanIntoBoolean($data['enabled']);
        $store->address1 = [$this->getDefaultLangId() => (string) $data['address1']];
        $store->city = $data['city'];
        $store->latitude = (float) $data['latitude'];
        $store->longitude = (float) $data['longitude'];
        $store->id_country = (int) Country::getIdByName($this->getDefaultLangId(), $data['country']);
        $store->add();

        SharedStorage::getStorage()->set($storeReference, new Store($store->id));
    }
}
