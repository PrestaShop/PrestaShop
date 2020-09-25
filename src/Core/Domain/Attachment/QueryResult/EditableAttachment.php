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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Domain\Attachment\QueryResult;

use SplFileInfo;

/**
 * Stores editable data for attachment
 */
class EditableAttachment
{
    /**
     * @var string
     */
    private $fileName;

    /**
     * @var string[]
     */
    private $name;

    /**
     * @var string[]|null
     */
    private $description;

    /**
     * @var SplFileInfo|null
     */
    private $file;

    /**
     * @param string $fileName
     * @param string[] $name
     * @param string[]|null $description
     */
    public function __construct(
        string $fileName,
        array $name,
        array $description
    ) {
        $this->fileName = $fileName;
        $this->name = $name;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @return string[]
     */
    public function getName(): array
    {
        return $this->name;
    }

    /**
     * @return string[]|null
     */
    public function getDescription(): ?array
    {
        return $this->description;
    }

    /**
     * @return SplFileInfo|null
     */
    public function getFile(): ?SplFileInfo
    {
        return $this->file;
    }

    /**
     * @param SplFileInfo|null $file
     *
     * @return EditableAttachment
     */
    public function setFile(?SplFileInfo $file): EditableAttachment
    {
        $this->file = $file;

        return $this;
    }
}
