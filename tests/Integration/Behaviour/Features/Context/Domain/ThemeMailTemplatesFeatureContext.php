<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\MailTemplate\Command\GenerateThemeMailTemplatesCommand;

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
    public function generateEmailsWithTheFollowingDetails(TableNode $table): void
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
    private function mapGenerateThemeMailTemplatesData(array $testCaseData, array $data): array
    {
        $data['themeName'] = $testCaseData['Email theme'];

        // have not found locale choice provider
        $data['languageLocale'] = array_flip(self::LANGUAGES_MAP)[$data['Language']];

        $data['overwriteTemplates'] = isset($testCaseData['overwrite_templates']);
        $data['coreMailsFolder'] = isset($testCaseData['coreMailsFolder']);
        $data['modulesMailFolder'] = isset($testCaseData['modulesMailFolder']);

        return $data;
    }
}
