<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\Command\AddQuickAccessCommand;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\Command\BulkDeleteQuickAccessCommand;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\Command\DeleteQuickAccessCommand;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\Command\EditQuickAccessCommand;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\Command\ToggleQuickAccessNewWindowCommand;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\Exception\QuickAccessConstraintException;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\Exception\QuickAccessException;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\Exception\QuickAccessNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\Query\GetQuickAccessForEditing;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\QueryResult\EditableQuickAccess;
use PrestaShop\PrestaShop\Core\Domain\QuickAccess\ValueObject\QuickAccessId;
use Tests\Integration\Behaviour\Features\Context\Util\NoExceptionAlthoughExpectedException;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class QuickAccessFeatureContext extends AbstractDomainFeatureContext
{
    private const NON_EXISTING_QUICK_ACCESS_ID = 74099901;

    /**
     * @Given quick access :reference does not exist
     */
    public function setNonExistingQuickAccessReference(string $reference): void
    {
        $this->getSharedStorage()->set($reference, self::NON_EXISTING_QUICK_ACCESS_ID);
    }

    /**
     * @When I add a quick access :reference with the following properties:
     */
    public function addQuickAccess(string $reference, TableNode $table): void
    {
        $data = $this->localizeByRows($table);

        try {
            /** @var QuickAccessId $id */
            $id = $this->getCommandBus()->handle(new AddQuickAccessCommand(
                $data['localizedNames'],
                (string) $data['link'],
                PrimitiveUtils::castStringBooleanIntoBoolean($data['new_window'])
            ));
            $this->getSharedStorage()->set($reference, $id->getValue());
        } catch (QuickAccessException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I edit quick access :reference with the following properties:
     */
    public function editQuickAccess(string $reference, TableNode $table): void
    {
        $data = $this->localizeByRows($table);
        $id = $this->referenceToId($reference);

        $command = new EditQuickAccessCommand($id);

        if (isset($data['localizedNames'])) {
            $command->setLocalizedNames($data['localizedNames']);
        }
        if (isset($data['link'])) {
            $command->setLink((string) $data['link']);
        }
        if (isset($data['new_window'])) {
            $command->setNewWindow(PrimitiveUtils::castStringBooleanIntoBoolean($data['new_window']));
        }

        try {
            $this->getCommandBus()->handle($command);
        } catch (QuickAccessException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I delete quick access :reference
     */
    public function deleteQuickAccess(string $reference): void
    {
        try {
            $this->getCommandBus()->handle(new DeleteQuickAccessCommand($this->referenceToId($reference)));
        } catch (QuickAccessException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I bulk delete quick accesses :references
     */
    public function bulkDeleteQuickAccesses(string $references): void
    {
        $ids = $this->referencesToIds($references);

        try {
            $this->getCommandBus()->handle(new BulkDeleteQuickAccessCommand($ids));
        } catch (QuickAccessException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I toggle the new_window flag for quick access :reference
     */
    public function toggleQuickAccessNewWindow(string $reference): void
    {
        try {
            $this->getCommandBus()->handle(new ToggleQuickAccessNewWindowCommand($this->referenceToId($reference)));
        } catch (QuickAccessException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then quick access :reference should have the following properties:
     */
    public function assertQuickAccessProperties(string $reference, TableNode $table): void
    {
        $data = $this->localizeByRows($table);
        $result = $this->getQuickAccessFromReference($reference);

        if (isset($data['localizedNames'])) {
            Assert::assertEquals($data['localizedNames'], $result->getLocalizedNames());
        }
        if (isset($data['link'])) {
            Assert::assertEquals($data['link'], $result->getLink());
        }
        if (isset($data['new_window'])) {
            Assert::assertEquals(
                PrimitiveUtils::castStringBooleanIntoBoolean($data['new_window']),
                $result->isNewWindow()
            );
        }
    }

    /**
     * @Then quick access :reference should be deleted
     */
    public function assertQuickAccessIsDeleted(string $reference): void
    {
        if ($this->quickAccessExists($reference)) {
            throw new NoExceptionAlthoughExpectedException(
                sprintf('Quick access "%s" exists but was expected to be deleted', $reference)
            );
        }
    }

    /**
     * @Then quick accesses :references should be deleted
     */
    public function assertQuickAccessesAreDeleted(string $references): void
    {
        foreach (PrimitiveUtils::castStringArrayIntoArray($references) as $reference) {
            $this->assertQuickAccessIsDeleted($reference);
        }
    }

    /**
     * @Then I should get error that quick access was not found
     */
    public function assertLastErrorQuickAccessNotFound(): void
    {
        $this->assertLastErrorIs(QuickAccessNotFoundException::class);
    }

    /**
     * @Then I should get error that quick access link already exists
     */
    public function assertLastErrorLinkAlreadyExists(): void
    {
        $this->assertLastErrorIs(
            QuickAccessConstraintException::class,
            QuickAccessConstraintException::LINK_ALREADY_EXISTS
        );
    }

    private function getQuickAccessFromReference(string $reference): EditableQuickAccess
    {
        /** @var EditableQuickAccess $result */
        $result = $this->getQueryBus()->handle(new GetQuickAccessForEditing($this->referenceToId($reference)));

        return $result;
    }

    private function quickAccessExists(string $reference): bool
    {
        try {
            $this->getQuickAccessFromReference($reference);

            return true;
        } catch (QuickAccessNotFoundException) {
            return false;
        }
    }
}
