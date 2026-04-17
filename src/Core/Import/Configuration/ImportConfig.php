<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\Configuration;

/**
 * Class ImportConfig defines import configuration.
 */
final class ImportConfig implements ImportConfigInterface
{
    /**
     * @var string
     */
    private $fileName;

    /**
     * @var int
     */
    private $entityType;

    /**
     * @var string
     */
    private $languageIso;

    /**
     * @var string
     */
    private $separator;

    /**
     * @var string
     */
    private $multipleValueSeparator;

    /**
     * @var bool
     */
    private $truncate;

    /**
     * @var bool
     */
    private $skipThumbnailRegeneration;

    /**
     * @var bool
     */
    private $matchReferences;

    /**
     * @var bool
     */
    private $forceIds;

    /**
     * @var bool
     */
    private $sendEmail;

    /**
     * @var int
     */
    private $skipRows;

    /**
     * @param string $fileName
     * @param int $entityType
     * @param string $languageIso
     * @param string $separator
     * @param string $multipleValueSeparator
     * @param bool $truncate
     * @param bool $skipThumbnailRegeneration
     * @param bool $matchReferences
     * @param bool $forceIds
     * @param bool $sendEmail
     * @param int $skipRows
     */
    public function __construct(
        $fileName,
        $entityType,
        $languageIso,
        $separator,
        $multipleValueSeparator,
        $truncate,
        $skipThumbnailRegeneration,
        $matchReferences,
        $forceIds,
        $sendEmail,
        $skipRows = 0
    ) {
        $this->fileName = $fileName;
        $this->entityType = $entityType;
        $this->languageIso = $languageIso;
        $this->separator = $separator;
        $this->multipleValueSeparator = $multipleValueSeparator;
        $this->truncate = $truncate;
        $this->skipThumbnailRegeneration = $skipThumbnailRegeneration;
        $this->matchReferences = $matchReferences;
        $this->forceIds = $forceIds;
        $this->sendEmail = $sendEmail;
        $this->skipRows = $skipRows;
    }

    /**
     * {@inheritdoc}
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityType()
    {
        return $this->entityType;
    }

    /**
     * {@inheritdoc}
     */
    public function getLanguageIso()
    {
        return $this->languageIso;
    }

    /**
     * {@inheritdoc}
     */
    public function getSeparator()
    {
        return $this->separator;
    }

    /**
     * {@inheritdoc}
     */
    public function getMultipleValueSeparator()
    {
        return $this->multipleValueSeparator;
    }

    /**
     * {@inheritdoc}
     */
    public function truncate()
    {
        return $this->truncate;
    }

    /**
     * {@inheritdoc}
     */
    public function skipThumbnailRegeneration()
    {
        return $this->skipThumbnailRegeneration;
    }

    /**
     * {@inheritdoc}
     */
    public function matchReferences()
    {
        return $this->matchReferences;
    }

    /**
     * {@inheritdoc}
     */
    public function forceIds()
    {
        return $this->forceIds;
    }

    /**
     * {@inheritdoc}
     */
    public function sendEmail()
    {
        return $this->sendEmail;
    }

    /**
     * {@inheritdoc}
     */
    public function getNumberOfRowsToSkip()
    {
        return $this->skipRows;
    }
}
