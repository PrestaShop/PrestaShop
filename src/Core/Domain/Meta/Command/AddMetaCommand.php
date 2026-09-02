<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Meta\Command;

use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\MetaConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Meta\ValueObject\Name;

/**
 * Class AddMetaCommand is responsible for saving meta entities data.
 */
class AddMetaCommand extends AbstractMetaCommand
{
    /**
     * @var Name
     */
    private $pageName;

    /**
     * @var string[]
     */
    private $localisedPageTitle;

    /**
     * @var string[]
     */
    private $localisedMetaDescription;

    /**
     * @var string[]
     */
    private $LocalisedRewriteUrls;

    /**
     * @param string $pageName
     *
     * @throws MetaConstraintException
     */
    public function __construct($pageName)
    {
        $this->pageName = new Name($pageName);
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
        return $this->localisedPageTitle;
    }

    /**
     * @param string[] $localisedPageTitle
     *
     * @return self
     *
     * @throws MetaConstraintException
     */
    public function setLocalisedPageTitle(array $localisedPageTitle)
    {
        foreach ($localisedPageTitle as $idLang => $title) {
            $this->assertNameMatchesRegexPattern($idLang, $title, MetaConstraintException::INVALID_PAGE_TITLE);
        }

        $this->localisedPageTitle = $localisedPageTitle;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLocalisedMetaDescription()
    {
        return $this->localisedMetaDescription;
    }

    /**
     * @param string[] $localisedMetaDescription
     *
     * @return self
     *
     * @throws MetaConstraintException
     */
    public function setLocalisedMetaDescription(array $localisedMetaDescription)
    {
        foreach ($localisedMetaDescription as $idLang => $description) {
            $this->assertNameMatchesRegexPattern($idLang, $description, MetaConstraintException::INVALID_META_DESCRIPTION);
        }

        $this->localisedMetaDescription = $localisedMetaDescription;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLocalisedRewriteUrls()
    {
        return $this->LocalisedRewriteUrls;
    }

    /**
     * @param string[] $LocalisedRewriteUrls
     *
     * @return self
     */
    public function setLocalisedRewriteUrls(array $LocalisedRewriteUrls)
    {
        $this->LocalisedRewriteUrls = $LocalisedRewriteUrls;

        return $this;
    }
}
