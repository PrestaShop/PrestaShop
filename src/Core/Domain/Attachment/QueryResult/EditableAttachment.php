<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Attachment\QueryResult;

use Symfony\Component\HttpFoundation\File\File;

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
     * @var File|null
     */
    private $file;

    /**
     * @param string $fileName
     * @param string[] $name
     * @param string[]|null $description
     * @param File|null $file
     *
     */
    public function __construct(
        string $fileName,
        array $name,
        ?array $description,
        ?File $file
    ) {
        $this->fileName = $fileName;
        $this->name = $name;
        $this->description = $description;
        $this->file = $file;
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
     * @return File|null
     */
    public function getFile(): ?File
    {
        return $this->file;
    }
}
