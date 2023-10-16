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
use PrestaShop\PrestaShop\Core\Domain\ApiAccess\Command\AddApiAccessCommand;
use PrestaShop\PrestaShop\Core\Domain\ApiAccess\Command\DeleteApiAccessCommand;
use PrestaShop\PrestaShop\Core\Domain\ApiAccess\Command\EditApiAccessCommand;
use PrestaShop\PrestaShop\Core\Domain\ApiAccess\Command\GenerateSecretApiAccessCommand;
use PrestaShop\PrestaShop\Core\Domain\ApiAccess\Exception\ApiAccessConstraintException;
use PrestaShop\PrestaShop\Core\Domain\ApiAccess\Exception\ApiAccessException;
use PrestaShop\PrestaShop\Core\Domain\ApiAccess\Exception\ApiAccessNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\ApiAccess\Query\GetApiAccessForEditing;
use PrestaShop\PrestaShop\Core\Domain\ApiAccess\QueryResult\EditableApiAccess;
use PrestaShop\PrestaShop\Core\Domain\ApiAccess\ValueObject\ApiAccessId;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;
use Tests\Resources\Resetter\ApiAccessResetter;

class ApiAccessManagementFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When /^I create an api access "(.+)" with following properties:$/
     */
    public function createApiAccessUsingCommand(string $apiAccessReference, TableNode $table)
    {
        $data = $this->fixDataType($table->getRowsHash());

        $command = new AddApiAccessCommand(
            $data['clientName'],
            $data['apiClientId'],
            $data['enabled'],
            $data['description'],
            PrimitiveUtils::castStringArrayIntoArray($data['scopes'] ?? '')
        );

        try {
            /** @var ApiAccessId $id */
            $id = $this->getCommandBus()->handle($command);

            $this->getSharedStorage()->set($apiAccessReference, $id->getValue());
        } catch (ApiAccessConstraintException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When /^I generate new secret for api access "(.+)"$/
     */
    public function generateSecretApiAccessUsingCommand(string $apiAccessReference)
    {
        $this->getSharedStorage()->exists($apiAccessReference);

        $commandBus = $this->getCommandBus();
        $command = new GenerateSecretApiAccessCommand($this->getSharedStorage()->get($apiAccessReference));
        $secret = $commandBus->handle($command);

        $this->getSharedStorage()->set('apiAccessSecret_' . $apiAccessReference, $secret);
    }

    /**
     * @When /^I check secret for api access "(.+)"$/
     */
    public function checkSecretApiAccessUsingCommand(string $apiAccessReference)
    {
        $this->getSharedStorage()->exists($apiAccessReference);

        $secret = $this->getSharedStorage()->get('apiAccessSecret_' . $apiAccessReference);
    }


    /**
     * @Then /^api access "(.+)" should have the following properties:$/
     */
    public function assertQueryApiAccessProperties(string $apiAccessReference, TableNode $table)
    {
        $errors = [];
        $expectedData = $table->getRowsHash();
        $this->getSharedStorage()->exists($apiAccessReference);

        /** @var EditableApiAccess $result */
        $result = $this->getQueryBus()->handle(new GetApiAccessForEditing($this->getSharedStorage()->get($apiAccessReference)));

        if (isset($expectedData['clientName'])) {
            if ($result->getClientName() !== $expectedData['clientName']) {
                $errors[] = 'clientName';
            }
        }
        if (isset($expectedData['apiClientId'])) {
            if ($result->getApiClientId() !== $expectedData['apiClientId']) {
                $errors[] = 'apiClientId';
            }
        }
        if (isset($expectedData['enabled'])) {
            if ($result->isEnabled() !== filter_var($expectedData['enabled'], FILTER_VALIDATE_BOOL)) {
                $errors[] = 'enabled';
            }
        }
        if (isset($expectedData['description'])) {
            if ($result->getDescription() !== $expectedData['description']) {
                $errors[] = 'description';
            }
        }

        if (isset($expectedData['scopes'])) {
            $expectedScopes = PrimitiveUtils::castStringArrayIntoArray($expectedData['scopes']);
            if ($result->getScopes() !== $expectedScopes) {
                $errors[] = 'scopes';
            }
        }

        if (count($errors) > 0) {
            throw new \RuntimeException(sprintf('Fields %s are not identical', implode(', ', $errors)));
        }
    }

    /**
     * @When /^I edit api access "(.+)" with the following values:$/
     */
    public function editCustomerUsingCommand(string $apiAccessReference, TableNode $table)
    {
        $this->getSharedStorage()->exists($apiAccessReference);
        $data = $this->fixDataType($table->getRowsHash());

        $commandBus = $this->getCommandBus();

        $command = new EditApiAccessCommand($this->getSharedStorage()->get($apiAccessReference));

        if (isset($data['clientName'])) {
            $command->setClientName($data['clientName']);
        }
        if (isset($data['apiClientId'])) {
            $command->setApiClientId($data['apiClientId']);
        }
        if (isset($data['enabled'])) {
            $command->setEnabled($data['enabled']);
        }
        if (isset($data['description'])) {
            $command->setDescription($data['description']);
        }
        if (isset($data['scopes'])) {
            $command->setScopes(PrimitiveUtils::castStringArrayIntoArray($data['scopes']));
        }

        try {
            $commandBus->handle($command);
        } catch (ApiAccessConstraintException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When /^I create an api access "(.+)" with a large value in (.+):$/
     */
    public function iCreateAnApiAccessWithALargeValueInFieldName(string $apiAccessReference, string $fieldName, TableNode $table)
    {
        $data = $this->generateMaxLengthValue($this->fixDataType($table->getRowsHash()), $fieldName);

        $command = new AddApiAccessCommand(
            $data['clientName'],
            $data['apiClientId'],
            $data['enabled'],
            $data['description']
        );

        try {
            /* @var ApiAccessId $id */
            $id = $this->getCommandBus()->handle($command);
            $this->getSharedStorage()->set($apiAccessReference, $id->getValue());
        } catch (ApiAccessConstraintException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When /^I edit api access "(.+)" with a large value in (.+):$/
     */
    public function iEditApiAccessWithALargeValueInApiClientId(string $apiAccessReference, string $fieldName)
    {
        $this->getSharedStorage()->exists($apiAccessReference);
        $data = $this->generateMaxLengthValue([], $fieldName);

        $commandBus = $this->getCommandBus();

        $command = new EditApiAccessCommand($this->getSharedStorage()->get($apiAccessReference));

        if (isset($data['clientName'])) {
            $command->setClientName($data['clientName']);
        }
        if (isset($data['apiClientId'])) {
            $command->setApiClientId($data['apiClientId']);
        }
        if (isset($data['enabled'])) {
            $command->setEnabled($data['enabled']);
        }
        if (isset($data['description'])) {
            $command->setDescription($data['description']);
        }

        try {
            $commandBus->handle($command);
        } catch (ApiAccessException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When /^I delete api access "(.+)"$/
     */
    public function iDeleteApiAccess(string $apiAccessReference)
    {
        $this->getCommandBus()->handle(new DeleteApiAccessCommand($this->getSharedStorage()->get($apiAccessReference)));
    }

    /**
     * @Then /^api access "(.+)" should not exist$/
     */
    public function checkApiAccessNotFound(string $apiAccessReference)
    {
        try {
            $this->getCommandBus()->handle(new GetApiAccessForEditing($this->getSharedStorage()->get($apiAccessReference)));
        } catch (ApiAccessNotFoundException $e) {
            return;
        }
        throw new \RuntimeException(sprintf('API Access %s still exists', $apiAccessReference));
    }

    /**
     * @Then I should get an error that :fieldName is not unique
     */
    public function iShouldGetAnErrorThatFieldIsNotUnique(string $fieldName): void
    {
        $this->assertLastErrorIs(
            ApiAccessConstraintException::class,
            $this->getUnicityConstraintErrorCode($fieldName)
        );
    }

    /**
     * @Then I should get an error that :fieldName is invalid
     */
    public function iShouldGetAnErrorThatFieldIsInvalid(string $fieldName): void
    {
        $this->assertLastErrorIs(
            ApiAccessConstraintException::class,
            $this->getInvalidConstraintErrorCode($fieldName)
        );
    }

    /**
     * @Then I should get an error that :fieldName is too large
     */
    public function iShouldGetAnErrorThatFieldIsTooLarge(string $fieldName): void
    {
        $this->assertLastErrorIs(
            ApiAccessConstraintException::class,
            $this->getTooLargeConstraintErrorCode($fieldName)
        );
    }

    /**
     * @Then I should get a new secret
     */
    public function iShouldGetANewSecret(): void
    {
        // @todo
    }

    /**
     * @Then I should get the correct secret
     */
    public function iShouldGetTheCorrectSecret(): void
    {
        // @todo
    }

    /**
     * @BeforeFeature @restore-api-access-before-feature
     */
    public static function restoreProductTablesBeforeFeature(): void
    {
        ApiAccessResetter::resetApiAccess();
    }

    private function fixDataType(array $data): array
    {
        if (array_key_exists('enabled', $data) && !is_null($data['enabled'])) {
            $data['enabled'] = filter_var($data['enabled'], FILTER_VALIDATE_BOOL);
        }

        return $data;
    }

    private function getUnicityConstraintErrorCode(string $fieldName): int
    {
        $constraintErrorFieldMap = [
            'clientId' => ApiAccessConstraintException::CLIENT_ID_ALREADY_USED,
            'clientName' => ApiAccessConstraintException::CLIENT_NAME_ALREADY_USED,
        ];

        if (!array_key_exists($fieldName, $constraintErrorFieldMap)) {
            throw new \RuntimeException(sprintf('"%s" is not mapped with constraint error code', $fieldName));
        }

        return $constraintErrorFieldMap[$fieldName];
    }

    private function getInvalidConstraintErrorCode(string $fieldName): int
    {
        $constraintErrorFieldMap = [
            'apiClientId' => ApiAccessConstraintException::INVALID_CLIENT_ID,
            'clientName' => ApiAccessConstraintException::INVALID_CLIENT_NAME,
            'enabled' => ApiAccessConstraintException::INVALID_ENABLED,
            'description' => ApiAccessConstraintException::INVALID_DESCRIPTION,
            'scopes' => ApiAccessConstraintException::NON_INSTALLED_SCOPES,
        ];

        if (!array_key_exists($fieldName, $constraintErrorFieldMap)) {
            throw new \RuntimeException(sprintf('"%s" is not mapped with constraint error code', $fieldName));
        }

        return $constraintErrorFieldMap[$fieldName];
    }

    private function getTooLargeConstraintErrorCode(string $fieldName): int
    {
        $constraintErrorFieldMap = [
            'apiClientId' => ApiAccessConstraintException::CLIENT_ID_TOO_LARGE,
            'clientName' => ApiAccessConstraintException::CLIENT_NAME_TOO_LARGE,
            'description' => ApiAccessConstraintException::DESCRIPTION_TOO_LARGE,
        ];

        if (!array_key_exists($fieldName, $constraintErrorFieldMap)) {
            throw new \RuntimeException(sprintf('"%s" is not mapped with constraint error code', $fieldName));
        }

        return $constraintErrorFieldMap[$fieldName];
    }

    private function generateMaxLengthValue(array $data, string $fieldName): array
    {
        $length = match ($fieldName) {
            'apiClientId', 'clientName' => 260,
            'description' => 21900,
            default => throw new \RuntimeException(sprintf('The field %s cannot have a max value generated', $fieldName)),
        };

        $data[$fieldName] = bin2hex(random_bytes($length));

        return $data;
    }
}
