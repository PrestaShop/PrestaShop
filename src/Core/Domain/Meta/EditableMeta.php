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

namespace PrestaShop\PrestaShop\Core\Domain\Meta;

use PrestaShop\PrestaShop\Core\Domain\Meta\ValueObject\MetaId;

/**
 * Class EditableMeta is responsible for providing data for meta form.
 */
class EditableMeta
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
    private $urlRewrite;

    /**
     * EditableMeta constructor.
     *
     * @param MetaId $metaId
     * @param string $pageName
     * @param string[] $pageTitle
     * @param string[] $metaDescription
     * @param string[] $metaKeywords
     * @param string[] $urlRewrite
     */
    public function __construct(
        MetaId $metaId,
        $pageName,
        array $pageTitle,
        array $metaDescription,
        array $metaKeywords,
        array $urlRewrite
    ) {
        $this->metaId = $metaId;
        $this->pageName = $pageName;
        $this->pageTitle = $pageTitle;
        $this->metaDescription = $metaDescription;
        $this->metaKeywords = $metaKeywords;
        $this->urlRewrite = $urlRewrite;
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
     * @return string[]
     */
    public function getPageTitle()
    {
        return $this->pageTitle;
    }

    /**
     * @return string[]
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
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
    public function getUrlRewrite()
    {
        return $this->urlRewrite;
    }
}
