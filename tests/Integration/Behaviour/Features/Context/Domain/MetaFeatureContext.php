<?php

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use Exception;
use Meta;
use PrestaShop\PrestaShop\Core\Domain\Meta\Command\AddMetaCommand;
use PrestaShop\PrestaShop\Core\Domain\Meta\Command\EditMetaCommand;
use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\MetaConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Meta\ValueObject\MetaId;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

class MetaFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @Given /^I specify following properties for new meta "([^"]*)":$/
     */
    public function specifyFollowingPropertiesForNewMeta($reference, TableNode $node)
    {
        $data = $node->getRowsHash();
        SharedStorage::getStorage()->set(sprintf('%s_properties', $reference), $data);
    }

    /**
     * @When /^I add meta "([^"]*)" with specified properties$/
     */
    public function addMetaWithSpecifiedProperties($reference)
    {
        $propertiesKey = sprintf('%s_properties', $reference);

        $data = SharedStorage::getStorage()->get($propertiesKey);
        $data = $this->getWithDefaultLanguage($data);

        $command = (new AddMetaCommand($data['page_name']))
            ->setLocalisedPageTitle(
                isset($data['localized_page_title']) ? $data['localized_page_title'] : []
            )
            ->setLocalisedMetaDescription(
                isset($data['localized_meta_description']) ? $data['localized_meta_description'] : []
            )
            ->setLocalisedMetaKeywords(
                isset($data['localized_meta_keywords']) ? $data['localized_meta_keywords'] : []
            )
            ->setLocalisedRewriteUrls(
                isset($data['localized_rewrite_urls']) ? $data['localized_rewrite_urls'] : []
            )
        ;

        try {
            /** @var MetaId $metaId */
            $metaId = $this->getCommandBus()->handle($command);

            SharedStorage::getStorage()->set($reference, new Meta($metaId->getValue()));
        } catch (Exception $exception) {
            $this->lastException = $exception;
            $this->lastErrorCode = $exception->getCode();
        }
    }

    /**
     * @When /^I update meta "([^"]*)" with specified properties$/
     */
    public function updateMetaWithSpecifiedProperties($reference)
    {
        $propertiesKey = sprintf('%s_properties', $reference);

        $data = SharedStorage::getStorage()->get($propertiesKey);
        $data = $this->getWithDefaultLanguage($data);

        $command = (new EditMetaCommand((int) $data['meta_id']))
            ->setLocalisedPageTitles(isset($data['localized_page_title']) ? $data['localized_page_title'] : [])
            ->setLocalisedMetaDescriptions(isset($data['localized_meta_description']) ? $data['localized_meta_description'] : [])
            ->setLocalisedMetaKeywords(isset($data['localized_meta_keywords']) ? $data['localized_meta_keywords'] : [])
            ->setLocalisedRewriteUrls(isset($data['localized_rewrite_urls']) ? $data['localized_rewrite_urls'] : [])
        ;

        if (isset($data['page_name'])) {
            $command->setPageName($data['page_name']);
        }

        try {
            $this->getCommandBus()->handle($command);

            SharedStorage::getStorage()->set($reference, new Meta((int) $data['meta_id']));
        } catch (Exception $exception) {
            $this->lastException = $exception;
            $this->lastErrorCode = $exception->getCode();
        }
    }

    /**
     * @When /^I add meta "([^"]*)" with specified properties without default language$/
     */
    public function addMetaWithSpecifiedPropertiesWithoutDefaultLanguage($reference)
    {
        $propertiesKey = sprintf('%s_properties', $reference);

        $data = SharedStorage::getStorage()->get($propertiesKey);
        $data = $this->getWithDefaultLanguage($data);

        $command = (new AddMetaCommand($data['page_name']))
            ->setLocalisedRewriteUrls([0 => $data['localized_rewrite_urls']])
        ;

        try {
            /** @var MetaId $metaId */
            $metaId = $this->getCommandBus()->handle($command);
        } catch (Exception $exception) {
            $this->lastException = $exception;
            $this->lastErrorCode = $exception->getCode();
        }
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function getWithDefaultLanguage(array $data)
    {
        $defaultLanguageId = SharedStorage::getStorage()->get('default_language_id');

        $languageFields = [
            'localized_page_title',
            'localized_meta_description',
            'localized_meta_keywords',
            'localized_rewrite_urls',
        ];

        foreach ($data as $key => $item) {
            if (!in_array($key, $languageFields, true)) {
                continue;
            }

            $data[$key] = [
                $defaultLanguageId => $item,
            ];
        }

        return $data;
    }

    /**
     * @Then /^I should get error that url rewrite value is incorrect$/
     */
    public function assertItShouldGetErrorThatDefaultLanguageIsMissingForUrlRewrite()
    {
        $this->assertLastErrorIs(MetaConstraintException::class);
        $this->assertLastErrorCodeIs(MetaConstraintException::INVALID_URL_REWRITE);
    }

    /**
     * @Then /^I should get error that page name value is incorrect$/
     */
    public function assertItShouldGetErrorThatPageNameValueIsIncorrect()
    {
        $this->assertLastErrorIs(MetaConstraintException::class);
        $this->assertLastErrorCodeIs(MetaConstraintException::INVALID_PAGE_NAME);
    }
}
