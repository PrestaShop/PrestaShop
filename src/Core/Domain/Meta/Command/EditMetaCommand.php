<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Meta\Command;

use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\MetaConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\MetaException;
use PrestaShop\PrestaShop\Core\Domain\Meta\ValueObject\MetaId;
use PrestaShop\PrestaShop\Core\Domain\Meta\ValueObject\Name;

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
    private $localisedRewriteUrls;

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
     * @return Name
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
        $this->pageName = new Name($pageName);

        return $this;
    }

    /**
     * @param string[] $localisedPageTitles
     *
     * @return self
     *
     * @throws MetaConstraintException
     */
    public function setLocalisedPageTitles(array $localisedPageTitles)
    {
        foreach ($localisedPageTitles as $idLang => $title) {
            $this->assertNameMatchesRegexPattern($idLang, $title, MetaConstraintException::INVALID_PAGE_TITLE);
        }

        $this->localisedPageTitles = $localisedPageTitles;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLocalisedPageTitles()
    {
        return $this->localisedPageTitles;
    }

    /**
     * @param string[] $localisedMetaDescriptions
     *
     * @return self
     *
     * @throws MetaConstraintException
     */
    public function setLocalisedMetaDescriptions(array $localisedMetaDescriptions)
    {
        foreach ($localisedMetaDescriptions as $idLang => $description) {
            $this->assertNameMatchesRegexPattern($idLang, $description, MetaConstraintException::INVALID_META_DESCRIPTION);
        }

        $this->localisedMetaDescriptions = $localisedMetaDescriptions;

        return $this;
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
    public function getLocalisedRewriteUrls()
    {
        return $this->localisedRewriteUrls;
    }

    /**
     * @param string[] $localisedRewriteUrls
     *
     * @return self
     */
    public function setLocalisedRewriteUrls(array $localisedRewriteUrls)
    {
        $this->localisedRewriteUrls = $localisedRewriteUrls;

        return $this;
    }
}
