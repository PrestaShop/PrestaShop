<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\MailTemplate\Command\EditEmailBodyTemplateCommand;
use PrestaShop\PrestaShop\Core\Domain\MailTemplate\Exception\EmailTemplateConstraintException;
use PrestaShop\PrestaShop\Core\Domain\MailTemplate\Exception\EmailTemplateNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\MailTemplate\Query\GetEmailBodyTemplateForEditing;
use PrestaShop\PrestaShop\Core\Domain\MailTemplate\Query\GetEmailBodyTemplatesForListing;
use PrestaShop\PrestaShop\Core\Domain\MailTemplate\QueryResult\EditableEmailBodyTemplate;
use PrestaShop\PrestaShop\Core\Domain\MailTemplate\ValueObject\EmailTemplateName;
use PrestaShop\PrestaShop\Core\Domain\MailTemplate\ValueObject\EmailTemplateSource;

class EmailBodyTranslationFeatureContext extends AbstractDomainFeatureContext
{
    private ?array $listedTemplates = null;
    private ?EditableEmailBodyTemplate $editableTemplate = null;

    /**
     * @When I list email body templates for locale :locale
     */
    public function listEmailBodyTemplatesForLocale(string $locale): void
    {
        $this->listedTemplates = $this->getQueryBus()->handle(
            new GetEmailBodyTemplatesForListing($locale)
        );
    }

    /**
     * @Then I should see email body template :templateName with source :source
     */
    public function shouldSeeEmailBodyTemplateWithSource(string $templateName, string $source): void
    {
        Assert::assertNotNull($this->listedTemplates, 'Templates list is empty. Call listing step first.');

        $found = false;
        foreach ($this->listedTemplates as $template) {
            if ($template['template_name'] === $templateName && $template['source'] === $source) {
                $found = true;
                break;
            }
        }

        Assert::assertTrue($found, sprintf(
            'Email body template "%s" with source "%s" was not found in the listing.',
            $templateName,
            $source
        ));
    }

    /**
     * @Then I should see email body template :templateName with source :source and module :moduleName
     */
    public function shouldSeeEmailBodyTemplateWithSourceAndModule(string $templateName, string $source, string $moduleName): void
    {
        Assert::assertNotNull($this->listedTemplates, 'Templates list is empty. Call listing step first.');

        $found = false;
        foreach ($this->listedTemplates as $template) {
            if ($template['template_name'] === $templateName
                && $template['source'] === $source
                && $template['module_name'] === $moduleName
            ) {
                $found = true;
                break;
            }
        }

        Assert::assertTrue($found, sprintf(
            'Email body template "%s" with source "%s" and module "%s" was not found in the listing.',
            $templateName,
            $source,
            $moduleName
        ));
    }

    /**
     * @Then email body template :templateName should have HTML version
     */
    public function emailBodyTemplateShouldHaveHtmlVersion(string $templateName): void
    {
        Assert::assertNotNull($this->listedTemplates);

        foreach ($this->listedTemplates as $template) {
            if ($template['template_name'] === $templateName) {
                Assert::assertTrue($template['has_html'], sprintf(
                    'Email body template "%s" should have an HTML version.',
                    $templateName
                ));

                return;
            }
        }

        Assert::fail(sprintf('Email body template "%s" was not found.', $templateName));
    }

    /**
     * @Then email body template :templateName should have TXT version
     */
    public function emailBodyTemplateShouldHaveTxtVersion(string $templateName): void
    {
        Assert::assertNotNull($this->listedTemplates);

        foreach ($this->listedTemplates as $template) {
            if ($template['template_name'] === $templateName) {
                Assert::assertTrue($template['has_txt'], sprintf(
                    'Email body template "%s" should have a TXT version.',
                    $templateName
                ));

                return;
            }
        }

        Assert::fail(sprintf('Email body template "%s" was not found.', $templateName));
    }

    /**
     * @When I get email body template :templateName for locale :locale with source :source
     */
    public function getEmailBodyTemplateForEditing(string $templateName, string $locale, string $source): void
    {
        try {
            $this->editableTemplate = $this->getQueryBus()->handle(
                new GetEmailBodyTemplateForEditing($templateName, $locale, $source)
            );
        } catch (EmailTemplateNotFoundException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then I should get an editable email body template with non-empty HTML content
     */
    public function shouldGetEditableEmailBodyTemplateWithHtmlContent(): void
    {
        Assert::assertNotNull($this->editableTemplate);
        Assert::assertNotEmpty($this->editableTemplate->getHtmlContent());
    }

    /**
     * @Then I should get an editable email body template with non-empty TXT content
     */
    public function shouldGetEditableEmailBodyTemplateWithTxtContent(): void
    {
        Assert::assertNotNull($this->editableTemplate);
        Assert::assertNotEmpty($this->editableTemplate->getTxtContent());
    }

    /**
     * @When I edit email body template :templateName for locale :locale with source :source with:
     */
    public function editEmailBodyTemplate(string $templateName, string $locale, string $source, TableNode $table): void
    {
        $data = $table->getRowsHash();

        try {
            $this->getCommandBus()->handle(new EditEmailBodyTemplateCommand(
                $templateName,
                $locale,
                $source,
                '',
                $data['html_content'] ?? '',
                $data['txt_content'] ?? '',
            ));
        } catch (EmailTemplateConstraintException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then email body template :templateName for locale :locale with source :source HTML content should contain :expectedContent
     */
    public function emailBodyTemplateHtmlContentShouldContain(string $templateName, string $locale, string $source, string $expectedContent): void
    {
        /** @var EditableEmailBodyTemplate $template */
        $template = $this->getQueryBus()->handle(
            new GetEmailBodyTemplateForEditing($templateName, $locale, $source)
        );

        Assert::assertStringContainsString($expectedContent, $template->getHtmlContent());
    }

    /**
     * @Then email body template :templateName for locale :locale with source :source TXT content should contain :expectedContent
     */
    public function emailBodyTemplateTxtContentShouldContain(string $templateName, string $locale, string $source, string $expectedContent): void
    {
        /** @var EditableEmailBodyTemplate $template */
        $template = $this->getQueryBus()->handle(
            new GetEmailBodyTemplateForEditing($templateName, $locale, $source)
        );

        Assert::assertStringContainsString($expectedContent, $template->getTxtContent());
    }

    /**
     * @Then I should get an email template constraint error
     */
    public function shouldGetEmailTemplateConstraintError(): void
    {
        $this->assertLastErrorIs(EmailTemplateConstraintException::class);
    }

    /**
     * @Then I should get an email template not found error
     */
    public function shouldGetEmailTemplateNotFoundError(): void
    {
        $this->assertLastErrorIs(EmailTemplateNotFoundException::class);
    }

    /**
     * @When I create email template name with value :value
     */
    public function createEmailTemplateNameWithValue(string $value): void
    {
        try {
            new EmailTemplateName($value);
        } catch (EmailTemplateConstraintException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I create email template source with value :value
     */
    public function createEmailTemplateSourceWithValue(string $value): void
    {
        try {
            new EmailTemplateSource($value);
        } catch (EmailTemplateConstraintException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I create module email template source without module name
     */
    public function createModuleEmailTemplateSourceWithoutModuleName(): void
    {
        try {
            new EmailTemplateSource(EmailTemplateSource::SOURCE_MODULE, '');
        } catch (EmailTemplateConstraintException $e) {
            $this->setLastException($e);
        }
    }
}
