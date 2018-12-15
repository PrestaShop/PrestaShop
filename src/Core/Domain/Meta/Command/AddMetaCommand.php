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

/**
 * Class AddMetaCommand is responsible for saving meta entities data.
 */
class AddMetaCommand extends AbstractSaveMetaCommand
{
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
     * @param string $pageName
     *
     * @throws MetaConstraintException
     */
    public function __construct($pageName)
    {
        $this->validatePageName($pageName);

        $this->pageName = $pageName;
    }

    /**
     * @return string
     */
    public function getPageName()
    {
        return $this->pageName;
    }

    /**
     * @return string[]
     */
    public function getPageTitle()
    {
        return $this->pageTitle;
    }

    /**
     * @param string[] $pageTitle
     *
     * @return self
     *
     * @throws MetaConstraintException
     */
    public function setPageTitle($pageTitle)
    {
        foreach ($pageTitle as $idLang => $title) {
            $this->validateName($idLang, $title);
        }

        $this->pageTitle = $pageTitle;

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
     * @param string[] $metaDescription
     *
     * @return self
     *
     * @throws MetaConstraintException
     */
    public function setMetaDescription($metaDescription)
    {
        foreach ($metaDescription as $idLang => $description) {
            $this->validateName($idLang, $description);
        }

        $this->metaDescription = $metaDescription;

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
     * @param string[] $metaKeywords
     *
     * @return self
     *
     * @throws MetaConstraintException
     */
    public function setMetaKeywords($metaKeywords)
    {
        foreach ($metaKeywords as $idLang => $metaKeyword) {
            $this->validateName($idLang, $metaKeyword);
        }

        $this->metaKeywords = $metaKeywords;

        return $this;
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
