<?php

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use PHPUnit_Framework_Assert;
use PrestaShop\PrestaShop\Core\Domain\MailTemplate\Command\GenerateThemeMailTemplatesCommand;
use Tests\Integration\Behaviour\Features\Context\Util\BehatTableNodeUtils;

class ThemeMailTemplatesFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I generate emails with the following details:
     *
     * @param TableNode $table
     */
    public function generateEmailsWithTheFollowingDetails(TableNode $table)
    {
        $data = BehatTableNodeUtils::extractFirstRowFromProperties($table);

        $overwriteTemplates = $data['overwrite_templates'] !== '' ?: false;
        $coreMailsFolder = $data['core_mails_folder'] !== '' ?: '';
        $modulesMailFolder = $data['modules_mail_folder'] !== '' ?: '';

        $this->getCommandBus()->handle(
            new GenerateThemeMailTemplatesCommand(
                $data['theme_name'],
                $data['language'],
                $overwriteTemplates,
                $coreMailsFolder,
                $modulesMailFolder
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
}
