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

namespace PrestaShopBundle\Form\DataTransformer;

use PrestaShop\PrestaShop\Core\Util\ArabicToLatinDigitConverter;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class ArabicToLatinDigitDataTransformer is responsible for converting arabic/persian digits to latin digits
 */
final class ArabicToLatinDigitDataTransformer implements DataTransformerInterface
{
    /**
     * @var ArabicToLatinDigitConverter
     */
    private $arabicToLatinDigitConverter;

    public function __construct(ArabicToLatinDigitConverter $arabicToLatinDigitConverter)
    {
        $this->arabicToLatinDigitConverter = $arabicToLatinDigitConverter;
    }

    /**
     * Do not transform latin number to arabic/persian number as
     * the javascript datepicker will handle that on its side
     *
     * {@inheritdoc}
     */
    public function transform($value)
    {
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (null === $value || '' === $value) {
            return null;
        }

        return $this->arabicToLatinDigitConverter->convert($value);
    }
}
