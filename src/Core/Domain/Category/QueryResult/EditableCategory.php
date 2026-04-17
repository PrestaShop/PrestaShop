<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Category\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\QueryResult\RedirectTargetInformation;

/**
 * Stores category data needed for editing.
 */
class EditableCategory
{
    /**
     * @var CategoryId
     */
    private $id;

    /**
     * @var string[]
     */
    private $name;

    /**
     * @var bool
     */
    private $isActive;

    /**
     * @var string[]
     */
    private $description;

    /**
     * @var int
     */
    private $parentId;

    /**
     * @var string[]
     */
    private $metaTitle;

    /**
     * @var string[]
     */
    private $metaDescription;

    private string $redirectType;

    private ?RedirectTargetInformation $categoryRedirectTarget;

    /**
     * @var string[]
     */
    private $linkRewrite;

    /**
     * @var int[]
     */
    private $groupAssociationIds;

    /**
     * @var int[]
     */
    private $shopAssociationIds;

    /**
     * @var mixed
     */
    private $thumbnailImage;

    /**
     * @var null
     */
    private $coverImage;

    /**
     * @var bool
     */
    private $isRootCategory;

    /**
     * @var array
     */
    private $subCategories;

    /**
     * @var string[]
     */
    private $additionalDescription;

    /**
     * @param CategoryId $id
     * @param string[] $name
     * @param bool $isActive
     * @param string[] $description
     * @param int $parentId
     * @param string[] $metaTitle
     * @param string[] $metaDescription
     * @param string $redirectType
     * @param ?RedirectTargetInformation $categoryRedirectTarget
     * @param string[] $linkRewrite
     * @param int[] $groupAssociationIds
     * @param int[] $shopAssociationIds
     * @param bool $isRootCategory
     * @param mixed $coverImage
     * @param mixed $thumbnailImage
     * @param array $subCategories
     * @param string[] $additionalDescription
     */
    public function __construct(
        CategoryId $id,
        array $name,
        $isActive,
        array $description,
        $parentId,
        array $metaTitle,
        array $metaDescription,
        array $linkRewrite,
        string $redirectType,
        ?RedirectTargetInformation $categoryRedirectTarget,
        array $groupAssociationIds,
        array $shopAssociationIds,
        $isRootCategory,
        $coverImage = null,
        $thumbnailImage = null,
        array $subCategories = [],
        array $additionalDescription = []
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->isActive = $isActive;
        $this->description = $description;
        $this->parentId = $parentId;
        $this->metaTitle = $metaTitle;
        $this->metaDescription = $metaDescription;
        $this->linkRewrite = $linkRewrite;
        $this->groupAssociationIds = $groupAssociationIds;
        $this->shopAssociationIds = $shopAssociationIds;
        $this->thumbnailImage = $thumbnailImage;
        $this->coverImage = $coverImage;
        $this->isRootCategory = $isRootCategory;
        $this->subCategories = $subCategories;
        $this->additionalDescription = $additionalDescription;
        $this->redirectType = $redirectType;
        $this->categoryRedirectTarget = $categoryRedirectTarget;
    }

    /**
     * @return CategoryId
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string[]
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->isActive;
    }

    /**
     * @return string[]
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string[]
     */
    public function getAdditionalDescription(): array
    {
        return $this->additionalDescription;
    }

    /**
     * @return int
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * @return string[]
     */
    public function getMetaTitle()
    {
        return $this->metaTitle;
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
    public function getLinkRewrite()
    {
        return $this->linkRewrite;
    }

    public function getRedirectType(): string
    {
        return $this->redirectType;
    }

    public function setRedirectType(string $redirectType): void
    {
        $this->redirectType = $redirectType;
    }

    public function getRedirectTarget(): ?RedirectTargetInformation
    {
        return $this->categoryRedirectTarget;
    }

    public function setRedirectTarget(?RedirectTargetInformation $categoryRedirectTarget): void
    {
        $this->categoryRedirectTarget = $categoryRedirectTarget;
    }

    /**
     * @return int[]
     */
    public function getGroupAssociationIds()
    {
        return $this->groupAssociationIds;
    }

    /**
     * @return int[]
     */
    public function getShopAssociationIds()
    {
        return $this->shopAssociationIds;
    }

    /**
     * @return mixed
     */
    public function getCoverImage()
    {
        return $this->coverImage;
    }

    /**
     * @return mixed
     */
    public function getThumbnailImage()
    {
        return $this->thumbnailImage;
    }

    /**
     * @return bool
     */
    public function isRootCategory()
    {
        return $this->isRootCategory;
    }

    /**
     * @return array
     */
    public function getSubCategories()
    {
        return $this->subCategories;
    }
}
