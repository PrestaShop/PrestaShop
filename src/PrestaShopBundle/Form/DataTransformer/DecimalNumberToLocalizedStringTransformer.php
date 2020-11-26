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

use PrestaShop\Decimal\DecimalNumber;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\DataTransformer\NumberToLocalizedStringTransformer;

class DecimalNumberToLocalizedStringTransformer extends NumberToLocalizedStringTransformer
{
    /**
     * @var int|null
     */
    private $scale;

    /**
     * @var string
     */
    private $emptyData;

    /**
     * {@inheritdoc}
     */
    public function __construct($scale = null, $grouping = false, $roundingMode = self::ROUND_HALF_UP, $emptyData = '')
    {
        $this->scale = $scale;
        $this->emptyData = $emptyData;
        parent::__construct($scale, $grouping, $roundingMode);
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (null === $value) {
            $value = new DecimalNumber($this->emptyData);
        }

        if (!($value instanceof DecimalNumber)) {
            throw new TransformationFailedException(sprintf('Expected a %s.', DecimalNumber::class));
        }

        return parent::transform($value->toPrecision($this->scale));
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        return new DecimalNumber((string) $value);
    }
}
