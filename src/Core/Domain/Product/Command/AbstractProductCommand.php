<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductName;

abstract class AbstractProductCommand
{
    /**
     * @var ProductName[]
     */
    private $localisedProductNames;
    
    /**
     * @var array
     */
    private $images;

    /**
     * @var null;
     */
    private $pricing;

    /**
     * @var array|string[]
     */
    private $localisedSummary;

    /**
     * @var array|string[]
     */
    private $localisedDescription;

    /**
     * @var array
     */
    private $features;

    /**
     * @var int
     */
    private $brandId;

    /**
     * @var array|int[]
     */
    private $relatedProductIds;

    /**
     * @var array
     */
    private $categories;

    /**
     * @var array
     */
    private $metaTitle;

    /**
     * @var array
     */
    private $metaDescription;

    /**
     * @var array
     */
    private $metaTags;

    /**
     * @var array
     */
    private $friendlyUrl;

    /**
     * @var null
     */
    private $redirectionPage;

    /**
     * @var null
     */
    private $visibility;

    /**
     * @var null
     */
    private $condition;

    /**
     * @var null
     */
    private $references;

    /**
     * @var array
     */
    private $customizationFields;

    /**
     * @var array
     */
    private $attachments;

    /**
     * @param string[] $localisedProductNames
     */
    public function __construct(array $localisedProductNames)
    {
        $this->setLocalisedProductNames($localisedProductNames);
    }

    /**
     * @return ProductName[]
     */
    public function getLocalisedProductNames(): array
    {
        return $this->localisedProductNames;
    }

    /**
     * @return array
     */
    public function getImages(): ?array
    {
        return $this->images;
    }

    /**
     * @param array $images
     *
     * @return self
     */
    public function setImages(array $images): self
    {
        $this->images = $images;

        return $this;
    }

    /**
     * @return null
     */
    public function getPricing()
    {
        return $this->pricing;
    }

    /**
     * @param null $pricing
     *
     * @return self
     */
    public function setPricing($pricing): self
    {
        $this->pricing = $pricing;

        return $this;
    }

    /**
     * @return array|string[]
     */
    public function getLocalisedSummary(): ?array
    {
        return $this->localisedSummary;
    }

    /**
     * @param array|string[] $localisedSummary
     *
     * @return self
     */
    public function setLocalisedSummary($localisedSummary): self
    {
        $this->localisedSummary = $localisedSummary;

        return $this;
    }

    /**
     * @return array|string[]
     */
    public function getLocalisedDescription(): array
    {
        return $this->localisedDescription;
    }

    /**
     * @param array|string[] $localisedDescription
     *
     * @return self
     */
    public function setLocalisedDescription($localisedDescription): self
    {
        $this->localisedDescription = $localisedDescription;

        return $this;
    }

    /**
     * @return array
     */
    public function getFeatures(): array
    {
        return $this->features;
    }

    /**
     * @param array $features
     *
     * @return self
     */
    public function setFeatures(array $features): void
    {
        $this->features = $features;
        return $this;
    }

    /**
     * @return int
     */
    public function getBrandId(): int
    {
        return $this->brandId;
    }

    /**
     * @param int $brandId
     *
     * @return self
     */
    public function setBrandId(int $brandId): self
    {
        $this->brandId = $brandId;

        return $this;
    }

    /**
     * @return array|int[]
     */
    public function getRelatedProductIds(): ?array
    {
        return $this->relatedProductIds;
    }

    /**
     * @param array|int[] $relatedProductIds
     *
     * @return self
     */
    public function setRelatedProductIds($relatedProductIds): self
    {
        $this->relatedProductIds = $relatedProductIds;

        return $this;
    }

    /**
     * @return array
     */
    public function getCategories(): ?array
    {
        return $this->categories;
    }

    /**
     * @param array $categories
     *
     * @return self
     */
    public function setCategories(array $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * @return array
     */
    public function getMetaTitle(): ?array
    {
        return $this->metaTitle;
    }

    /**
     * @param array $metaTitle
     *
     * @return self
     */
    public function setMetaTitle(array $metaTitle): self
    {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    /**
     * @return array
     */
    public function getMetaDescription(): ?array
    {
        return $this->metaDescription;
    }

    /**
     * @param array $metaDescription
     *
     * @return self
     */
    public function setMetaDescription(array $metaDescription): self
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    /**
     * @return array
     */
    public function getMetaTags(): ?array
    {
        return $this->metaTags;
    }

    /**
     * @param array $metaTags
     *
     * @return self
     */
    public function setMetaTags(array $metaTags): self
    {
        $this->metaTags = $metaTags;
        return $this;
    }

    /**
     * @return array
     */
    public function getFriendlyUrl(): ?array
    {
        return $this->friendlyUrl;
    }

    /**
     * @param array $friendlyUrl
     *
     * @return self
     */
    public function setFriendlyUrl(array $friendlyUrl): self
    {
        $this->friendlyUrl = $friendlyUrl;

        return $this;
    }

    /**
     * @return null
     */
    public function getRedirectionPage()
    {
        return $this->redirectionPage;
    }

    /**
     * @param null $redirectionPage
     *
     * @return self
     */
    public function setRedirectionPage($redirectionPage): self
    {
        $this->redirectionPage = $redirectionPage;

        return $this;
    }

    /**
     * @return null
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * @param null $visibility
     *
     * @return self
     */
    public function setVisibility($visibility): self
    {
        $this->visibility = $visibility;

        return $this;
    }

    /**
     * @return null
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * @param null $condition
     *
     * @return self
     */
    public function setCondition($condition): self
    {
        $this->condition = $condition;

        return $this;
    }

    /**
     * @return null
     */
    public function getReferences()
    {
        return $this->references;
    }

    /**
     * @param null $references
     *
     * @return self
     */
    public function setReferences($references): self
    {
        $this->references = $references;

        return $this;
    }

    /**
     * @return array
     */
    public function getCustomizationFields(): ?array
    {
        return $this->customizationFields;
    }

    /**
     * @param array $customizationFields
     *
     * @return self
     */
    public function setCustomizationFields(array $customizationFields): void
    {
        $this->customizationFields = $customizationFields;
        return $this;
    }

    /**
     * @return array
     */
    public function getAttachments(): ?array
    {
        return $this->attachments;
    }

    /**
     * @param array $attachments
     *
     * @return self
     */
    public function setAttachments(array $attachments): self
    {
        $this->attachments = $attachments;

        return $this;
    }


    private function setLocalisedProductNames(array $productNames): self
    {
        foreach ($productNames as $productName) {
            $this->localisedProductNames[] = new ProductName($productName);
        }
        
        return $this;
    }
}
