<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Import\EntityField\Provider;

use InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Import\ImportSettings;

/**
 * Class EntityFieldsProviderFinder defines an entity fields provider finder.
 */
final class EntityFieldsProviderFinder implements EntityFieldsProviderFinderInterface
{
    /**
     * @var int import entity
     *
     * @see ImportSettings
     */
    private $importEntity;

    /**
     * @var array of entity fields providers
     */
    private $entityFieldsProviders;

    /**
     * @param int $importEntity
     * @param array $entityFieldsProviders
     */
    public function __construct($importEntity, array $entityFieldsProviders)
    {
        $this->importEntity = $importEntity;
        $this->entityFieldsProviders = $entityFieldsProviders;
    }

    /**
     * {@inheritdoc}
     */
    public function find()
    {
        if (!isset($this->entityFieldsProviders[$this->importEntity])) {
            throw new InvalidArgumentException("Entity fields provider does not exist for entity $this->importEntity.");
        }

        return $this->entityFieldsProviders[$this->importEntity];
    }
}
