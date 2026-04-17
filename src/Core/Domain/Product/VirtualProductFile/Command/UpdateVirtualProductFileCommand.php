<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Command;

use DateTimeInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\ValueObject\VirtualProductFileId;

class UpdateVirtualProductFileCommand
{
    /**
     * @var VirtualProductFileId
     */
    private $virtualProductFileId;

    /**
     * @var string|null
     */
    private $filePath;

    /**
     * @var string|null
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
     * @param int $virtualProductFileId
     */
    public function __construct(
        int $virtualProductFileId
    ) {
        $this->virtualProductFileId = new VirtualProductFileId($virtualProductFileId);
    }

    /**
     * @return VirtualProductFileId
     */
    public function getVirtualProductFileId(): VirtualProductFileId
    {
        return $this->virtualProductFileId;
    }

    /**
     * @return string|null
     */
    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    /**
     * @param string|null $filePath
     */
    public function setFilePath(?string $filePath): void
    {
        $this->filePath = $filePath;
    }

    /**
     * @return string|null
     */
    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    /**
     * @param string|null $displayName
     */
    public function setDisplayName(?string $displayName): void
    {
        $this->displayName = $displayName;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getExpirationDate(): ?DateTimeInterface
    {
        return $this->expirationDate;
    }

    /**
     * @param DateTimeInterface|null $expirationDate
     */
    public function setExpirationDate(?DateTimeInterface $expirationDate): void
    {
        $this->expirationDate = $expirationDate;
    }

    /**
     * @return int|null
     */
    public function getAccessDays(): ?int
    {
        return $this->accessDays;
    }

    /**
     * @param int|null $accessDays
     */
    public function setAccessDays(?int $accessDays): void
    {
        $this->accessDays = $accessDays;
    }

    /**
     * @return int|null
     */
    public function getDownloadTimesLimit(): ?int
    {
        return $this->downloadTimesLimit;
    }

    /**
     * @param int|null $downloadTimesLimit
     */
    public function setDownloadTimesLimit(?int $downloadTimesLimit): void
    {
        $this->downloadTimesLimit = $downloadTimesLimit;
    }
}
