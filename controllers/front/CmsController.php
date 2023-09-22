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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
class CmsControllerCore extends FrontController
{
    public const CMS_CASE_PAGE = 1;
    public const CMS_CASE_CATEGORY = 2;

    /** @var string */
    public $php_self = 'cms';
    public $assignCase;

    /**
     * @var CMS|null
     */
    protected $cms;

    /**
     * @var CMSCategory|null
     */
    protected $cms_category;

    /** @var bool */
    public $ssl = false;

    public function canonicalRedirection(string $canonicalURL = '')
    {
        if (Validate::isLoadedObject($this->cms) && ($canonicalURL = $this->context->link->getCMSLink($this->cms, $this->cms->link_rewrite, $this->ssl))) {
            parent::canonicalRedirection($canonicalURL);
        } elseif (Validate::isLoadedObject($this->cms_category) && ($canonicalURL = $this->context->link->getCMSCategoryLink($this->cms_category))) {
            parent::canonicalRedirection($canonicalURL);
        }
    }

    /**
     * Initialize cms controller.
     *
     * @see FrontController::init()
     */
    public function init()
    {
        if ($id_cms = (int) Tools::getValue('id_cms')) {
            $this->cms = new CMS($id_cms, $this->context->language->id, $this->context->shop->id);
        } elseif ($id_cms_category = (int) Tools::getValue('id_cms_category')) {
            $this->cms_category = new CMSCategory($id_cms_category, $this->context->language->id, $this->context->shop->id);
        }

        if (Configuration::get('PS_SSL_ENABLED') && Tools::getValue('content_only') && $id_cms && Validate::isLoadedObject($this->cms)
            && in_array($id_cms, $this->getSSLCMSPageIds())) {
            $this->ssl = true;
        }

        parent::init();

        $this->canonicalRedirection();

        if (Validate::isLoadedObject($this->cms)) {
            $adtoken = Tools::getAdminToken('AdminCmsContent' . (int) Tab::getIdFromClassName('AdminCmsContent') . (int) Tools::getValue('id_employee'));
            if (!$this->cms->isAssociatedToShop() || !$this->cms->active && Tools::getValue('adtoken') != $adtoken) {
                $this->redirect_after = '404';
                $this->redirect();
            } else {
                $this->assignCase = self::CMS_CASE_PAGE;
            }
        } elseif (Validate::isLoadedObject($this->cms_category) && $this->cms_category->active) {
            $this->assignCase = self::CMS_CASE_CATEGORY;
        } else {
            $this->redirect_after = '404';
            $this->redirect();
        }
    }

    /**
     * Assign template vars related to page content.
     *
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        if ($this->assignCase == self::CMS_CASE_PAGE) {
            $cmsVar = $this->objectPresenter->present($this->cms);

            // Chained hook call - if multiple modules are hooked here, they will receive the result of the previous one as a parameter
            $filteredCmsContent = Hook::exec(
                'filterCmsContent',
                ['object' => $cmsVar],
                null,
                false,
                true,
                false,
                null,
                true
            );
            if (!empty($filteredCmsContent['object'])) {
                $cmsVar = $filteredCmsContent['object'];
            }

            $this->context->smarty->assign([
                'cms' => $cmsVar,
            ]);

            if ($this->cms->indexation == 0) {
                $this->context->smarty->assign('nobots', true);
            }

            $this->setTemplate(
                'cms/page',
                ['entity' => 'cms', 'id' => $this->cms->id]
            );
        } elseif ($this->assignCase == self::CMS_CASE_CATEGORY) {
            $cmsCategoryVar = $this->getTemplateVarCategoryCms();

            // Chained hook call - if multiple modules are hooked here, they will receive the result of the previous one as a parameter
            $filteredCmsCategoryContent = Hook::exec(
                'filterCmsCategoryContent',
                ['object' => $cmsCategoryVar],
                null,
                false,
                true,
                false,
                null,
                true
            );
            if (!empty($filteredCmsCategoryContent['object'])) {
                $cmsCategoryVar = $filteredCmsCategoryContent['object'];
            }

            $this->context->smarty->assign($cmsCategoryVar);
            $this->setTemplate(
                'cms/category',
                ['entity' => 'cms_category', 'id' => $this->cms_category->id]
            );
        }
        parent::initContent();
    }

    /**
     * Return an array of IDs of CMS pages, which shouldn't be forwared to their canonical URLs in SSL environment.
     * Required for pages which are shown in iframes.
     */
    protected function getSSLCMSPageIds()
    {
        return [(int) Configuration::get('PS_CONDITIONS_CMS_ID'), (int) Configuration::get('LEGAL_CMS_ID_REVOCATION')];
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        if ($this->assignCase == self::CMS_CASE_CATEGORY) {
            $cmsCategory = new CMSCategory($this->cms_category->id_cms_category);
        } else {
            $cmsCategory = new CMSCategory($this->cms->id_cms_category);
        }

        if ($cmsCategory->id_parent != 0) {
            foreach (array_reverse($cmsCategory->getParentsCategories()) as $category) {
                if ($category['active']) {
                    $cmsSubCategory = new CMSCategory($category['id_cms_category']);
                    $breadcrumb['links'][] = [
                        'title' => $cmsSubCategory->getName(),
                        'url' => $this->context->link->getCMSCategoryLink($cmsSubCategory),
                    ];
                }
            }
        }

        if ($this->assignCase == self::CMS_CASE_PAGE && $this->context->controller instanceof CmsControllerCore) {
            $breadcrumb['links'][] = [
                'title' => $this->context->controller->cms->meta_title,
                'url' => $this->context->link->getCMSLink($this->context->controller->cms),
            ];
        }

        return $breadcrumb;
    }

    /**
     * Initializes a set of commonly used variables related to the current page, available for use
     * in the template. @see FrontController::assignGeneralPurposeVariables for more information.
     *
     * @return array
     */
    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();

        if ($this->assignCase == self::CMS_CASE_CATEGORY) {
            $page['body_classes']['cms-id-' . $this->cms_category->id] = true;
        } else {
            $page['body_classes']['cms-id-' . $this->cms->id] = true;
            if (!$this->cms->indexation) {
                $page['meta']['robots'] = 'noindex';
            }
        }

        return $page;
    }

    public function getTemplateVarCategoryCms()
    {
        $categoryCms = [];

        $categoryCms['cms_category'] = $this->objectPresenter->present($this->cms_category);
        $categoryCms['sub_categories'] = [];
        $categoryCms['cms_pages'] = [];

        foreach ($this->cms_category->getSubCategories($this->context->language->id) as $subCategory) {
            $categoryCms['sub_categories'][$subCategory['id_cms_category']] = $subCategory;
            $categoryCms['sub_categories'][$subCategory['id_cms_category']]['link'] = $this->context->link->getCMSCategoryLink($subCategory['id_cms_category'], $subCategory['link_rewrite']);
        }

        foreach (CMS::getCMSPages($this->context->language->id, (int) $this->cms_category->id, true, (int) $this->context->shop->id) as $cmsPages) {
            $categoryCms['cms_pages'][$cmsPages['id_cms']] = $cmsPages;
            $categoryCms['cms_pages'][$cmsPages['id_cms']]['link'] = $this->context->link->getCMSLink($cmsPages['id_cms'], $cmsPages['link_rewrite']);
        }

        return $categoryCms;
    }

    /**
     * @return CMS|null
     */
    public function getCms()
    {
        return $this->cms;
    }

    /**
     * @return CMSCategory|null
     */
    public function getCmsCategory()
    {
        return $this->cms_category;
    }
}
