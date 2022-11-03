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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use Exception;
use Meta;
use PrestaShop\PrestaShop\Core\Domain\Meta\Command\AddMetaCommand;
use PrestaShop\PrestaShop\Core\Domain\Meta\Command\EditMetaCommand;
use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\MetaConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\MetaNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Meta\Query\GetMetaForEditing;
use PrestaShop\PrestaShop\Core\Domain\Meta\Query\GetPagesForLayoutCustomization;
use PrestaShop\PrestaShop\Core\Domain\Meta\QueryResult\EditableMeta;
use PrestaShop\PrestaShop\Core\Domain\Meta\QueryResult\LayoutCustomizationPage;
use PrestaShop\PrestaShop\Core\Domain\Meta\ValueObject\MetaId;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use Tests\Integration\Behaviour\Features\Context\Util\DataTransfer;

class MetaFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When /^I add meta "([^"]*)" with specified properties$/
     */
    public function addMetaWithSpecifiedProperties($reference, TableNode $node)
    {
        $data = $node->getRowsHash();
        $data = $this->getWithDefaultLanguage($data);

        $command = new AddMetaCommand($data['pageName']);
        $command = DataTransfer::transferAttributesFromArrayToObject($data, $command);

        try {
            /** @var MetaId $metaId */
            $metaId = $this->getCommandBus()->handle($command);

            SharedStorage::getStorage()->set($reference, new Meta($metaId->getValue()));
        } catch (Exception $exception) {
            $this->setLastException($exception);
        }
    }

    /**
     * @When /^I get meta "([^"]*)" with specified properties$/
     */
    public function getMetaWithSpecifiedProperties($reference, TableNode $node)
    {
        $data = $node->getRowsHash();
        $queryCommand = new GetMetaForEditing((int) $data['metaId']);

        try {
            /** @var EditableMeta $editableMeta */
            $editableMeta = $this->getQueryBus()->handle($queryCommand);

            SharedStorage::getStorage()->set("editable_{$reference}", $editableMeta);
        } catch (Exception $exception) {
            $this->setLastException($exception);
        }
    }

    /**
     * @When /^I update meta "([^"]*)" with specified properties$/
     */
    public function updateMetaWithSpecifiedProperties($reference, TableNode $node)
    {
        $data = $node->getRowsHash();
        $data = $this->getWithDefaultLanguage($data);

        $command = new EditMetaCommand((int) $data['metaId']);
        $command = DataTransfer::transferAttributesFromArrayToObject($data, $command);

        try {
            $this->getCommandBus()->handle($command);

            SharedStorage::getStorage()->set($reference, new Meta((int) $data['metaId']));
        } catch (Exception $exception) {
            $this->setLastException($exception);
        }
    }

    /**
     * @When /^I add meta "([^"]*)" with specified properties without default language$/
     */
    public function addMetaWithSpecifiedPropertiesWithoutDefaultLanguage($reference, TableNode $node)
    {
        $data = $node->getRowsHash();
        $data = $this->getWithDefaultLanguage($data);

        $command = (new AddMetaCommand($data['pageName']))
            ->setLocalisedRewriteUrls([0 => $data['localisedRewriteUrls']])
        ;

        try {
            $this->getCommandBus()->handle($command);
        } catch (Exception $exception) {
            $this->setLastException($exception);
        }
    }

    /**
     * @When /^I update meta "([^"]*)" with specified properties without default language$/
     */
    public function updateMetaWithSpecifiedPropertiesWithoutDefaultLanguage($reference, TableNode $node)
    {
        $data = $node->getRowsHash();
        $data = $this->getWithDefaultLanguage($data);

        $command = (new EditMetaCommand((int) $data['metaId']))
            ->setLocalisedRewriteUrls([0 => $data['localisedRewriteUrls']])
        ;

        try {
            $this->getCommandBus()->handle($command);
        } catch (Exception $exception) {
            $this->setLastException($exception);
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
            'localisedPageTitles',
            'localisedPageTitle',
            'localisedMetaDescriptions',
            'localisedMetaDescription',
            'localisedMetaKeywords',
            'localisedRewriteUrls',
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
     * @Then /^I get pages for customization layout$/
     */
    public function getPagesForCustomizationLayout()
    {
        /** @var LayoutCustomizationPage[] $layoutCustomizationPages */
        $layoutCustomizationPages = $this->getQueryBus()->handle(
            new GetPagesForLayoutCustomization()
        );

        SharedStorage::getStorage()->set('meta_customization_pages', $layoutCustomizationPages);
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
            throw new RuntimeException(sprintf('For given field "%s" expected value "%s" did not matched given value "%s"', $field, $value, $actualValue));
        }
    }

    /**
     * @Then /^I should get error that url rewrite value is incorrect$/
     */
    public function assertItShouldGetErrorThatDefaultLanguageIsMissingForUrlRewrite()
    {
        $this->assertLastErrorIs(MetaConstraintException::class, MetaConstraintException::INVALID_URL_REWRITE);
    }

    /**
     * @Then /^I should get error that page name value is incorrect$/
     */
    public function assertItShouldGetErrorThatPageNameValueIsIncorrect()
    {
        $this->assertLastErrorIs(MetaConstraintException::class, MetaConstraintException::INVALID_PAGE_NAME);
    }

    /**
     * @Then /^I should get error that meta entity is not found$/
     */
    public function assertItShouldGetErrorThatMetaEntityIsNotFound()
    {
        $this->assertLastErrorIs(MetaNotFoundException::class);
    }

    /**
     * @Then /^page "([^"]*)" should exist in customization layout pages$/
     */
    public function assertPageShouldExistInCustomizationLayoutPages($pageName)
    {
        /** @var LayoutCustomizationPage[] $layoutCustomizationPages */
        $layoutCustomizationPages = SharedStorage::getStorage()->get('meta_customization_pages');

        $pageNames = array_map(
            static function (LayoutCustomizationPage $item) { return $item->getPage(); },
            $layoutCustomizationPages)
        ;

        if (!in_array($pageName, $pageNames, true)) {
            throw new RuntimeException(sprintf('Page name "%s" not found in available customization layout pages "%s"', $pageName, var_export($pageNames, true)));
        }
    }
}
