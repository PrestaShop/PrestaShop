<?php

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use Exception;
use Meta;
use PrestaShop\PrestaShop\Core\Domain\Meta\Command\AddMetaCommand;
use PrestaShop\PrestaShop\Core\Domain\Meta\Command\EditMetaCommand;
use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\MetaConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\MetaNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Meta\Query\GetMetaForEditing;
use PrestaShop\PrestaShop\Core\Domain\Meta\QueryResult\EditableMeta;
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
     * @When /^I get meta "([^"]*)" with specified properties$/
     */
    public function getMetaWithSpecifiedProperties($reference)
    {
        $propertiesKey = sprintf('%s_properties', $reference);
        $data = SharedStorage::getStorage()->get($propertiesKey);

        $queryCommand = new GetMetaForEditing((int) $data['meta_id']);

        try {
            /** @var EditableMeta $editableMeta */
            $editableMeta = $this->getQueryBus()->handle($queryCommand);

            SharedStorage::getStorage()->set("editable_{$reference}", $editableMeta);
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
            $this->getCommandBus()->handle($command);
        } catch (Exception $exception) {
            $this->lastException = $exception;
            $this->lastErrorCode = $exception->getCode();
        }
    }

    /**
     * @When /^I update meta "([^"]*)" with specified properties without default language$/
     */
    public function updateMetaWithSpecifiedPropertiesWithoutDefaultLanguage($reference)
    {
        $propertiesKey = sprintf('%s_properties', $reference);

        $data = SharedStorage::getStorage()->get($propertiesKey);
        $data = $this->getWithDefaultLanguage($data);

        $command = (new EditMetaCommand((int) $data['meta_id']))
            ->setLocalisedRewriteUrls([0 => $data['localized_rewrite_urls']])
        ;

        try {
            $this->getCommandBus()->handle($command);
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
     * @Then /^meta "([^"]*)" editable form field "([^"]*)" should be equal to "([^"]*)"$/
     */
    public function assertMetaEditableFormFieldShouldBeEqualTo($reference, $field, $value)
    {
        /** @var EditableMeta $editableMeta */
        $editableMeta = SharedStorage::getStorage()->get("editable_{$reference}");
        $defaultLanguageId = SharedStorage::getStorage()->get('default_language_id');
        $actualValue = null;

        switch ($field) {
            case 'page':
                $actualValue = $editableMeta->getPageName()->getValue();

                break;
            case 'title':
                $actualValue = $editableMeta->getLocalisedPageTitles()[$defaultLanguageId];

                break;
            case 'description':
                $actualValue = $editableMeta->getLocalisedMetaDescriptions()[$defaultLanguageId];

                break;
            case 'keywords':
                $actualValue = $editableMeta->getLocalisedMetaKeywords()[$defaultLanguageId];

                break;
            case 'url_rewrite':
                $actualValue = $editableMeta->getLocalisedUrlRewrites()[$defaultLanguageId];

                break;
        }

        if ($actualValue !== $value) {
            throw new RuntimeException(
                sprintf(
                    'For given field "%s" expected value "%s" did not matched given value "%s"',
                    $field,
                    $value,
                    $actualValue
                )
            );
        }
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

    /**
     * @Then /^I should get error that meta entity is not found$/
     */
    public function assertItShouldGetErrorThatMetaEntityIsNotFound()
    {
        $this->assertLastErrorIs(MetaNotFoundException::class);
    }
}
