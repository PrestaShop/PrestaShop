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

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\AddEmployeeCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\BulkDeleteEmployeeCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\BulkUpdateEmployeeStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\DeleteEmployeeCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\EditEmployeeCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\ResetEmployeePasswordCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\SendEmployeePasswordResetEmailCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\ToggleEmployeeStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Query\GetEmployeeForEditing;
use PrestaShop\PrestaShop\Core\Domain\Employee\QueryResult\EditableEmployee;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\EmployeeId;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\LanguageChoiceProvider;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\ProfileChoiceProvider;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\TabChoiceProvider;
use PrestaShopBundle\Entity\Repository\EmployeeRepository;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Tests\Integration\Behaviour\Features\Context\CommonFeatureContext;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class EmployeeFeatureContext extends AbstractDomainFeatureContext
{
    private const MIN_PASSWORD_LENGTH = 1;
    private const MAX_PASSWORD_LENGTH = 72;
    private const MIN_PASSWORD_SCORE = 1;

    /**
     * @Given I Add new employee :employeeReference to shop :shopReference with the following details:
     *
     * @param string $employeeReference
     * @param string $shopReference
     * @param TableNode $table
     */
    public function addNewEmployeeToShopWithTheFollowingDetails(
        string $employeeReference,
        string $shopReference,
        TableNode $table
    ) {
        $testCaseData = $table->getRowsHash();

        $data = $this->mapDataWithSelectedValues($testCaseData, $shopReference);

        /** @var EmployeeId $employeeIdObject */
        $employeeIdObject = $this->getCommandBus()->handle(new AddEmployeeCommand(
            $data['firstName'],
            $data['lastName'],
            $data['email'],
            $data['plainPassword'],
            $data['defaultPageId'],
            $data['languageId'],
            $data['active'],
            $data['profileId'],
            $data['shopAssociation'],
            false, // has enable gravatar
            self::MIN_PASSWORD_LENGTH,
            self::MAX_PASSWORD_LENGTH,
            self::MIN_PASSWORD_SCORE
        ));

        SharedStorage::getStorage()->set($employeeReference, $employeeIdObject->getValue());
    }

    /**
     * @Given I edit employee :employeeReference with the following details:
     *
     * @param string $employeeReference
     * @param TableNode $table
     */
    public function editEmployeeToShopWithTheFollowingDetails(string $employeeReference, TableNode $table): void
    {
        $tableData = $table->getRowsHash();

        $data = $this->mapDataWithSelectedValues($tableData, $tableData['Associated shops'] ?? null);

        $command = new EditEmployeeCommand($this->referenceToId($employeeReference));

        if (isset($data['firstName'])) {
            $command->setFirstName($data['firstName']);
        }
        if (isset($data['lastName'])) {
            $command->setLastName($data['lastName']);
        }
        if (isset($data['email'])) {
            $command->setEmail($data['email']);
        }
        if (isset($data['plainPassword'])) {
            $command->setPlainPassword($data['plainPassword'], self::MIN_PASSWORD_LENGTH, self::MAX_PASSWORD_LENGTH, self::MIN_PASSWORD_SCORE);
        }
        if (isset($data['defaultPageId'])) {
            $command->setDefaultPageId($data['defaultPageId']);
        }
        if (isset($data['languageId'])) {
            $command->setLanguageId($data['languageId']);
        }
        if (isset($data['profileId'])) {
            $command->setProfileId($data['profileId']);
        }
        if (isset($data['shopAssociation'])) {
            $command->setShopAssociation($data['shopAssociation']);
        }
        if (isset($data['active'])) {
            $command->setActive($data['active']);
        }

        $this->getCommandBus()->handle($command);
    }

    /**
     * @When I delete the employee :employeeReference
     */
    public function deleteEmployee(string $employeeReference): void
    {
        $this->getCommandBus()->handle(new DeleteEmployeeCommand($this->referenceToId($employeeReference)));
    }

    /**
     * @When I bulk delete the employees :employeeReferences
     */
    public function bulkDeleteEmployee(string $employeeReferences): void
    {
        $this->getCommandBus()->handle(new BulkDeleteEmployeeCommand($this->referencesToIds($employeeReferences)));
    }

    /**
     * @Then employee :employeeReference does not exist
     */
    public function assertEmployeeNotFound(string $employeeReference): void
    {
        $caughtException = null;
        try {
            $this->getCommandBus()->handle(new GetEmployeeForEditing($this->referenceToId($employeeReference)));
        } catch (EmployeeNotFoundException $e) {
            $caughtException = $e;
        }
        Assert::assertNotNull($caughtException);
    }

    /**
     * @Then employee :employeeReference should have the following details:
     */
    public function assertEmployeeDetails(string $employeeReference, TableNode $table): void
    {
        $tableData = $table->getRowsHash();
        $data = $this->mapDataWithSelectedValues($tableData, $tableData['Associated shops'] ?? null);

        /** @var EditableEmployee $employeeDetails */
        $employeeDetails = $this->getCommandBus()->handle(new GetEmployeeForEditing($this->referenceToId($employeeReference)));
        if (isset($data['firstName'])) {
            Assert::assertEquals($data['firstName'], $employeeDetails->getFirstName()->getValue());
        }
        if (isset($data['lastName'])) {
            Assert::assertEquals($data['lastName'], $employeeDetails->getLastName()->getValue());
        }
        if (isset($data['email'])) {
            Assert::assertEquals($data['email'], $employeeDetails->getEmail()->getValue());
        }
        if (isset($data['profileId'])) {
            Assert::assertEquals($data['profileId'], $employeeDetails->getProfileId());
        }
        if (isset($data['shopAssociation'])) {
            Assert::assertEquals($data['shopAssociation'], $employeeDetails->getShopAssociation());
        }
        if (isset($data['active'])) {
            Assert::assertEquals($data['active'], $employeeDetails->isActive());
        }
        if (isset($data['languageId'])) {
            Assert::assertEquals($data['languageId'], $employeeDetails->getLanguageId());
        }
        if (isset($data['defaultPageId'])) {
            Assert::assertEquals($data['defaultPageId'], $employeeDetails->getDefaultPageId());
        }
        if (isset($data['password'])) {
            $this->checkPassword($data['password'], $employeeReference);
        }
    }

    /**
     * @When I toggle employee status for :employeeReference
     */
    public function toggleEmployeeStatus(string $employeeReference): void
    {
        $this->getCommandBus()->handle(new ToggleEmployeeStatusCommand($this->referenceToId($employeeReference)));
    }

    /**
     * @When /^I bulk (enable|disable) employees "(.+)"$/
     */
    public function bulkToggleEmployeeStatus(bool $enable, string $employeeReferences): void
    {
        $this->getCommandBus()->handle(new BulkUpdateEmployeeStatusCommand($this->referencesToIds($employeeReferences), $enable));
    }

    /**
     * @When I send password reset email to :employeeEmail and reference the token as :tokenReference
     */
    public function sendPasswordReset(string $employeeEmail, string $tokenReference): void
    {
        // Clean emails stored by maildev before running the command
        $mailDevClient = $this->getMailDevClient();
        $mailDevClient->deleteAllEmails();

        $resetUrl = $this->getCommandBus()->handle(new SendEmployeePasswordResetEmailCommand($employeeEmail));
        $resetUrl = str_replace('http://localhost', '', $resetUrl);
        /** @var UrlMatcherInterface $router */
        $router = CommonFeatureContext::getContainer()->get('router');
        $matchedRoute = $router->match($resetUrl);
        Assert::assertEquals('admin_reset_password', $matchedRoute['_route']);
        Assert::assertNotEmpty($matchedRoute['resetToken']);

        $emails = $mailDevClient->getAllEmails();
        Assert::assertCount(1, $emails);
        $resetEmail = $emails[0];
        Assert::assertStringContainsString('Your new password', $resetEmail['subject']);
        Assert::assertStringContainsString($resetUrl, $resetEmail['text']);
        Assert::assertStringContainsString($resetUrl, $resetEmail['html']);
        Assert::assertEquals($employeeEmail, $resetEmail['to'][0]['address']);

        $this->getSharedStorage()->set($tokenReference, $matchedRoute['resetToken']);
    }

    /**
     * @Then I can use token :tokenReference to set new password as :newPassword
     */
    public function resetEmployeePasswordByToken(string $tokenReference, string $newPassword): void
    {
        $this->getCommandBus()->handle(new ResetEmployeePasswordCommand(
            $this->getSharedStorage()->get($tokenReference),
            $newPassword,
        ));
    }

    /**
     * @param array $testCaseData
     * @param ?string $shopReference
     *
     * @return array
     */
    private function mapDataWithSelectedValues(array $testCaseData, ?string $shopReference = null): array
    {
        $data = [];

        if (isset($testCaseData['First name'])) {
            $data['firstName'] = $testCaseData['First name'];
        }
        if (isset($testCaseData['Last name'])) {
            $data['lastName'] = $testCaseData['Last name'];
        }
        if (isset($testCaseData['Email address'])) {
            $data['email'] = $testCaseData['Email address'];
        }
        if (isset($testCaseData['Password'])) {
            $data['plainPassword'] = $testCaseData['Password'];
        }

        if (isset($testCaseData['Default page'])) {
            /** @var TabChoiceProvider $tabChoiceProvider */
            $tabChoiceProvider = $this->getContainer()->get('prestashop.core.form.choice_provider.accessible_tab');
            $availableDefaultPageChoices = $tabChoiceProvider->getChoices();
            $pageId = null;
            foreach ($availableDefaultPageChoices as $pageName => $page) {
                if (is_array($page)) {
                    foreach ($page as $subPageName => $subPageId) {
                        if ($subPageName === $testCaseData['Default page']) {
                            $pageId = $subPageId;
                            break 2;
                        }
                    }
                } elseif ($pageName === $testCaseData['Default page']) {
                    $pageId = $page;
                    break;
                }
            }
            $data['defaultPageId'] = $pageId;
        }

        if (isset($testCaseData['Language'])) {
            /** @var LanguageChoiceProvider $languageChoiceProvider */
            $languageChoiceProvider = $this->getContainer()->get('prestashop.core.form.choice_provider.all_languages');
            $availableLanguages = $languageChoiceProvider->getChoices();
            $data['languageId'] = $availableLanguages[$testCaseData['Language']];
        }

        if (isset($testCaseData['Permission profile'])) {
            /** @var ProfileChoiceProvider $profileChoiceProvider */
            $profileChoiceProvider = $this->getContainer()->get('prestashop.core.form.choice_provider.profile');
            $availablePermissionProfiles = $profileChoiceProvider->getChoices();
            $data['profileId'] = $availablePermissionProfiles[$testCaseData['Permission profile']];
        }

        if (isset($testCaseData['Shop association'])) {
            $data['shopAssociation'] = $this->referencesToIds($testCaseData['Shop association']);
        } elseif (!empty($shopReference)) {
            /** @var array $shopAssociation */
            $shopAssociation = [
                SharedStorage::getStorage()->get($shopReference),
            ];
            $data['shopAssociation'] = $shopAssociation;
        }

        if (isset($testCaseData['Active'])) {
            $data['active'] = PrimitiveUtils::castStringBooleanIntoBoolean($testCaseData['Active']);
        }

        return $data;
    }

    private function checkPassword(string $password, string $employeeReference): void
    {
        $employeeRepository = $this->getContainer()->get(EmployeeRepository::class);
        $employee = $employeeRepository->find($this->referenceToId($employeeReference));

        // Similar validation as in CheckCredentialsListener
        /** @var PasswordHasherFactoryInterface $passwordFactory */
        $passwordFactory = $this->getContainer()->get('test_password_hasher_factory');
        Assert::assertTrue($passwordFactory->getPasswordHasher($employee)->verify($employee->getPassword(), $password));
    }
}
