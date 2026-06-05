<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use Country;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Adapter\Store\ContactDetailsConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Store\Command\AddStoreCommand;
use PrestaShop\PrestaShop\Core\Domain\Store\Command\BulkDeleteStoreCommand;
use PrestaShop\PrestaShop\Core\Domain\Store\Command\BulkUpdateStoreStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Store\Command\DeleteStoreCommand;
use PrestaShop\PrestaShop\Core\Domain\Store\Command\EditStoreCommand;
use PrestaShop\PrestaShop\Core\Domain\Store\Command\ToggleStoreStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Store\Exception\StoreConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Store\Exception\StoreNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Store\Query\GetStoreForEditing;
use PrestaShop\PrestaShop\Core\Domain\Store\QueryResult\StoreForEditing;
use PrestaShop\PrestaShop\Core\Domain\Store\ValueObject\StoreId;
use RuntimeException;
use State;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class StoreFeatureContext extends AbstractDomainFeatureContext
{
    private const DAY_ORDER = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

    // ──────────────────────────────────────────────────────────────────────────
    // Toggle
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * @When I toggle :reference
     */
    public function toggleStore(string $reference): void
    {
        $this->getCommandBus()->handle(new ToggleStoreStatusCommand($this->referenceToId($reference)));
    }

    /**
     * @Then /^the store "(.*)" should have status (enabled|disabled)$/
     */
    public function assertStoreStatus(string $reference, string $status): void
    {
        /** @var StoreForEditing $storeForEditing */
        $storeForEditing = $this->getQueryBus()->handle(new GetStoreForEditing($this->referenceToId($reference)));
        Assert::assertSame($status === 'enabled', $storeForEditing->isActive());
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Bulk status
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * @When /^I (enable|disable) multiple stores "(.+)" using bulk action$/
     */
    public function bulkToggleStatus(string $action, string $storeReferences): void
    {
        $expectedStatus = 'enable' === $action;
        $storeIds = [];

        foreach (PrimitiveUtils::castStringArrayIntoArray($storeReferences) as $storeReference) {
            $storeIds[$storeReference] = $this->referenceToId($storeReference);
        }

        $this->getCommandBus()->handle(new BulkUpdateStoreStatusCommand($expectedStatus, $storeIds));
    }

    /**
     * @Then /^stores "(.+)" should be (enabled|disabled)$/
     */
    public function assertMultipleStoreStatus(string $storeReferences, string $expectedStatus): void
    {
        $isEnabled = 'enabled' === $expectedStatus;
        foreach (PrimitiveUtils::castStringArrayIntoArray($storeReferences) as $storeReference) {
            /** @var StoreForEditing $storeForEditing */
            $storeForEditing = $this->getQueryBus()->handle(new GetStoreForEditing($this->referenceToId($storeReference)));
            if ($storeForEditing->isActive() !== $isEnabled) {
                throw new RuntimeException(sprintf(
                    'Store "%s" is %s, but expected to be %s',
                    $storeReference,
                    $storeForEditing->isActive() ? 'enabled' : 'disabled',
                    $expectedStatus
                ));
            }
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Delete
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * @When I delete store :storeReference
     */
    public function deleteStore(string $storeReference): void
    {
        try {
            $this->getCommandBus()->handle(new DeleteStoreCommand($this->referenceToId($storeReference)));
        } catch (StoreNotFoundException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When /^I delete stores "(.+)" using bulk action$/
     */
    public function bulkDeleteStores(string $storeReferences): void
    {
        $storeIds = [];
        foreach (PrimitiveUtils::castStringArrayIntoArray($storeReferences) as $storeReference) {
            $storeIds[] = $this->referenceToId($storeReference);
        }

        try {
            $this->getCommandBus()->handle(new BulkDeleteStoreCommand($storeIds));
        } catch (StoreNotFoundException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then /^stores "(.+)" should (exist|be deleted)$/
     */
    public function assertMultipleStorePresence(string $storeReferences, string $expectedPresence): void
    {
        foreach (PrimitiveUtils::castStringArrayIntoArray($storeReferences) as $storeReference) {
            $storeId = $this->referenceToId($storeReference);
            $isToBePresent = 'exist' === $expectedPresence;

            try {
                $this->getQueryBus()->handle(new GetStoreForEditing($storeId));
                if (!$isToBePresent) {
                    throw new RuntimeException(sprintf('Store "%s" still exists, expected it to be deleted', $storeReference));
                }
            } catch (StoreNotFoundException $e) {
                if ($isToBePresent) {
                    throw new RuntimeException(sprintf('Store "%s" was not found, expected it to exist', $storeReference));
                }
                $this->getSharedStorage()->clear($storeReference);
            }
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Add via CQRS command
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * @When I add store :reference using command with the following properties:
     */
    public function addStoreViaCommand(string $reference, TableNode $table): void
    {
        $data = $this->localizeByRows($table);
        $langId = $this->getDefaultLangId();
        $countryId = (int) Country::getIdByName($langId, $data['country']);

        $command = new AddStoreCommand(
            is_array($data['name']) ? $data['name'] : [$langId => $data['name']],
            is_array($data['address1']) ? $data['address1'] : [$langId => $data['address1']],
            $countryId,
            $data['city']
        );

        if (isset($data['active'])) {
            $command->setActive(PrimitiveUtils::castStringBooleanIntoBoolean($data['active']));
        }
        if (!empty($data['address2'])) {
            $command->setLocalizedAddress2(is_array($data['address2']) ? $data['address2'] : [$langId => $data['address2']]);
        }
        if (!empty($data['postcode'])) {
            $command->setPostcode($data['postcode']);
        }
        if (!empty($data['latitude'])) {
            $command->setLatitude((float) $data['latitude']);
        }
        if (!empty($data['longitude'])) {
            $command->setLongitude((float) $data['longitude']);
        }
        if (!empty($data['phone'])) {
            $command->setPhone($data['phone']);
        }
        if (!empty($data['fax'])) {
            $command->setFax($data['fax']);
        }
        if (!empty($data['email'])) {
            $command->setEmail($data['email']);
        }
        if (!empty($data['state'])) {
            $command->setStateId((int) State::getIdByName($data['state']));
        }

        try {
            /** @var StoreId $storeId */
            $storeId = $this->getCommandBus()->handle($command);
            $this->getSharedStorage()->set($reference, $storeId->getValue());
        } catch (StoreConstraintException $e) {
            $this->setLastException($e);
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Edit via CQRS command
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * @When I edit store :reference with the following properties:
     */
    public function editStore(string $reference, TableNode $table): void
    {
        $data = $this->localizeByRows($table);
        $langId = $this->getDefaultLangId();

        $command = new EditStoreCommand($this->referenceToId($reference));

        if (isset($data['name'])) {
            $command->setLocalizedNames(is_array($data['name']) ? $data['name'] : [$langId => $data['name']]);
        }
        if (isset($data['address1'])) {
            $command->setLocalizedAddress1(is_array($data['address1']) ? $data['address1'] : [$langId => $data['address1']]);
        }
        if (isset($data['address2'])) {
            $command->setLocalizedAddress2(is_array($data['address2']) ? $data['address2'] : [$langId => $data['address2']]);
        }
        if (isset($data['city'])) {
            $command->setCity($data['city']);
        }
        if (isset($data['postcode'])) {
            $command->setPostcode($data['postcode']);
        }
        if (isset($data['latitude'])) {
            $command->setLatitude((float) $data['latitude']);
        }
        if (isset($data['longitude'])) {
            $command->setLongitude((float) $data['longitude']);
        }
        if (isset($data['phone'])) {
            $command->setPhone($data['phone']);
        }
        if (isset($data['fax'])) {
            $command->setFax($data['fax']);
        }
        if (isset($data['email'])) {
            $command->setEmail($data['email']);
        }
        if (isset($data['active'])) {
            $command->setActive(PrimitiveUtils::castStringBooleanIntoBoolean($data['active']));
        }
        if (isset($data['country'])) {
            $command->setCountryId((int) Country::getIdByName($langId, $data['country']));
        }
        if (array_key_exists('state', $data)) {
            $stateId = $data['state'] !== '' ? (int) State::getIdByName($data['state']) : null;
            $command->setStateId($stateId);
        }

        try {
            $this->getCommandBus()->handle($command);
        } catch (StoreConstraintException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I edit store :reference opening hours with the following schedule:
     */
    public function editStoreHours(string $reference, TableNode $table): void
    {
        $langId = $this->getDefaultLangId();

        $command = new EditStoreCommand($this->referenceToId($reference));
        $command->setLocalizedHours($this->buildHoursFromScheduleTable($table, $langId));
        $this->getCommandBus()->handle($command);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Assertions
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * @Then store :reference should have the following properties:
     */
    public function assertStoreProperties(string $reference, TableNode $table): void
    {
        $data = $this->localizeByRows($table);
        $langId = $this->getDefaultLangId();

        /** @var StoreForEditing $storeForEditing */
        $storeForEditing = $this->getQueryBus()->handle(new GetStoreForEditing($this->referenceToId($reference)));

        if (isset($data['name'])) {
            $expected = is_array($data['name']) ? $data['name'] : [$langId => $data['name']];
            foreach ($expected as $lid => $value) {
                Assert::assertSame($value, $storeForEditing->getLocalizedNames()[$lid] ?? null, 'name');
            }
        }
        if (isset($data['active'])) {
            Assert::assertSame(PrimitiveUtils::castStringBooleanIntoBoolean($data['active']), $storeForEditing->isActive(), 'active');
        }
        if (isset($data['city'])) {
            Assert::assertSame($data['city'], $storeForEditing->getCity(), 'city');
        }
        if (isset($data['postcode'])) {
            Assert::assertSame($data['postcode'], $storeForEditing->getPostcode(), 'postcode');
        }
        if (isset($data['phone'])) {
            Assert::assertSame($data['phone'], $storeForEditing->getPhone(), 'phone');
        }
        if (isset($data['fax'])) {
            Assert::assertSame($data['fax'], $storeForEditing->getFax(), 'fax');
        }
        if (isset($data['email'])) {
            Assert::assertSame($data['email'], $storeForEditing->getEmail(), 'email');
        }
        if (isset($data['country'])) {
            $expectedCountryId = (int) Country::getIdByName($langId, $data['country']);
            Assert::assertSame($expectedCountryId, $storeForEditing->getCountryId(), 'country');
        }
        if (isset($data['state'])) {
            $expectedStateId = (int) State::getIdByName($data['state']);
            Assert::assertSame($expectedStateId, $storeForEditing->getStateId(), 'state');
        }
    }

    /**
     * @Then store :reference should have the following opening hours:
     */
    public function assertStoreOpeningHours(string $reference, TableNode $table): void
    {
        $langId = $this->getDefaultLangId();
        $expected = $this->buildHoursFromScheduleTable($table, $langId);

        /** @var StoreForEditing $storeForEditing */
        $storeForEditing = $this->getQueryBus()->handle(new GetStoreForEditing($this->referenceToId($reference)));
        $actual = $storeForEditing->getLocalizedHours();

        Assert::assertSame($expected[$langId], $actual[$langId] ?? [], 'opening hours');
    }

    /**
     * @Then I should get a store constraint error with code :errorCode
     */
    public function assertStoreConstraintError(string $errorCode): void
    {
        $code = constant(StoreConstraintException::class . '::' . $errorCode);
        $this->assertLastErrorIs(StoreConstraintException::class, $code);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Contact details
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * @When I save contact details with the following values:
     */
    public function saveContactDetails(TableNode $table): void
    {
        $data = $table->getRowsHash();
        $langId = $this->getDefaultLangId();

        $configuration = [
            'name' => $data['name'] ?? '',
            'email' => $data['email'] ?? '',
            'registration_number' => $data['registration_number'] ?? '',
            'address1' => $data['address1'] ?? '',
            'address2' => $data['address2'] ?? '',
            'postcode' => $data['postcode'] ?? '',
            'city' => $data['city'] ?? '',
            'phone' => $data['phone'] ?? '',
            'fax' => $data['fax'] ?? '',
            'id_country' => isset($data['country']) && $data['country'] !== ''
                ? (int) Country::getIdByName($langId, $data['country'])
                : 0,
            'id_state' => isset($data['state']) && $data['state'] !== ''
                ? (int) State::getIdByName($data['state'])
                : 0,
        ];

        /** @var ContactDetailsConfiguration $contactDetailsConfig */
        $contactDetailsConfig = $this->getContainer()->get(ContactDetailsConfiguration::class);
        $errors = $contactDetailsConfig->updateConfiguration($configuration);

        SharedStorage::getStorage()->set('contact_details_last_errors', $errors);
    }

    /**
     * @Then the contact details should have the following values:
     */
    public function assertContactDetailsValues(TableNode $table): void
    {
        $data = $table->getRowsHash();
        /** @var ContactDetailsConfiguration $contactDetailsConfig */
        $contactDetailsConfig = $this->getContainer()->get(ContactDetailsConfiguration::class);
        $actual = $contactDetailsConfig->getConfiguration();

        foreach (['name', 'email', 'city', 'postcode', 'phone', 'fax'] as $field) {
            if (isset($data[$field])) {
                Assert::assertSame($data[$field], $actual[$field], $field);
            }
        }
    }

    /**
     * @Then the contact details country should be :countryName
     */
    public function assertContactDetailsCountry(string $countryName): void
    {
        $expectedCountryId = (int) Country::getIdByName($this->getDefaultLangId(), $countryName);
        /** @var ContactDetailsConfiguration $contactDetailsConfig */
        $contactDetailsConfig = $this->getContainer()->get(ContactDetailsConfiguration::class);
        Assert::assertSame($expectedCountryId, $contactDetailsConfig->getConfiguration()['id_country']);
    }

    /**
     * @Then the contact details state should be :stateName
     */
    public function assertContactDetailsState(string $stateName): void
    {
        $expectedStateId = (int) State::getIdByName($stateName);
        /** @var ContactDetailsConfiguration $contactDetailsConfig */
        $contactDetailsConfig = $this->getContainer()->get(ContactDetailsConfiguration::class);
        Assert::assertSame($expectedStateId, $contactDetailsConfig->getConfiguration()['id_state']);
    }

    /**
     * @Then the contact details state should have no value
     */
    public function assertContactDetailsStateEmpty(): void
    {
        /** @var ContactDetailsConfiguration $contactDetailsConfig */
        $contactDetailsConfig = $this->getContainer()->get(ContactDetailsConfiguration::class);
        Assert::assertNull($contactDetailsConfig->getConfiguration()['id_state']);
    }

    /**
     * @Then saving contact details should fail with a validation error
     */
    public function assertContactDetailsValidationError(): void
    {
        $errors = SharedStorage::getStorage()->get('contact_details_last_errors');
        Assert::assertNotEmpty($errors, 'Expected validation errors but none were returned');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Converts a schedule table (columns: day, open, close) to the localized hours format
     * expected by AddStoreCommand / EditStoreCommand.
     *
     * @return array<int, array<int, string>>
     */
    private function buildHoursFromScheduleTable(TableNode $table, int $langId): array
    {
        $daySlots = array_fill(0, 7, '');

        foreach ($table->getColumnsHash() as $row) {
            $dayIndex = array_search($row['day'], self::DAY_ORDER, true);
            if (false === $dayIndex) {
                throw new RuntimeException(sprintf('Unknown day "%s" in schedule table', $row['day']));
            }

            $open = trim($row['open'] ?? '');
            $close = trim($row['close'] ?? '');
            $daySlots[$dayIndex] = ($open !== '' && $close !== '') ? $open . ' | ' . $close : $open;
        }

        return [$langId => $daySlots];
    }
}
