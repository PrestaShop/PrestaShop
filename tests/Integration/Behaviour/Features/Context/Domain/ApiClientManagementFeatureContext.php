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
use PrestaShop\PrestaShop\Core\Context\ApiClient;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Command\AddApiClientCommand;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Command\DeleteApiClientCommand;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Command\EditApiClientCommand;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Command\GenerateApiClientSecretCommand;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Exception\ApiClientConstraintException;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Exception\ApiClientException;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Exception\ApiClientNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Query\GetApiClientForEditing;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\QueryResult\EditableApiClient;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\ValueObject\ApiClientId;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\ValueObject\CreatedApiClient;
use PrestaShopBundle\Entity\Repository\ApiClientRepository;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Tests\Integration\Behaviour\Features\Context\CommonFeatureContext;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;
use Tests\Resources\Context\ApiClientContextDecorator;
use Tests\Resources\Resetter\ApiClientResetter;

class ApiClientManagementFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I create an api client :apiClientReference with following properties:
     */
    public function createApiClientUsingCommand(string $apiClientReference, TableNode $table)
    {
        $this->createApiClient($apiClientReference, $table);
    }

    /**
     * @When I create an api client :apiClientReference with generated secret :secretReference using following properties:
     */
    public function createApiClientUsingCommandAndStoreSecret(string $apiClientReference, string $secretReference, TableNode $table)
    {
        $this->createApiClient($apiClientReference, $table, $secretReference);
    }

    /**
     * @When I generate new secret :secretReference for api client :apiClientReference
     */
    public function generateSecretApiClientUsingCommand(string $secretReference, string $apiClientReference)
    {
        $this->getSharedStorage()->exists($apiClientReference);

        $commandBus = $this->getCommandBus();
        $command = new GenerateApiClientSecretCommand($this->getSharedStorage()->get($apiClientReference));
        $newSecret = $commandBus->handle($command);
        $this->getSharedStorage()->set($secretReference, $newSecret);
    }

    /**
     * @Then /^api client "(.+)" should have the following properties:$/
     */
    public function assertQueryApiClientProperties(string $apiClientReference, TableNode $table)
    {
        $errors = [];
        $expectedData = $table->getRowsHash();
        $this->getSharedStorage()->exists($apiClientReference);

        /** @var EditableApiClient $result */
        $result = $this->getQueryBus()->handle(new GetApiClientForEditing($this->getSharedStorage()->get($apiClientReference)));

        if (isset($expectedData['clientName'])) {
            if ($result->getClientName() !== $expectedData['clientName']) {
                $errors[] = 'clientName';
            }
        }
        if (isset($expectedData['apiClientId'])) {
            if ($result->getClientId() !== $expectedData['apiClientId']) {
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
     * @When /^I edit api client "(.+)" with the following values:$/
     */
    public function editCustomerUsingCommand(string $apiClientReference, TableNode $table)
    {
        $this->getSharedStorage()->exists($apiClientReference);
        $data = $this->fixDataType($table->getRowsHash());

        $commandBus = $this->getCommandBus();

        $command = new EditApiClientCommand($this->getSharedStorage()->get($apiClientReference));

        if (isset($data['clientName'])) {
            $command->setClientName($data['clientName']);
        }
        if (isset($data['apiClientId'])) {
            $command->setClientId($data['apiClientId']);
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
        if (isset($data['lifetime'])) {
            $command->setLifetime($data['lifetime']);
        }

        try {
            $commandBus->handle($command);
        } catch (ApiClientConstraintException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When /^I create an api client "(.+)" with a large value in (.+):$/
     */
    public function iCreateAnApiClientWithALargeValueInFieldName(string $apiClientReference, string $fieldName, TableNode $table)
    {
        $data = $this->generateMaxLengthValue($this->fixDataType($table->getRowsHash()), $fieldName);

        $command = new AddApiClientCommand(
            $data['clientName'],
            $data['apiClientId'],
            $data['enabled'],
            $data['description'],
            $data['lifetime'],
            PrimitiveUtils::castStringArrayIntoArray($data['scopes'] ?? '')
        );

        try {
            /* @var ApiClientId $id */
            $id = $this->getCommandBus()->handle($command);
            $this->getSharedStorage()->set($apiClientReference, $id->getValue());
        } catch (ApiClientConstraintException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When /^I edit api client "(.+)" with a large value in (.+):$/
     */
    public function iEditApiClientWithALargeValueInApiClientId(string $apiClientReference, string $fieldName)
    {
        $this->getSharedStorage()->exists($apiClientReference);
        $data = $this->generateMaxLengthValue([], $fieldName);

        $commandBus = $this->getCommandBus();

        $command = new EditApiClientCommand($this->getSharedStorage()->get($apiClientReference));

        if (isset($data['clientName'])) {
            $command->setClientName($data['clientName']);
        }
        if (isset($data['apiClientId'])) {
            $command->setClientId($data['apiClientId']);
        }
        if (isset($data['enabled'])) {
            $command->setEnabled($data['enabled']);
        }
        if (isset($data['description'])) {
            $command->setDescription($data['description']);
        }

        try {
            $commandBus->handle($command);
        } catch (ApiClientException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When /^I delete api client "(.+)"$/
     */
    public function iDeleteApiClient(string $apiClientReference)
    {
        $this->getCommandBus()->handle(new DeleteApiClientCommand($this->getSharedStorage()->get($apiClientReference)));
    }

    /**
     * @Then /^api client "(.+)" should not exist$/
     */
    public function checkApiClientNotFound(string $apiClientReference)
    {
        try {
            $this->getCommandBus()->handle(new GetApiClientForEditing($this->getSharedStorage()->get($apiClientReference)));
        } catch (ApiClientNotFoundException $e) {
            return;
        }
        throw new \RuntimeException(sprintf('API Client %s still exists', $apiClientReference));
    }

    /**
     * @Then I should get an error that :fieldName is not unique
     */
    public function iShouldGetAnErrorThatFieldIsNotUnique(string $fieldName): void
    {
        $this->assertLastErrorIs(
            ApiClientConstraintException::class,
            $this->getUnicityConstraintErrorCode($fieldName)
        );
    }

    /**
     * @Then I should get an error that :fieldName is invalid
     */
    public function iShouldGetAnErrorThatFieldIsInvalid(string $fieldName): void
    {
        $this->assertLastErrorIs(
            ApiClientConstraintException::class,
            $this->getInvalidConstraintErrorCode($fieldName)
        );
    }

    /**
     * @Then I should get an error that :fieldName is too large
     */
    public function iShouldGetAnErrorThatFieldIsTooLarge(string $fieldName): void
    {
        $this->assertLastErrorIs(
            ApiClientConstraintException::class,
            $this->getTooLargeConstraintErrorCode($fieldName)
        );
    }

    /**
     * @Then secret :secretReference is valid for api client :apiClientReference
     */
    public function assertSecretIsValid(string $secretReference, string $apiClientReference): void
    {
        $this->assertSecret($secretReference, $apiClientReference, true);
    }

    /**
     * @Then secret :secretReference is invalid for api client :apiClientReference
     */
    public function assertSecretIsInvalid(string $secretReference, string $apiClientReference): void
    {
        $this->assertSecret($secretReference, $apiClientReference, false);
    }

    private function assertSecret(string $secretReference, string $apiClientReference, bool $expected): void
    {
        // Manually get the entity because secret is not part of the CQRS query
        $apiClientRepository = $this->getContainer()->get(ApiClientRepository::class);
        $apiClient = $apiClientRepository->getById($this->getSharedStorage()->get($apiClientReference));
        $hashedSecret = $apiClient->getClientSecret();

        $plainSecret = $this->getSharedStorage()->get($secretReference);
        $passwordHasher = $this->getContainer()->get(PasswordHasherInterface::class);
        if ($expected !== $passwordHasher->verify($hashedSecret, $plainSecret)) {
            throw new \RuntimeException(sprintf(
                'Secret %s was expected to be %s',
                $secretReference,
                $expected ? 'valid' : 'invalid'
            ));
        }
    }

    /**
     * @BeforeFeature @restore-api-client-before-feature
     * @AfterFeature @restore-api-client-after-feature     */
    public static function restoreApiClientTables(): void
    {
        ApiClientResetter::resetApiClient();
    }

    /**
     * @Given I am logged in as api client with id :apiClientId
     */
    public function logsInAsApiClient(string $apiClientId): void
    {
        $apiAccessRepository = CommonFeatureContext::getContainer()->get(ApiClientRepository::class);
        $apiClient = $apiAccessRepository->getByClientId($apiClientId);
        /** @var ApiClientContextDecorator $apiClientContext */
        $apiClientContext = CommonFeatureContext::getContainer()->get(ApiClientContextDecorator::class);
        $apiClientContext->setOverriddenApiClient(new ApiClient(
            id: $apiClient->getId(),
            clientId: $apiClient->getClientId(),
            shopId: $this->getDefaultShopId(),
            scopes: $apiClient->getScopes(),
        ));
    }

    /**
     * @Given I am not logged in as an api client
     */
    public function logsOutApiClient()
    {
        /** @var ApiClientContextDecorator $apiClientContext */
        $apiClientContext = CommonFeatureContext::getContainer()->get(ApiClientContextDecorator::class);
        $apiClientContext->setOverriddenApiClient(null);
    }

    /**
     * @AfterFeature
     */
    public static function resetApiClientContext(): void
    {
        /** @var ApiClientContextDecorator $apiClientContext */
        $apiClientContext = CommonFeatureContext::getContainer()->get(ApiClientContextDecorator::class);
        $apiClientContext->resetOverriddenApiClient();
    }

    private function createApiClient(string $apiClientReference, TableNode $table, string $secretReference = null): void
    {
        $data = $this->fixDataType($table->getRowsHash());

        $command = new AddApiClientCommand(
            $data['clientName'],
            $data['apiClientId'],
            $data['enabled'],
            $data['description'],
            $data['lifetime'],
            PrimitiveUtils::castStringArrayIntoArray($data['scopes'] ?? '')
        );

        try {
            /** @var CreatedApiClient $apiClient */
            $apiClient = $this->getCommandBus()->handle($command);

            $this->getSharedStorage()->set($apiClientReference, $apiClient->getApiClientId()->getValue());
            if (!empty($secretReference)) {
                $this->getSharedStorage()->set($secretReference, $apiClient->getSecret());
            }
        } catch (ApiClientConstraintException $e) {
            $this->setLastException($e);
        }
    }

    private function fixDataType(array $data): array
    {
        if (array_key_exists('enabled', $data) && !is_null($data['enabled'])) {
            $data['enabled'] = PrimitiveUtils::castStringBooleanIntoBoolean($data['enabled']);
        }

        if (array_key_exists('lifetime', $data) && !is_null($data['lifetime'])) {
            $data['lifetime'] = intval($data['lifetime']);
        }

        return $data;
    }

    private function getUnicityConstraintErrorCode(string $fieldName): int
    {
        $constraintErrorFieldMap = [
            'clientId' => ApiClientConstraintException::CLIENT_ID_ALREADY_USED,
            'clientName' => ApiClientConstraintException::CLIENT_NAME_ALREADY_USED,
        ];

        if (!array_key_exists($fieldName, $constraintErrorFieldMap)) {
            throw new \RuntimeException(sprintf('"%s" is not mapped with constraint error code', $fieldName));
        }

        return $constraintErrorFieldMap[$fieldName];
    }

    private function getInvalidConstraintErrorCode(string $fieldName): int
    {
        $constraintErrorFieldMap = [
            'apiClientId' => ApiClientConstraintException::INVALID_CLIENT_ID,
            'clientName' => ApiClientConstraintException::INVALID_CLIENT_NAME,
            'enabled' => ApiClientConstraintException::INVALID_ENABLED,
            'description' => ApiClientConstraintException::INVALID_DESCRIPTION,
            'scopes' => ApiClientConstraintException::NON_INSTALLED_SCOPES,
            'lifetime' => ApiClientConstraintException::NOT_POSITIVE_LIFETIME,
        ];

        if (!array_key_exists($fieldName, $constraintErrorFieldMap)) {
            throw new \RuntimeException(sprintf('"%s" is not mapped with constraint error code', $fieldName));
        }

        return $constraintErrorFieldMap[$fieldName];
    }

    private function getTooLargeConstraintErrorCode(string $fieldName): int
    {
        $constraintErrorFieldMap = [
            'apiClientId' => ApiClientConstraintException::CLIENT_ID_TOO_LARGE,
            'clientName' => ApiClientConstraintException::CLIENT_NAME_TOO_LARGE,
            'description' => ApiClientConstraintException::DESCRIPTION_TOO_LARGE,
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
