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

/**
 * Class SaveMetaCommand is responsible for saving meta entities data.
 */
class SaveMetaCommand
{
    /**
     * @var string
     */
    private $pageName;

    /**
     * @var array
     */
    private $pageTitle;

    /**
     * @var array
     */
    private $metaDescription;

    /**
     * @var array
     */
    private $metaKeywords;

    /**
     * @var array
     */
    private $rewriteUrl;

    /**
     * SaveMetaCommand constructor.
     *
     * @param string $pageName
     * @param array $pageTitle
     * @param array $metaDescription
     * @param array $metaKeywords
     * @param array $rewriteUrl
     */
    public function __construct(
        $pageName,
        array $pageTitle,
        array $metaDescription,
        array $metaKeywords,
        array $rewriteUrl
    ) {
        $this->pageName = $pageName;
        $this->pageTitle = $pageTitle;
        $this->metaDescription = $metaDescription;
        $this->metaKeywords = $metaKeywords;
        $this->rewriteUrl = $rewriteUrl;
    }

    /**
     * @return string
     */
    public function getPageName()
    {
        return $this->pageName;
    }

    /**
     * @return array
     */
    public function getPageTitle()
    {
        return $this->pageTitle;
    }

    /**
     * @return array
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * @return array
     */
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }

    /**
     * @return array
     */
    public function getRewriteUrl()
    {
        return $this->rewriteUrl;
    }
}
