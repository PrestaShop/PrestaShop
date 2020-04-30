<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert as Assert;
use PrestaShop\PrestaShop\Core\Domain\MailTemplate\Command\GenerateThemeMailTemplatesCommand;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\ThemeByNameWithEmailsChoiceProvider;

class ThemeMailTemplatesFeatureContext extends AbstractDomainFeatureContext
{
    private const LANGUAGES_MAP = [
        'en' => 'English (English)',
    ];

    /**
     * @When I generate emails with the following details:
     *
     * @param TableNode $table
     */
    public function generateEmailsWithTheFollowingDetails(TableNode $table)
    {
        $testCaseData = $table->getRowsHash();

        $data = $this->mapGenerateThemeMailTemplatesData($testCaseData, $testCaseData);

        $this->getCommandBus()->handle(
            new GenerateThemeMailTemplatesCommand(
                $data['themeName'],
                $data['languageLocale'],
                $data['overwriteTemplates'],
                $data['coreMailsFolder'],
                $data['modulesMailFolder']
            )
        );
    }

    /**
     * @Then mails folder with sub folder :subFolder exists
     *
     * @param string $subFolder
     */
    public function mailsFolderWithSubFolderExists(string $subFolder)
    {
        $mailsSubFolder = _PS_MAIL_DIR_ . $subFolder;
        Assert::assertTrue(is_dir($mailsSubFolder));
    }

    /**
     * @param array $testCaseData
     * @param array $data
     *
     * @return array
     */
    private function mapGenerateThemeMailTemplatesData(array $testCaseData, array $data)
    {
        /** @var ThemeByNameWithEmailsChoiceProvider $themeWithEmailsChoiceProvider */
        $themeWithEmailsChoiceProvider =
            $this->getContainer()->get('prestashop.core.form.choice_provider.theme_by_name_with_emails');
        $AvailableLanguages = $themeWithEmailsChoiceProvider->getChoices();
        $data['themeName'] = $AvailableLanguages[$testCaseData['Email theme']];

        // have not found locale choice provider
        $data['languageLocale'] = array_flip(self::LANGUAGES_MAP)[$data['Language']];

        $data['overwriteTemplates'] = isset($testCaseData['overwrite_templates']);
        $data['coreMailsFolder'] = isset($testCaseData['coreMailsFolder']);
        $data['modulesMailFolder'] = isset($testCaseData['modulesMailFolder']);

        return $data;
    }
}
