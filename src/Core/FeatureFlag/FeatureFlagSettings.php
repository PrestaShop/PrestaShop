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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\FeatureFlag;

class FeatureFlagSettings
{
    public const STABILITY_STABLE = 'stable';
    public const STABILITY_BETA = 'beta';

    /**
     * @deprecated Deprecated in 8.1 will be removed in next major.
     */
    public const FEATURE_FLAG_PRODUCT_PAGE_V2 = 'product_page_v2';

    /**
     * @deprecated Deprecated in 8.1 will be removed in next major.
     */
    public const FEATURE_FLAG_PRODUCT_PAGE_V2_MULTI_SHOP = 'product_page_v2_multi_shop';

    public const FEATURE_FLAG_AUTHORIZATION_SERVER = 'authorization_server';
    public const FEATURE_FLAG_MULTIPLE_IMAGE_FORMAT = 'multiple_image_format';
    public const SYMFONY_LAYOUT = 'symfony_layout';
}
