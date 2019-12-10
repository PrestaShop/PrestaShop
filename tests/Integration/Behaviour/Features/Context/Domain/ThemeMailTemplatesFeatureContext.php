<?php

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use PHPUnit_Framework_AssertionFailedError;
use PrestaShop\PrestaShop\Core\Domain\MailTemplate\Command\GenerateThemeMailTemplatesCommand;
use RuntimeException;
use Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class ThemeMailTemplatesFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I generate emails with the following properties:
     *
     * @param TableNode $table
     *
     * @throws RuntimeException
     * @throws ServiceCircularReferenceException
     * @throws ServiceNotFoundException
     */
    public function iGenerateEmailsWithTheFollowingProperties(TableNode $table)
    {
        $data = $this->extractFirstRowFromProperties($table);

        $overwriteTemplates = $data['overwrite_templates'] === '' ? false : $data['overwrite_templates'];
        $coreMailsFolder = $data['core_mails_folder'] === '' ? '' : $data['core_mails_folder'];
        $modulesMailFolder = $data['modules_mail_folder'] === '' ? '' : $data['modules_mail_folder'];

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
     *
     * @throws PHPUnit_Framework_AssertionFailedError
     */
    public function mailsFolderWithSubfolderExists(string $subFolder)
    {
        $mailsSubFolder = _PS_MAIL_DIR_ . $subFolder;
        assertTrue(is_dir($mailsSubFolder));
    }

    /**
     * duplicated ir Orders pull request
     *
     * @param TableNode $table
     *
     * @return array
     *
     * @throws RuntimeException
     */
    private function extractFirstRowFromProperties(TableNode $table): array
    {
        $hash = $table->getHash();
        if (count($hash) != 1) {
            throw new RuntimeException('Properties are invalid');
        }
        /** @var array $data */
        $data = $hash[0];

        return $data;
    }
}
