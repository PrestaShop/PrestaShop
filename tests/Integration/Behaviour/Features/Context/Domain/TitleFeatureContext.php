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

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert as Assert;
use PrestaShop\PrestaShop\Core\Domain\Title\Command\AddTitleCommand;
use PrestaShop\PrestaShop\Core\Domain\Title\Command\BulkDeleteTitleCommand;
use PrestaShop\PrestaShop\Core\Domain\Title\Command\DeleteTitleCommand;
use PrestaShop\PrestaShop\Core\Domain\Title\Command\EditTitleCommand;
use PrestaShop\PrestaShop\Core\Domain\Title\Exception\CannotAddTitleException;
use PrestaShop\PrestaShop\Core\Domain\Title\Exception\CannotUpdateTitleException;
use PrestaShop\PrestaShop\Core\Domain\Title\Exception\TitleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Title\Exception\TitleException;
use PrestaShop\PrestaShop\Core\Domain\Title\Exception\TitleNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Title\Query\GetTitleForEditing;
use PrestaShop\PrestaShop\Core\Domain\Title\QueryResult\EditableTitle;
use PrestaShop\PrestaShop\Core\Domain\Title\ValueObject\TitleId;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\CommonFeatureContext;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use Tests\Integration\Behaviour\Features\Context\Util\NoExceptionAlthoughExpectedException;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class TitleFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I add a new title :titleReference with the following properties:
     *
     * @param string $titleReference
     * @param TableNode $table
     */
    public function createTitle(string $titleReference, TableNode $table): void
    {
        $data = $this->localizeByRows($table);

        /** @var FormChoiceProviderInterface $provider */
        $provider = CommonFeatureContext::getContainer()->get('prestashop.core.form.choice_provider.gender_choice_provider');
        $availableGendersTypes = $provider->getChoices();
        if (!isset($availableGendersTypes[$data['type']])) {
            throw new RuntimeException(sprintf('Gender type "%s" is not available.', $data['type']));
        }

        try {
            /** @var TitleId $titleId */
            $titleId = $this->getCommandBus()->handle(new AddTitleCommand(
                $data['name'],
                (int) $availableGendersTypes[$data['type']]
            ));

            SharedStorage::getStorage()->set($titleReference, $titleId->getValue());
        } catch (CannotAddTitleException|TitleConstraintException|TitleException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I edit title :titleReference with following properties:
     *
     * @param string $titleReference
     * @param TableNode $table
     */
    public function editTitle(string $titleReference, TableNode $table): void
    {
        $editableTitle = $this->getTitleForEdition($titleReference);

        $command = new EditTitleCommand($editableTitle->getTitleId());

        $data = $this->localizeByRows($table);
        if (isset($data['name'])) {
            $command->setLocalizedNames($data['name']);
        }
        if (isset($data['type'])) {
            /** @var FormChoiceProviderInterface $provider */
            $provider = CommonFeatureContext::getContainer()->get('prestashop.core.form.choice_provider.gender_choice_provider');
            $availableGendersTypes = $provider->getChoices();
            if (!isset($availableGendersTypes[$data['type']])) {
                throw new RuntimeException(sprintf('Gender type "%s" is not available.', $data['type']));
            }

            $command->setGender((int) $availableGendersTypes[$data['type']]);
        }

        try {
            $this->getCommandBus()->handle($command);
        } catch (CannotUpdateTitleException|TitleConstraintException|TitleException|TitleNotFoundException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I delete the title :titleReference
     *
     * @param string $titleReference
     */
    public function deleteTitle(string $titleReference): void
    {
        /** @var int $titleId */
        $titleId = SharedStorage::getStorage()->get($titleReference);

        try {
            $this->getCommandBus()->handle(new DeleteTitleCommand((int) $titleId));
        } catch (TitleNotFoundException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I delete titles :titleReferences using bulk action
     *
     * @param string $titleReferences
     */
    public function bulkDeleteTitles(string $titleReferences): void
    {
        $titleIds = [];
        foreach (PrimitiveUtils::castStringArrayIntoArray($titleReferences) as $titleReference) {
            $titleIds[] = (int) SharedStorage::getStorage()->get($titleReference);
        }

        try {
            $this->getCommandBus()->handle(new BulkDeleteTitleCommand($titleIds));
        } catch (TitleNotFoundException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then I should get an error that the title has not been found
     */
    public function assertLastErrorTitleNotFound(): void
    {
        $this->assertLastErrorIs(TitleNotFoundException::class);
    }

    /**
     * @Then the title :titleReference should have the following properties:
     */
    public function assertTitleProperties(string $titleReference, TableNode $table): void
    {
        /** @var FormChoiceProviderInterface $provider */
        $provider = CommonFeatureContext::getContainer()->get('prestashop.core.form.choice_provider.gender_choice_provider');
        $availableGendersTypes = $provider->getChoices();

        $expectedData = $this->localizeByRows($table);

        $result = $this->getTitleForEdition($titleReference);

        Assert::assertEquals($expectedData['name'], $result->getLocalizedNames());
        Assert::assertEquals(
            $availableGendersTypes[$expectedData['type']],
            $result->getGender(),
            sprintf(
                'Failed asserting that "%s" matches expected "%s".',
                array_flip($availableGendersTypes)[$result->getGender()],
                $expectedData['type']
            )
        );
    }

    /**
     * @Then /^the title "(.+)" should be deleted$/
     *
     * @param string $titleReference
     */
    public function assertTitleIsDeleted(string $titleReference): void
    {
        if ($this->isFoundTitle($titleReference)) {
            throw new NoExceptionAlthoughExpectedException(sprintf('Title %s exist, but it was expected to be deleted', $titleReference));
        }
    }

    /**
     * @Then /^the title "(.+)" should not be deleted$/
     *
     * @param string $titleReference
     */
    public function assertTitleIsNotDeleted(string $titleReference): void
    {
        if (!$this->isFoundTitle($titleReference)) {
            throw new NoExceptionAlthoughExpectedException(sprintf('Title %s doesn\'t exist, but it was expected to be existing', $titleReference));
        }
    }

    /**
     * @Then titles :titleReferences should be deleted
     *
     * @param string $titleReferences
     */
    public function assertTitlesAreDeleted(string $titleReferences): void
    {
        foreach (PrimitiveUtils::castStringArrayIntoArray($titleReferences) as $titleReference) {
            $this->assertTitleIsDeleted($titleReference);
        }
    }

    /**
     * @param string $reference
     *
     * @return EditableTitle
     */
    protected function getTitleForEdition(string $reference): EditableTitle
    {
        $titleId = $this->getSharedStorage()->get($reference);

        return $this->getQueryBus()->handle(new GetTitleForEditing($titleId));
    }

    protected function isFoundTitle(string $titleReference): bool
    {
        try {
            $this->getTitleForEdition($titleReference);

            return true;
        } catch (TitleNotFoundException $e) {
            return false;
        }
    }
}
