<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Command;

use DateTimeInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Adds downloadable file for virtual product
 */
class AddVirtualProductFileCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var string
     */
    private $filePath;

    /**
     * @var string
     */
    private $displayName;

    /**
     * @var DateTimeInterface|null
     */
    private $expirationDate;

    /**
     * @var int|null
     */
    private $accessDays;

    /**
     * @var int|null
     */
    private $downloadTimesLimit;

    /**
     * @param int $productId
     * @param string $filePath
     * @param string $displayName display name of the file
     * @param int|null $accessDays
     * @param int|null $downloadTimesLimit
     * @param DateTimeInterface|null $expirationDate
     */
    public function __construct(
        int $productId,
        string $filePath,
        string $displayName,
        ?int $accessDays = null,
        ?int $downloadTimesLimit = null,
        ?DateTimeInterface $expirationDate = null
    ) {
        $this->productId = new ProductId($productId);
        $this->filePath = $filePath;
        $this->displayName = $displayName;
        $this->accessDays = $accessDays;
        $this->downloadTimesLimit = $downloadTimesLimit;
        $this->expirationDate = $expirationDate;
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return string
     */
    public function getFilePath(): string
    {
        return $this->filePath;
    }

    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getExpirationDate(): ?DateTimeInterface
    {
        return $this->expirationDate;
    }

    /**
     * @return int|null
     */
    public function getAccessDays(): ?int
    {
        return $this->accessDays;
    }

    /**
     * @return int|null
     */
    public function getDownloadTimesLimit(): ?int
    {
        return $this->downloadTimesLimit;
    }
}
