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
use CMS;
use Exception;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Command\AddCmsPageCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Command\BulkDeleteCmsPageCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Command\BulkDisableCmsPageCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Command\BulkEnableCmsPageCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Command\DeleteCmsPageCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Command\EditCmsPageCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Command\ToggleCmsPageStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CmsPageNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Query\GetCmsPageForEditing;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\ValueObject\CmsPageId;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Query\GetCmsPageCategoryForEditing;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\ValueObject\CmsPageCategoryId;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\CommonFeatureContext;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use Tests\Integration\Behaviour\Features\Context\Util\NoExceptionAlthoughExpectedException;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class CmsPageFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @var int
     */
    private $defaultLangId;

    /**
     * @var int
     */
    private $defaultShopId;

    /**
     * "When" steps perform actions, and some of them store the latest exception
     * in this variable so that "Then" action can check it
     *
     * @var mixed
     */
    private $latestException;

    public function __construct()
    {
        $configuration = CommonFeatureContext::getContainer()->get('prestashop.adapter.legacy.configuration');
        $this->defaultLangId = $configuration->get('PS_LANG_DEFAULT');
        $this->defaultShopId = $configuration->get('PS_SHOP_DEFAULT');
    }

    /**
     * @When I add new CMS page :cmsPageReference with following properties:
     */
    public function createCmsPage($cmsPageReference, TableNode $node)
    {
        $data = $node->getRowsHash();
        $this->createCmsPageUsingCommand($cmsPageReference, $data);
    }

    /**
     * @When I create CMS page :cmsPageReference with cms category id :id
     */
    public function createCmsPageWithProvidedCategoryId($cmsPageReference, $id)
    {
        $data = $this->getValidDataForCmsPageCreation();
        $data['id_cms_category'] = (int) $id;

        try {
            $this->createCmsPageUsingCommand($cmsPageReference, $data);
        } catch (Exception $e) {
            $this->latestException = $e;
        }
    }

    /**
     * @When I edit CMS page :cmsPageReference with following properties:
     */
    public function editCmsPage($cmsPageReference, TableNode $node)
    {
        $cmsId = (int) SharedStorage::getStorage()->get($cmsPageReference)->id;
        $command = new EditCmsPageCommand($cmsId);
        $data = $node->getRowsHash();

        if (isset($data['meta_title'])) {
            $command->setLocalizedTitle([$this->defaultLangId => $data['meta_title']]);
        }
        if (isset($data['head_seo_title'])) {
            $command->setLocalizedMetaTitle([$this->defaultLangId => $data['head_seo_title']]);
        }
        if (isset($data['meta_description'])) {
            $command->setLocalizedMetaDescription([$this->defaultLangId => $data['meta_description']]);
        }
        if (isset($data['meta_keywords'])) {
            $command->setLocalizedMetaKeyword([$this->defaultLangId => $data['meta_keywords']]);
        }
        if (isset($data['link_rewrite'])) {
            $command->setLocalizedFriendlyUrl([$this->defaultLangId => $data['link_rewrite']]);
        }
        if (isset($data['content'])) {
            $command->setLocalizedContent([$this->defaultLangId => $data['content']]);
        }
        if (isset($data['indexation'])) {
            $command->setIsIndexedForSearch(PrimitiveUtils::castStringBooleanIntoBoolean($data['indexation']));
        }
        if (isset($data['active'])) {
            $command->setIsDisplayed(PrimitiveUtils::castStringBooleanIntoBoolean($data['active']));
        }
        if (isset($data['id_cms_category'])) {
            $command->setCmsPageCategoryId((int) $data['id_cms_category']);
        }

        try {
            $this->getCommandBus()->handle($command);
            SharedStorage::getStorage()->set($cmsPageReference, new CMS($cmsId));
        } catch (Exception $e) {
            $this->latestException = $e;
        }
    }

    /**
     * @When /^I (enable|disable) CMS pages: "(.*)" in bulk action?$/
     */
    public function bulkToggleDisplayStatus($action, $cmsPageReferences)
    {
        $idsByReferences = [];

        foreach (PrimitiveUtils::castStringArrayIntoArray($cmsPageReferences) as $cmsPageReference) {
            $cms = SharedStorage::getStorage()->get($cmsPageReference);
            $idsByReferences[$cmsPageReference] = (int) $cms->id;
        }

        if ('enable' === $action) {
            $this->getQueryBus()->handle(new BulkEnableCmsPageCommand($idsByReferences));
        } else {
            $this->getQueryBus()->handle(new BulkDisableCmsPageCommand($idsByReferences));
        }

        foreach ($idsByReferences as $cmsPageReference => $id) {
            SharedStorage::getStorage()->set($cmsPageReference, new CMS($id));
        }
    }

    /**
     * @When I toggle CMS page :cmsPageReference display status
     */
    public function toggleDisplayStatus($cmsPageReference)
    {
        /** @var CMS $cms */
        $cms = SharedStorage::getStorage()->get($cmsPageReference);
        $cmsId = (int) $cms->id;
        $this->getCommandBus()->handle(new ToggleCmsPageStatusCommand($cmsId));

        SharedStorage::getStorage()->set($cmsPageReference, new CMS($cmsId));
    }

    /**
     * @Given CMS pages: :cmsPageReferences exists
     */
    public function createMultipleCmsPages($cmsPageReferences)
    {
        $cmsPageReferences = explode(',', $cmsPageReferences);

        foreach ($cmsPageReferences as $ref) {
            $data = $this->getValidDataForCmsPageCreation();
            $data['active'] = false;
            $this->createCmsPageUsingCommand($ref, $data);
        }
    }

    /**
     * @When I delete CMS page :cmsPageReference
     */
    public function deleteCmsPage($cmsPageReference)
    {
        $cmsPageId = (int) SharedStorage::getStorage()->get($cmsPageReference)->id;

        $this->getCommandBus()->handle(new DeleteCmsPageCommand($cmsPageId));
    }

    /**
     * @When I delete CMS pages: :cmsPageReferences using bulk action
     */
    public function bulkDeleteCmsPages($cmsPageReferences)
    {
        $idsByReferences = [];

        foreach (PrimitiveUtils::castStringArrayIntoArray($cmsPageReferences) as $cmsPageReference) {
            $cms = SharedStorage::getStorage()->get($cmsPageReference);
            $idsByReferences[$cmsPageReference] = (int) $cms->id;
        }
        $this->getCommandBus()->handle(new BulkDeleteCmsPageCommand($idsByReferences));
    }

    /**
     * @Then CMS pages: :cmsPageReferences should be deleted
     */
    public function assertCmsPagesAreDeleted($cmsPageReferences)
    {
        foreach (PrimitiveUtils::castStringArrayIntoArray($cmsPageReferences) as $cmsPageReference) {
            $this->assertCmsPageIsDeleted($cmsPageReference);
        }
    }

    /**
     * @Then CMS page :cmsPageReference should be deleted
     */
    public function assertCmsPageIsDeleted($cmsPageReference)
    {
        $cmsPageId = (int) SharedStorage::getStorage()->get($cmsPageReference)->id;

        try {
            $this->getQueryBus()->handle(new GetCmsPageForEditing($cmsPageId));
            throw new NoExceptionAlthoughExpectedException(sprintf('CMS page "%s" was found, but it was expected to be deleted', $cmsPageReference));
        } catch (CmsPageNotFoundException $e) {
            SharedStorage::getStorage()->clear($cmsPageReference);
        }
    }

    /**
     * @Then /^CMS page "(.*)" indexation for search engines should be (enabled|disabled)?$/
     */
    public function assertIndexationStatus($cmsPageReference, $status)
    {
        /** @var CMS $cmsPage */
        $cmsPage = SharedStorage::getStorage()->get($cmsPageReference);
        $isEnabled = $status === 'enabled';
        if ($isEnabled !== (bool) $cmsPage->indexation) {
            throw new RuntimeException(sprintf('Cms page "%s" indexation is %s, but it was expected to be %s', $cmsPageReference, $cmsPage->indexation ? 'enabled' : 'disabled', $status));
        }
    }

    /**
     * @Then /^CMS pages: "(.*)" should be (displayed|not displayed)?$/
     */
    public function assertMultipleCmsPagesDisplayStatus($cmsPageReferences, $status)
    {
        foreach (PrimitiveUtils::castStringArrayIntoArray($cmsPageReferences) as $cmsPageReference) {
            $this->assertDisplayStatus($cmsPageReference, $status);
        }
    }

    /**
     * @Then /^CMS page "(.*)" should be (displayed|not displayed)?$/
     */
    public function assertDisplayStatus($cmsPageReference, $status)
    {
        /** @var CMS $cmsPage */
        $cmsPage = SharedStorage::getStorage()->get($cmsPageReference);
        $isEnabled = $status === 'displayed';
        if ($isEnabled !== (bool) $cmsPage->active) {
            throw new RuntimeException(sprintf('Cms page "%s" is %s, but it was expected to be %s', $cmsPageReference, $cmsPage->active ? 'displayed' : 'not displayed', $status));
        }
    }

    /**
     * @Then /^CMS page "(.+)" "(.+)" in default language should be '([^']+)'$/
     */
    public function assertFieldValue($cmsPageReference, $field, $value)
    {
        /** @var CMS $cmsPage */
        $cmsPage = SharedStorage::getStorage()->get($cmsPageReference);
        if ($cmsPage->$field[$this->defaultLangId] !== $value) {
            throw new RuntimeException(sprintf('Cms page "%s" has "%s" %s, but "%s" was expected.', $cmsPageReference, $cmsPage->$field[$this->defaultLangId], $field, $value));
        }
    }

    /**
     * @Then CMS page :cmsPageReference :field field in default language should be empty
     */
    public function assertFieldIsEmpty($cmsPageReference, $field)
    {
        /** @var CMS $cmsPage */
        $cmsPage = SharedStorage::getStorage()->get($cmsPageReference);
        if ($cmsPage->$field[$this->defaultLangId] !== '') {
            throw new RuntimeException(sprintf('Cms page "%s" has "%s" %s, but it was expected to be empty', $cmsPageReference, $cmsPage->$field[$this->defaultLangId], $field));
        }
    }

    /**
     * @Then /^I should get error message '(.+)'$/
     */
    public function assertExceptionWasThrown($message)
    {
        if ($this->latestException instanceof Exception) {
            if ($this->latestException->getMessage() !== $message) {
                throw new RuntimeException(sprintf('Got error message "%s", but expected %s', $this->latestException->getMessage(), $message));
            }

            return true;
        }

        throw new NoExceptionAlthoughExpectedException('No exception was thrown in latest result');
    }

    /**
     * @Given cms category with id :id does not exist
     */
    public function assertCmsCategoryWithIdDoesNotExist($id)
    {
        try {
            $query = new GetCmsPageCategoryForEditing((int) $id);
            $this->getQueryBus()->handle($query);

            throw new NoExceptionAlthoughExpectedException(sprintf('Cms category with id "%s" expected to not exist, but it exists', $id));
        } catch (CmsPageCategoryNotFoundException $e) {
        }
    }

    /**
     * @param string $cmsPageReference
     * @param array $data
     */
    private function createCmsPageUsingCommand($cmsPageReference, array $data)
    {
        $command = new AddCmsPageCommand(
            (int) $data['id_cms_category'],
            [$this->defaultLangId => $data['meta_title']],
            [$this->defaultLangId => $data['head_seo_title']],
            [$this->defaultLangId => $data['meta_description']],
            [$this->defaultLangId => $data['meta_keywords']],
            [$this->defaultLangId => $data['link_rewrite']],
            [$this->defaultLangId => $data['content']],
            PrimitiveUtils::castStringBooleanIntoBoolean($data['indexation']),
            PrimitiveUtils::castStringBooleanIntoBoolean($data['active']),
            [$this->defaultShopId]
        );

        /** @var CmsPageId $cmsPageId */
        $cmsPageId = $this->getCommandBus()->handle($command);

        SharedStorage::getStorage()->set($cmsPageReference, new \CMS($cmsPageId->getValue()));
    }

    /**
     * Provides reusable valid data for cms creation.
     *
     * @return array
     */
    private function getValidDataForCmsPageCreation()
    {
        return [
            'id_cms_category' => CmsPageCategoryId::ROOT_CMS_PAGE_CATEGORY_ID,
            'meta_title' => 'Special delivery options',
            'head_seo_title' => 'delivery options',
            'meta_description' => 'Our special delivery options',
            'meta_keywords' => 'delivery,configure,special',
            'link_rewrite' => 'delivery-options',
            'content' => '<div> <h5> Delivery <img src="../delivery/options.jpg" alt="" /></h5> </div>',
            'indexation' => true,
            'active' => true,
        ];
    }
}
