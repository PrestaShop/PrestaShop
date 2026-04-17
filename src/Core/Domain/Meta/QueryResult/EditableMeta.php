<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
    private $localisedUrlRewrites;

    /**
     * EditableMeta constructor.
     *
     * @param int $metaId
     * @param string $pageName
     * @param string[] $localisedPageTitles
     * @param string[] $localisedMetaDescriptions
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
        array $localisedUrlRewrites
    ) {
        $this->metaId = new MetaId($metaId);
        $this->pageName = new Name($pageName);
        $this->localisedPageTitles = $localisedPageTitles;
        $this->localisedMetaDescriptions = $localisedMetaDescriptions;
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
    public function getLocalisedUrlRewrites()
    {
        return $this->localisedUrlRewrites;
    }
}
