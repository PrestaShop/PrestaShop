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

namespace PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints;

use PrestaShop\PrestaShop\Core\ConstraintValidator\TypedRegexValidator;
use Symfony\Component\Validator\Constraint;

/**
 * Provides regex validation by type
 */
class TypedRegex extends Constraint
{
    /**
     * Available types
     */
    const TYPE_NAME = 'name';
    const TYPE_CATALOG_NAME = 'catalog_name';
    const TYPE_GENERIC_NAME = 'generic_name';
    const TYPE_CITY_NAME = 'city_name';
    const TYPE_ADDRESS = 'address';
    const TYPE_POST_CODE = 'post_code';
    const TYPE_PHONE_NUMBER = 'phone_number';
    const TYPE_MESSAGE = 'message';
    const TYPE_LANGUAGE_ISO_CODE = 'language_iso_code';
    const TYPE_LANGUAGE_CODE = 'language_code';
    const TYPE_CURRENCY_ISO_CODE = 'currency_iso_code';
    const TYPE_FILE_NAME = 'file_name';
    const TYPE_DNI_LITE = 'dni_lite';

    /**
     * @var string
     */
    public $message = '%s is invalid';

    /**
     * @var string
     */
    public $type;

    /**
     * {@inheritdoc}
     */
    public function getRequiredOptions()
    {
        return ['type'];
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return TypedRegexValidator::class;
    }
}
