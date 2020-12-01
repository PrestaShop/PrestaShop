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
use PrestaShop\Decimal\Operation\Rounding;
use PrestaShopBundle\Form\Admin\Type\DecimalNumberType;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\DataTransformer\NumberToLocalizedStringTransformer;

/**
 * This transformer allows to use DecimalNumber as input in a form straight away @see DecimalNumberType
 */
class DecimalNumberToLocalizedStringTransformer extends NumberToLocalizedStringTransformer
{
    private const ROUNDING_MAP = [
        self::ROUND_HALF_UP => Rounding::ROUND_HALF_UP,
        self::ROUND_HALF_EVEN => Rounding::ROUND_HALF_EVEN,
        self::ROUND_HALF_DOWN => Rounding::ROUND_HALF_DOWN,
        self::ROUND_FLOOR => Rounding::ROUND_FLOOR,
        self::ROUND_CEILING => Rounding::ROUND_CEIL,
        self::ROUND_DOWN => Rounding::ROUND_HALF_DOWN,
        self::ROUND_UP => Rounding::ROUND_HALF_UP,
    ];

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
        $this->emptyData = $emptyData ?: '0.0';
        parent::__construct($scale, $grouping, $roundingMode);
    }

    /**
     * @phpstan-ignore-next-line
     *
     * @param DecimalNumber $value The value in the original representation
     *
     * @return string The value in the transformed representation
     */
    public function transform($value)
    {
        if (null === $value) {
            $value = new DecimalNumber($this->emptyData);
        }

        if (!($value instanceof DecimalNumber)) {
            throw new TransformationFailedException(sprintf('Expected a %s.', DecimalNumber::class));
        }

        return parent::transform($value->toPrecision($this->scale, $this->getRounding($this->roundingMode)));
    }

    /**
     * @phpstan-ignore-next-line
     *
     * @param string $value The value in the transformed representation
     *
     * @return DecimalNumber The value in the original representation
     */
    public function reverseTransform($value)
    {
        $value = $value ?: $this->emptyData;
        // We use parent method to apply rounding
        $transformedValue = parent::reverseTransform($value);

        return new DecimalNumber((string) $transformedValue);
    }

    /**
     * @param int $roundingMode
     *
     * @return string
     */
    private function getRounding(int $roundingMode): string
    {
        if (!isset(self::ROUNDING_MAP[$roundingMode])) {
            return Rounding::ROUND_TRUNCATE;
        }

        return self::ROUNDING_MAP[$roundingMode];
    }
}
