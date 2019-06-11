<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Command\AddCmsPageCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Command\EditCmsPageCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\ValueObject\CmsPageId;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\ValueObject\CmsPageCategoryId;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

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

    public function __construct()
    {
        $this->defaultLangId = \Configuration::get('PS_LANG_DEFAULT');
        $this->defaultShopId = \Configuration::get('PS_SHOP_DEFAULT');
    }

    /**
     * @When I add new cms page :reference with following properties:
     */
    public function createCmsPage($reference, TableNode $node)
    {
        $data = $node->getRowsHash();
        $command = new AddCmsPageCommand(
            CmsPageCategoryId::ROOT_CMS_PAGE_CATEGORY_ID,
            [$this->defaultLangId => $data['meta_title']],
            [$this->defaultLangId => $data['head_seo_title']],
            [$this->defaultLangId => $data['meta_description']],
            [$this->defaultLangId => $data['meta_keywords']],
            [$this->defaultLangId => $data['link_rewrite']],
            [$this->defaultLangId => $data['content']],
            (bool) $data['indexation'],
            (bool) $data['active'],
            [$this->defaultShopId]
        );

        /** @var CmsPageId $cmsPageId */
        $cmsPageId = $this->getCommandBus()->handle($command);

        SharedStorage::getStorage()->set($reference, new \CMS($cmsPageId->getValue()));
    }

    /**
     * @When I edit cms page :reference with following properties:
     */
    public function editCmsPage($reference, TableNode $node)
    {
        $cmsId = (int) SharedStorage::getStorage()->get($reference)->id;
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
            $command->setIsIndexedForSearch((bool) $data['indexation']);
        }
        if (isset($data['active'])) {
            $command->setIsDisplayed((bool) $data['active']);
        }

        $this->getCommandBus()->handle($command);
        SharedStorage::getStorage()->set($reference, new \CMS($cmsId));
    }

    /**
     * @Then /^cms page "(.*)" indexation for search engines should be (enabled|disabled)?$/
     */
    public function assertIndexation($reference, $status)
    {
        /** @var \CMS $cmsPage */
        $cmsPage = SharedStorage::getStorage()->get($reference);
        $statusBool = $status === 'enabled' ? true : false;
        if ($statusBool !== (bool) $cmsPage->indexation) {
            throw new RuntimeException(sprintf(
                'Cms page "%s" indexation is %s, but it was expected to be %s',
                $reference,
                $cmsPage->indexation ? 'enabled' : 'disabled',
                $status
            ));
        }
    }

    /**
     * @Then /^cms page "(.*)" should be (displayed|not displayed)?$/
     */
    public function assertIsActive($reference, $status)
    {
        /** @var \CMS $cmsPage */
        $cmsPage = SharedStorage::getStorage()->get($reference);
        $statusBool = $status === 'displayed' ? true : false;
        if ($statusBool !== (bool) $cmsPage->active) {
            throw new RuntimeException(sprintf(
                'Cms page "%s" is %s, but it was expected to be %s',
                $reference,
                $cmsPage->active ? 'displayed' : 'not displayed',
                $status
            ));
        }
    }

    /**
     * @Then cms page :reference :field in default language should be :value
     */
    public function assertFieldValue($reference, $field, $value)
    {
        /** @var \CMS $cmsPage */
        $cmsPage = SharedStorage::getStorage()->get($reference);
        if ($cmsPage->$field[$this->defaultLangId] !== $value) {
            throw new RuntimeException(sprintf(
                'Cms page "%s" has "%s" %s, but "%s" was expected.',
                $reference,
                $cmsPage->$field[$this->defaultLangId],
                $field,
                $value
            ));
        }
    }

    /**
     * @Then cms page :reference :field field in default language should be empty
     */
    public function assertFieldIsEmpty($reference, $field)
    {
        /** @var \CMS $cmsPage */
        $cmsPage = SharedStorage::getStorage()->get($reference);
        if ($cmsPage->$field[$this->defaultLangId] !== '') {
            throw new RuntimeException(sprintf(
                'Cms page "%s" has "%s" %s, but it was expected to be empty',
                $reference,
                $cmsPage->$field[$this->defaultLangId],
                $field
            ));
        }
    }
}
