<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectTarget;
use PrestaShop\PrestaShop\Core\Domain\QueryResult\RedirectTargetInformation;

/**
 * Transfers data about Product SEO options
 */
class ProductSeoOptions
{
    /**
     * @var string[]
     */
    private $localizedMetaTitles;

    /**
     * @var string[]
     */
    private $localizedMetaDescriptions;

    /**
     * @var string[]
     */
    private $localizedLinkRewrites;

    /**
     * @var string
     */
    private $redirectType;

    /**
     * @var RedirectTargetInformation
     */
    private $redirectTarget;

    /**
     * @param string[] $localizedMetaTitles
     * @param string[] $localizedMetaDescriptions
     * @param string[] $localizedLinkRewrites
     * @param string $redirectType
     * @param RedirectTargetInformation|null $redirectTarget
     */
    public function __construct(
        array $localizedMetaTitles,
        array $localizedMetaDescriptions,
        array $localizedLinkRewrites,
        string $redirectType,
        ?RedirectTargetInformation $redirectTarget
    ) {
        $this->localizedMetaTitles = $localizedMetaTitles;
        $this->localizedMetaDescriptions = $localizedMetaDescriptions;
        $this->localizedLinkRewrites = $localizedLinkRewrites;
        $this->redirectType = $redirectType;
        $this->redirectTarget = $redirectTarget;
    }

    /**
     * @return string[]
     */
    public function getLocalizedMetaTitles(): array
    {
        return $this->localizedMetaTitles;
    }

    /**
     * @return string[]
     */
    public function getLocalizedMetaDescriptions(): array
    {
        return $this->localizedMetaDescriptions;
    }

    /**
     * @return string[]
     */
    public function getLocalizedLinkRewrites(): array
    {
        return $this->localizedLinkRewrites;
    }

    /**
     * @return string
     */
    public function getRedirectType(): string
    {
        return $this->redirectType;
    }

    /**
     * @return int
     */
    public function getRedirectTargetId(): int
    {
        return null !== $this->redirectTarget ? $this->redirectTarget->getId() : RedirectTarget::NO_TARGET;
    }

    /**
     * @return RedirectTargetInformation|null
     */
    public function getRedirectTarget(): ?RedirectTargetInformation
    {
        return $this->redirectTarget;
    }
}
