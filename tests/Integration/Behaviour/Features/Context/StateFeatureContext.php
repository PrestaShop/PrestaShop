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
use State;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;
use Zone;

class StateFeatureContext extends AbstractPrestaShopFeatureContext
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
     * @When I define an uncreated state :stateReference
     *
     * @param string $stateReference
     */
    public function defineUnCreatedState(string $stateReference): void
    {
        SharedStorage::getStorage()->set($stateReference, new State(0));
    }

    /**
     * @When I add new state :stateReference with following properties:
     *
     * @param string $stateReference
     * @param TableNode $table
     */
    public function createState(string $stateReference, TableNode $table): void
    {
        $data = $table->getRowsHash();

        $state = new State();
        $state->name = $data['name'];
        $state->active = PrimitiveUtils::castStringBooleanIntoBoolean($data['enabled']);
        $state->id_country = (int) Country::getIdByName($this->defaultLangId, $data['country']);
        $state->id_zone = Zone::getIdByName($data['zone']);
        $state->iso_code = $data['iso_code'];
        $state->add();

        SharedStorage::getStorage()->set($stateReference, new State($state->id));
    }

    /**
     * @When I edit state :stateReference with following properties:
     *
     * @param string $stateReference
     * @param TableNode $table
     */
    public function editState(string $stateReference, TableNode $table): void
    {
        /** @var State $state */
        $state = SharedStorage::getStorage()->get($stateReference);

        $data = $table->getRowsHash();
        if (isset($data['name'])) {
            $state->name = $data['name'];
        }
        if (isset($data['iso_code'])) {
            $state->iso_code = $data['iso_code'];
        }
        if (isset($data['enabled'])) {
            $state->active = PrimitiveUtils::castStringBooleanIntoBoolean($data['enabled']);
        }
        if (isset($data['country'])) {
            $state->id_country = Country::getIdByName($this->defaultLangId, $data['country']);
        }
        if (isset($data['zone'])) {
            $state->id_zone = Zone::getIdByName($data['zone']);
        }
        $state->save();

        SharedStorage::getStorage()->set($stateReference, new State($state->id));
    }
}
