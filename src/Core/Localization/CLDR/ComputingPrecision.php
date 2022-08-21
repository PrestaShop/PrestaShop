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

namespace PrestaShop\PrestaShop\Core\Localization\CLDR;

/**
 * {@inheritdoc}
 */
final class ComputingPrecision implements ComputingPrecisionInterface
{
    public const MULTIPLIER = 1;
    public const MINIMAL_VALUE = 0;

    /**
     * {@inheritdoc}
     */
    public function getPrecision($displayPrecision)
    {
        // Earlier, there was int type hint in this method; but in the process of developing one e-store with KRW currency that does not have analog of cents and does not need precision, it was found that sometimes null is passed to this method which causes some errors. Detailed testing is needed to reproduce errors, they were not logged fully; but this bugfix with replacing int type hint with intval() call worked fine and did not cause any visible issues. Obviously, the original issue and bugfix should be researched by some experienced PrestaShop core developer.
        $displayPrecision = intval($displayPrecision);

        // the MULTIPLIER attribute is set to 1 for now, so that it matches display precision
        $computingPrecision = $displayPrecision * self::MULTIPLIER;

        return ($computingPrecision < self::MINIMAL_VALUE) ? self::MINIMAL_VALUE : $computingPrecision;
    }
}
