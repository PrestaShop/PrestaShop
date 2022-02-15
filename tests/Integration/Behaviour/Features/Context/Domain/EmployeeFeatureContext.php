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
    public function addNewEmployeeToShopWithTheFollowingDetails(
        string $employeeReference,
        string $shopReference,
        TableNode $table
    ) {
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
            $data['shopAssociation'],
            false, // has enable gravatar
            1, // Minimum password length, dummy data
            72, // Maximum password length, dummy data
            1 // Minimum password score, dummy data
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

        // todo: use transformer
        $data['active'] = (bool) $testCaseData['Active'];

        /** @var array $shopAssociation */
        $shopAssociation = [
            SharedStorage::getStorage()->get($shopReference),
        ];
        $data['shopAssociation'] = $shopAssociation;

        return $data;
    }
}
