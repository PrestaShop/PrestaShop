<?php

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use PHPUnit_Framework_Assert;
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
        PHPUnit_Framework_Assert::assertTrue(is_dir($mailsSubFolder));
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

        $data['overwriteTemplates'] = isset($testCaseData['overwrite_templates']) ?: false;
        $data['coreMailsFolder'] = isset($testCaseData['coreMailsFolder']) ?: '';
        $data['modulesMailFolder'] = isset($testCaseData['modulesMailFolder']) ?: '';

        return $data;
    }
}
