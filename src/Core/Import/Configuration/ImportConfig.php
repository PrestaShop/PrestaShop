<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
