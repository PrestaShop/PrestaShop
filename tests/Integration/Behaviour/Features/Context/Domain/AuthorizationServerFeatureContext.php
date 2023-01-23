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
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\Command\AddApplicationCommand;
use PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\Command\EditApplicationCommand;
use PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\Exception\ApplicationConstraintException;
use PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\Exception\ApplicationException;
use PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\Exception\ApplicationNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\Exception\DuplicateApplicationNameException;
use PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\Query\GetApplicationForEditing;
use PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\QueryResult\EditableApplication;
use PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\ValueObject\ApplicationId;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

class AuthorizationServerFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I add new authorized application :reference with following properties:
     */
    public function createAuthorizedApplication(string $reference, TableNode $node): void
    {
        $data = $node->getRowsHash();
        $command = new AddApplicationCommand($data['name'], $data['description']);

        /** @var ApplicationId $applicationId */
        $applicationId = $this->getCommandBus()->handle($command);

        SharedStorage::getStorage()->set($reference, $applicationId);
    }

    /**
     * @When I add new already exist authorized application :reference with following properties:
     */
    public function createAlreadyExistAuthorizedApplication(string $reference, TableNode $node): void
    {
        try {
            $this->createAuthorizedApplication($reference, $node);
        } catch (ApplicationException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I update authorized application :reference with the following details:
     */
    public function updateAuthorizedApplication(string $reference, TableNode $node): void
    {
        $data = $node->getRowsHash();
        /** @var ApplicationId $applicationId */
        $applicationId = SharedStorage::getStorage()->get($reference);
        $command = new EditApplicationCommand($applicationId->getValue());
        $command->setName($data['name']);
        $command->setDescription($data['description']);
        $this->getCommandBus()->handle($command);
    }

    /**
     * @When I update already exist authorized application :reference with following properties:
     */
    public function updateAlreadyExistAuthorizedApplication(string $reference, TableNode $node): void
    {
        try {
            $this->updateAuthorizedApplication($reference, $node);
        } catch (ApplicationException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I update non-existent authorized application
     */
    public function updateNonExistentAuthorizedApplication(): void
    {
        try {
            $command = new GetApplicationForEditing(99999);
            $this->getCommandBus()->handle($command);
        } catch (ApplicationException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then I should get error that authorized application does not exist
     */
    public function assertAuthorizedApplicationDoesNotExistError(): void
    {
        $this->assertLastErrorIs(ApplicationNotFoundException::class);
    }

    /**
     * @Then I should get error that authorized application with this name already exists
     */
    public function assertAuthorizedApplicationAlreadyExistError(): void
    {
        $this->assertLastErrorIs(DuplicateApplicationNameException::class);
    }

    /**
     * @Then authorized application :reference should have the following details:
     *
     * @throws ApplicationConstraintException
     */
    public function assertAuthorizedApplicationName(string $reference, TableNode $node): void
    {
        $data = $node->getRowsHash();
        /** @var ApplicationId $applicationId */
        $applicationId = SharedStorage::getStorage()->get($reference);
        $expectedEditableApplication = new EditableApplication($applicationId, $data['name'], $data['description']);
        $editableApplication = $this->getQueryBus()->handle(new GetApplicationForEditing($applicationId->getValue()));
        Assert::assertEquals($expectedEditableApplication, $editableApplication);
    }
}
