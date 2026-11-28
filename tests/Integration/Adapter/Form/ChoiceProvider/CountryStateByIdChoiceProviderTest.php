<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Form\ChoiceProvider;

use Db;
use PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider\CountryStateByIdChoiceProvider;
use State;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

final class CountryStateByIdChoiceProviderTest extends KernelTestCase
{
    /**
     * United States, the country the demo data gives states to.
     */
    private const COUNTRY_ID = 21;

    private CountryStateByIdChoiceProvider $choiceProvider;

    protected function setUp(): void
    {
        parent::setUp();

        DatabaseDump::restoreTables(['state']);
        self::bootKernel();

        $this->choiceProvider = self::getContainer()->get('prestashop.adapter.form.choice_provider.country_state_by_id');
    }

    protected function tearDown(): void
    {
        DatabaseDump::restoreTables(['state']);

        parent::tearDown();
    }

    public function testADisabledStateIsNotOffered(): void
    {
        $stateId = $this->disableFirstState();

        $this->assertNotContains($stateId, $this->choiceProvider->getChoices(['id_country' => self::COUNTRY_ID]));
    }

    public function testADisabledStateIsKeptWhenItIsTheOneAlreadySelected(): void
    {
        $stateId = $this->disableFirstState();

        $choices = $this->choiceProvider->getChoices([
            'id_country' => self::COUNTRY_ID,
            'kept_state_id' => $stateId,
        ]);

        $this->assertContains($stateId, $choices);
    }

    public function testAStateOfAnotherCountryIsNotKept(): void
    {
        $otherCountryStateId = (int) Db::getInstance()->getValue(
            'SELECT id_state FROM ' . _DB_PREFIX_ . 'state WHERE id_country != ' . self::COUNTRY_ID . ' ORDER BY id_state'
        );
        $this->assertGreaterThan(0, $otherCountryStateId, 'Expected the demo data to give states to another country');

        $choices = $this->choiceProvider->getChoices([
            'id_country' => self::COUNTRY_ID,
            'kept_state_id' => $otherCountryStateId,
        ]);

        $this->assertNotContains($otherCountryStateId, $choices);
        $this->assertCount(count(State::getStatesByIdCountry(self::COUNTRY_ID, true)), $choices);
    }

    public function testTheRawListIsStillAvailable(): void
    {
        $stateId = $this->disableFirstState();

        $choices = $this->choiceProvider->getChoices([
            'id_country' => self::COUNTRY_ID,
            'only_active' => false,
        ]);

        $this->assertContains($stateId, $choices);
    }

    private function disableFirstState(): int
    {
        $states = State::getStatesByIdCountry(self::COUNTRY_ID, true, 'name', 'asc');
        $this->assertNotEmpty($states, 'Expected the demo data to give states to country ' . self::COUNTRY_ID);

        $state = new State((int) $states[0]['id_state']);
        $state->active = false;
        $this->assertTrue($state->update());

        return (int) $state->id;
    }
}
