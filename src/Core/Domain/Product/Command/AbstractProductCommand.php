<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\Command;

abstract class AbstractProductCommand
{
    /**
     * @var array|string[]
     */
    private $localisedProductNames;
    /**
     * @var array
     */
    private $images;

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

    private $redirectionPage;

    private $visibility;

    private $condition;

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
     * @return array|string[]
     */
    public function getLocalisedProductNames()
    {
        return $this->localisedProductNames;
    }

    /**
     * @return array
     */
    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * @param array $images
     *
     * @return self
     */
    public function setImages(array $images): void
    {
        $this->images = $images;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPricing()
    {
        return $this->pricing;
    }

    /**
     * @param mixed $pricing
     *
     * @return self
     */
    public function setPricing($pricing): void
    {
        $this->pricing = $pricing;
        return $this;
    }

    /**
     * @return array|string[]
     */
    public function getLocalisedSummary()
    {
        return $this->localisedSummary;
    }

    /**
     * @param array|string[] $localisedSummary
     *
     * @return self
     */
    public function setLocalisedSummary($localisedSummary): void
    {
        $this->localisedSummary = $localisedSummary;
        return $this;
    }

    /**
     * @return array|string[]
     */
    public function getLocalisedDescription()
    {
        return $this->localisedDescription;
    }

    /**
     * @param array|string[] $localisedDescription
     *
     * @return self
     */
    public function setLocalisedDescription($localisedDescription): void
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
    public function setBrandId(int $brandId): void
    {
        $this->brandId = $brandId;
        return $this;
    }

    /**
     * @return array|int[]
     */
    public function getRelatedProductIds()
    {
        return $this->relatedProductIds;
    }

    /**
     * @param array|int[] $relatedProductIds
     *
     * @return self
     */
    public function setRelatedProductIds($relatedProductIds): void
    {
        $this->relatedProductIds = $relatedProductIds;
        return $this;
    }

    /**
     * @return array
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * @param array $categories
     *
     * @return self
     */
    public function setCategories(array $categories): void
    {
        $this->categories = $categories;
        return $this;
    }

    /**
     * @return array
     */
    public function getMetaTitle(): array
    {
        return $this->metaTitle;
    }

    /**
     * @param array $metaTitle
     *
     * @return self
     */
    public function setMetaTitle(array $metaTitle): void
    {
        $this->metaTitle = $metaTitle;
        return $this;
    }

    /**
     * @return array
     */
    public function getMetaDescription(): array
    {
        return $this->metaDescription;
    }

    /**
     * @param array $metaDescription
     *
     * @return self
     */
    public function setMetaDescription(array $metaDescription): void
    {
        $this->metaDescription = $metaDescription;
        return $this;
    }

    /**
     * @return array
     */
    public function getMetaTags(): array
    {
        return $this->metaTags;
    }

    /**
     * @param array $metaTags
     *
     * @return self
     */
    public function setMetaTags(array $metaTags): void
    {
        $this->metaTags = $metaTags;
        return $this;
    }

    /**
     * @return array
     */
    public function getFriendlyUrl(): array
    {
        return $this->friendlyUrl;
    }

    /**
     * @param array $friendlyUrl
     *
     * @return self
     */
    public function setFriendlyUrl(array $friendlyUrl): void
    {
        $this->friendlyUrl = $friendlyUrl;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRedirectionPage()
    {
        return $this->redirectionPage;
    }

    /**
     * @param mixed $redirectionPage
     *
     * @return self
     */
    public function setRedirectionPage($redirectionPage): void
    {
        $this->redirectionPage = $redirectionPage;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * @param mixed $visibility
     *
     * @return self
     */
    public function setVisibility($visibility): void
    {
        $this->visibility = $visibility;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * @param mixed $condition
     *
     * @return self
     */
    public function setCondition($condition): void
    {
        $this->condition = $condition;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReferences()
    {
        return $this->references;
    }

    /**
     * @param mixed $references
     *
     * @return self
     */
    public function setReferences($references): void
    {
        $this->references = $references;
        return $this;
    }

    /**
     * @return array
     */
    public function getCustomizationFields(): array
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
    public function getAttachments(): array
    {
        return $this->attachments;
    }

    /**
     * @param array $attachments
     *
     * @return self
     */
    public function setAttachments(array $attachments): void
    {
        $this->attachments = $attachments;
        return $this;
    }

    private function setLocalisedProductNames(array $productNames): void
    {
        $this->localisedProductNames = $productNames;
    }
}
