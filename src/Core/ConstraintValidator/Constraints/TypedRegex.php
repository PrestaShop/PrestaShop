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
    public const TYPE_NAME = 'name';
    public const TYPE_CATALOG_NAME = 'catalog_name';
    public const TYPE_GENERIC_NAME = 'generic_name';
    public const TYPE_CITY_NAME = 'city_name';
    public const TYPE_ADDRESS = 'address';
    public const TYPE_POST_CODE = 'post_code';
    public const TYPE_PHONE_NUMBER = 'phone_number';
    public const TYPE_MESSAGE = 'message';
    public const TYPE_LANGUAGE_ISO_CODE = 'language_iso_code';
    public const TYPE_LANGUAGE_CODE = 'language_code';
    public const TYPE_CURRENCY_ISO_CODE = 'currency_iso_code';
    public const TYPE_FILE_NAME = 'file_name';
    public const TYPE_DNI_LITE = 'dni_lite';
    public const TYPE_UPC = 'upc';
    /**
     * @deprecated since 9.0 will be removed in 10.0
     */
    public const TYPE_EAN_13 = 'ean_13';
    public const TYPE_GTIN = 'gtin';
    public const TYPE_ISBN = 'isbn';
    public const TYPE_REFERENCE = 'reference';
    public const TYPE_URL = 'url';
    public const TYPE_MODULE_NAME = 'module_name';
    public const TYPE_STATE_ISO_CODE = 'state_iso_code';
    public const TYPE_WEBSERVICE_KEY = 'webservice_key';
    public const TYPE_LINK_REWRITE = 'link_rewrite';
    public const TYPE_ZIP_CODE_FORMAT = 'zip_code_format';
    public const TYPE_IMAGE_TYPE_NAME = 'image_type_name';
    public const CLEAN_HTML_NO_IFRAME = 'clean_html_no_iframe';
    public const CLEAN_HTML_ALLOW_IFRAME = 'clean_html_allow_iframe';

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

    /**
     * {@inheritDoc}
     */
    public function getDefaultOption()
    {
        return 'type';
    }
}
