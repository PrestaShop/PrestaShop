<?php
/**
 * 2007-2018 PrestaShop.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Meta\Command;

use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\MetaConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\MetaException;
use PrestaShop\PrestaShop\Core\Domain\Meta\ValueObject\MetaId;
use PrestaShop\PrestaShop\Core\Domain\Meta\ValueObject\PageName;

/**
 * Class EditMetaCommand
 */
class EditMetaCommand extends AbstractMetaCommand
{
    /**
     * @var MetaId
     */
    private $metaId;

    /**
     * @var PageName
     */
    private $pageName;

    /**
     * @var string[]
     */
    private $pageTitle;

    /**
     * @var string[]
     */
    private $metaDescription;

    /**
     * @var string[]
     */
    private $metaKeywords;

    /**
     * @var string[]
     */
    private $rewriteUrl;

    /**
     * @param int $metaId
     *
     * @throws MetaException
     */
    public function __construct($metaId)
    {
        $this->metaId = new MetaId($metaId);
    }

    /**
     * @return MetaId
     */
    public function getMetaId()
    {
        return $this->metaId;
    }

    /**
     * @return PageName
     */
    public function getPageName()
    {
        return $this->pageName;
    }

    /**
     * @param string $pageName
     *
     * @return self
     *
     * @throws MetaConstraintException
     */
    public function setPageName($pageName)
    {
        $this->pageName = new PageName($pageName);

        return $this;
    }

    /**
     * @param string[] $pageTitle
     *
     * @return self
     *
     * @throws MetaConstraintException
     */
    public function setPageTitle(array $pageTitle)
    {
        foreach ($pageTitle as $idLang => $title) {
            $this->assertNameMatchesRegexPattern($idLang, $title, MetaConstraintException::INVALID_PAGE_TITLE);
        }

        $this->pageTitle = $pageTitle;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getPageTitle()
    {
        return $this->pageTitle;
    }

    /**
     * @param string[] $metaDescription
     *
     * @return self
     *
     * @throws MetaConstraintException
     */
    public function setMetaDescription(array $metaDescription)
    {
        foreach ($metaDescription as $idLang => $description) {
            $this->assertNameMatchesRegexPattern($idLang, $description, MetaConstraintException::INVALID_META_DESCRIPTION);
        }

        $this->metaDescription = $metaDescription;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * @param string[] $metaKeywords
     *
     * @return self
     *
     * @throws MetaConstraintException
     */
    public function setMetaKeywords(array $metaKeywords)
    {
        foreach ($metaKeywords as $idLang => $metaKeyword) {
            $this->assertNameMatchesRegexPattern($idLang, $metaKeyword, MetaConstraintException::INVALID_META_KEYWORDS);
        }

        $this->metaKeywords = $metaKeywords;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }

    /**
     * @return string[]
     */
    public function getRewriteUrl()
    {
        return $this->rewriteUrl;
    }

    /**
     * @param string[] $rewriteUrl
     *
     * @return self
     */
    public function setRewriteUrl(array $rewriteUrl)
    {
        $this->rewriteUrl = $rewriteUrl;

        return $this;
    }
}
