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

namespace PrestaShopBundle\Service\Database;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use ReflectionClass;

/**
 * Naming strategy for Doctrine ORM to use prefixed database table names.
 */
class DoctrineNamingStrategy extends UnderscoreNamingStrategy
{
    /**
     * Constructor.
     *
     * Prefix is given by injection, set in app/config/parameters.yml.
     *
     * @param string $prefix
     */
    public function __construct(
        private Reader $reader,
        private string $prefix,
    ) {
        parent::__construct(CASE_LOWER, true);
    }

    /**
     * {@inheritdoc}
     *
     * This override adds a prefix to the underscored table name.
     */
    public function classToTableName($className)
    {
        $underscored = parent::classToTableName($className);

        return $this->prefix . $underscored;
    }

    /**
     * {@inheritdoc}
     *
     * This override adds a prefix to the underscored table name.
     */
    public function joinTableName($sourceEntity, $targetEntity, $propertyName = null)
    {
        $prestashopTable = $this->getPrestashopTable($sourceEntity, $propertyName);
        if (!empty($prestashopTable)) {
            return $prestashopTable;
        }

        return $this->prefix . parent::classToTableName($sourceEntity) . '_' . parent::classToTableName($targetEntity);
    }

    /**
     * In case the join table doesn't match the default value (owner_ownee) we need to define a custom table
     * but we still need to prepend the prefix so we rely on a custom options ps_table on the JoinTable annotation
     * and we correctly prepend the prestashop prefix.
     *
     * @param string $sourceEntity
     * @param string|null $propertyName
     *
     * @return string|null
     */
    private function getPrestashopTable($sourceEntity, $propertyName = null): ?string
    {
        $reflectionClass = new ReflectionClass($sourceEntity);
        if (!$reflectionClass->hasProperty($propertyName)) {
            return null;
        }

        $propertyAnnotations = $this->reader->getPropertyAnnotations($reflectionClass->getProperty($propertyName));
        foreach ($propertyAnnotations as $propertyAnnotation) {
            if ($propertyAnnotation instanceof JoinTable && !empty($propertyAnnotation->options['ps_table'])) {
                return $this->prefix . $propertyAnnotation->options['ps_table'];
            }
        }

        return null;
    }
}
