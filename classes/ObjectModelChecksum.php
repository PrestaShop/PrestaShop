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

/**
 * Class ObjectModelChecksumCore.
 */
class ObjectModelChecksumCore implements ChecksumInterface
{
    public const SEPARATOR = '_';

    /**
     * Hash algorithm name
     *
     * @var string
     */
    protected $hashAlgo;

    public function __construct(string $hashAlgo = 'sha1')
    {
        $this->hashAlgo = $hashAlgo;
    }

    /**
     * Generate a checksum.
     *
     * @param ObjectModel $objectModel
     *
     * @return string SHA1 checksum for an objectModel
     *
     * @throws InvalidArgumentException
     */
    public function generateChecksum($objectModel)
    {
        if (!($objectModel instanceof ObjectModel)) {
            throw new InvalidArgumentException('The given object is not valid. Expected ObjectModel');
        }

        if (!Validate::isLoadedObject($objectModel)) {
            return hash($this->hashAlgo, 'No ' . get_class($objectModel) . ' set');
        }

        $uniqId = '';

        foreach ($objectModel->getFields() as $value) {
            $uniqId .= $value . static::SEPARATOR;
        }

        $uniqId = rtrim($uniqId, static::SEPARATOR);

        return hash($this->hashAlgo, $uniqId);
    }
}
