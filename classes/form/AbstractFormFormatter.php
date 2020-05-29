<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

abstract class AbstractFormFormatterCore implements FormFormatterInterface
{
    /**
     * Add validation constrains
     *
     * @param $format
     * @param $definition array object difinition
     *
     * @return array
     */
    protected function addConstraints(array $format, array $definition)
    {
        foreach ($format as $field) {
            if (!empty($definition[$field->getName()]['validate'])) {
                $field->addConstraint(
                    $definition[$field->getName()]['validate']
                );
            }
        }

        return $format;
    }

    /**
     * Add max length
     *
     * @param $format
     * @param $definition array object difinition
     *
     * @return array
     */
    protected function addMaxLength(array $format, array $definition)
    {
        foreach ($format as $field) {
            if (!empty($definition[$field->getName()]['size'])) {
                $field->setMaxLength(
                    $definition[$field->getName()]['size']
                );
            }
        }

        return $format;
    }

    /**
     * Set contrains and lengths
     *
     * @param $format
     * @param $definition array object difinition
     *
     * @return array
     */
    protected function setConstraints(array $format, array $definition)
    {
        return $this->addConstraints($this->addMaxLength($format, $definition), $definition);
    }
}
