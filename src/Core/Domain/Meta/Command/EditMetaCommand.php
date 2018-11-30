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
use PrestaShop\PrestaShop\Core\Domain\Meta\ValueObject\MetaId;

/**
 * Class EditMetaCommand
 */
class EditMetaCommand extends SaveMetaCommand
{
    /**
     * @var MetaId
     */
    private $metaId;

    /**
     * @var string
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
     * @param MetaId $metaId
     */
    public function __construct(MetaId $metaId)
    {
        $this->metaId = $metaId;
    }

    /**
     * @return MetaId
     */
    public function getMetaId()
    {
        return $this->metaId;
    }

    /**
     * @return string
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
        $this->validatePageName($pageName);
        $this->pageName = $pageName;

        return $this;
    }

    /**
     * @param string[] $pageTitle
     *
     * @return self
     */
    public function setPageTitle(array $pageTitle)
    {
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
     */
    public function setMetaDescription(array $metaDescription)
    {
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
     */
    public function setMetaKeywords(array $metaKeywords)
    {
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
    public function setRewriteUrl($rewriteUrl)
    {
        $this->rewriteUrl = $rewriteUrl;

        return $this;
    }
}
