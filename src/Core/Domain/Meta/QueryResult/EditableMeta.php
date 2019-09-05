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

namespace PrestaShop\PrestaShop\Core\Domain\Meta\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Meta\Exception;
use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\MetaException;
use PrestaShop\PrestaShop\Core\Domain\Meta\ValueObject\MetaId;
use PrestaShop\PrestaShop\Core\Domain\Meta\ValueObject\Name;

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
     * @var Name
     */
    private $pageName;

    /**
     * @var string[]
     */
    private $localisedPageTitles;

    /**
     * @var string[]
     */
    private $localisedMetaDescriptions;

    /**
     * @var string[]
     */
    private $localisedMetaKeywords;

    /**
     * @var string[]
     */
    private $localisedUrlRewrites;

    /**
     * EditableMeta constructor.
     *
     * @param int $metaId
     * @param string $pageName
     * @param string[] $localisedPageTitles
     * @param string[] $localisedMetaDescriptions
     * @param string[] $localisedMetaKeywords
     * @param string[] $localisedUrlRewrites
     *
     * @throws Exception\MetaConstraintException
     * @throws MetaException
     */
    public function __construct(
        $metaId,
        $pageName,
        array $localisedPageTitles,
        array $localisedMetaDescriptions,
        array $localisedMetaKeywords,
        array $localisedUrlRewrites
    ) {
        $this->metaId = new MetaId($metaId);
        $this->pageName = new Name($pageName);
        $this->localisedPageTitles = $localisedPageTitles;
        $this->localisedMetaDescriptions = $localisedMetaDescriptions;
        $this->localisedMetaKeywords = $localisedMetaKeywords;
        $this->localisedUrlRewrites = $localisedUrlRewrites;
    }

    /**
     * @return MetaId
     */
    public function getMetaId()
    {
        return $this->metaId;
    }

    /**
     * @return Name
     */
    public function getPageName()
    {
        return $this->pageName;
    }

    /**
     * @return string[]
     */
    public function getLocalisedPageTitles()
    {
        return $this->localisedPageTitles;
    }

    /**
     * @return string[]
     */
    public function getLocalisedMetaDescriptions()
    {
        return $this->localisedMetaDescriptions;
    }

    /**
     * @return string[]
     */
    public function getLocalisedMetaKeywords()
    {
        return $this->localisedMetaKeywords;
    }

    /**
     * @return string[]
     */
    public function getLocalisedUrlRewrites()
    {
        return $this->localisedUrlRewrites;
    }
}
