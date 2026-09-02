<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\QueryResult;

use DateTimeInterface;

/**
 * Holds data for editing Virtual Product File
 */
class VirtualProductFileForEditing
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $fileName;

    /**
     * @var string
     */
    private $displayName;

    /**
     * @var int
     */
    private $accessDays;

    /**
     * @var int
     */
    private $downloadTimesLimit;

    /**
     * @var DateTimeInterface|null
     */
    private $expirationDate;

    /**
     * @param int $id
     * @param string $fileName
     * @param string $displayName
     * @param int $accessDays
     * @param int $downloadTimesLimit
     * @param DateTimeInterface|null $expirationDate
     */
    public function __construct(
        int $id,
        string $fileName,
        string $displayName,
        int $accessDays,
        int $downloadTimesLimit,
        ?DateTimeInterface $expirationDate
    ) {
        $this->id = $id;
        $this->fileName = $fileName;
        $this->displayName = $displayName;
        $this->accessDays = $accessDays;
        $this->downloadTimesLimit = $downloadTimesLimit;
        $this->expirationDate = $expirationDate;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * @return int
     */
    public function getAccessDays(): int
    {
        return $this->accessDays;
    }

    /**
     * @return int
     */
    public function getDownloadTimesLimit(): int
    {
        return $this->downloadTimesLimit;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getExpirationDate(): ?DateTimeInterface
    {
        return $this->expirationDate;
    }
}
