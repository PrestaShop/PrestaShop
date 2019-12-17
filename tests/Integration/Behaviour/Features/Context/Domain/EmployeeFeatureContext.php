<?php

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\AddEmployeeCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\EmployeeId;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\LanguageChoiceProvider;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\ProfileChoiceProvider;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\TabChoiceProvider;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

class EmployeeFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @Given I Add new employee :employeeReference to shop :shopReference with the following details:
     *
     * @param string $employeeReference
     * @param string $shopReference
     * @param TableNode $table
     */
    public function addNewEmployeeToShopWithTheFollowingDetails(string $employeeReference,
                                                                string $shopReference,
                                                                TableNode $table)
    {
        $testCaseData = $table->getRowsHash();

        $data = $this->mapDataForAddEmployeeHandler($testCaseData, $shopReference);

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
            $data['shopAssociation']
        ));

        SharedStorage::getStorage()->set($employeeReference, $employeeIdObject->getValue());
    }

    /**
     * @param array $testCaseData
     * @param string $shopReference
     *
     * @return array
     */
    private function mapDataForAddEmployeeHandler(array $testCaseData, string $shopReference): array
    {
        $data = [];

        $data['firstName'] = $testCaseData['First name'];
        $data['lastName'] = $testCaseData['Last name'];
        $data['email'] = $testCaseData['Email address'];
        $data['plainPassword'] = $testCaseData['Password'];

        /** @var TabChoiceProvider $tabChoiseProvider */
        $tabChoiseProvider = $this->getContainer()->get('prestashop.core.form.choice_provider.accessible_tab');
        $AvailableDefaultPageChoices = $tabChoiseProvider->getChoices();
        $data['defaultPageId'] = $AvailableDefaultPageChoices[$testCaseData['Default page']];

        /** @var LanguageChoiceProvider $languageChoiceProvider */
        $languageChoiceProvider = $this->getContainer()->get('prestashop.core.form.choice_provider.all_languages');
        $availableLanguages = $languageChoiceProvider->getChoices();
        $data['languageId'] = $availableLanguages[$testCaseData['Language']];

        /** @var ProfileChoiceProvider $profileChoiceProvider */
        $profileChoiceProvider = $this->getContainer()->get('prestashop.core.form.choice_provider.profile');
        $availablePermissionProfiles = $profileChoiceProvider->getChoices();
        $data['profileId'] = $availablePermissionProfiles[$testCaseData['Permission profile']];

        $isActive = $testCaseData['Active'] ? true : false;
        $data['active'] = $isActive;

        /** @var array $shopAssociation */
        $shopAssociation = [
            SharedStorage::getStorage()->get($shopReference)->id,
        ];
        $data['shopAssociation'] = $shopAssociation;

        return $data;
    }
}
