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

namespace PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureId;
use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureValueId;

/**
 * Transfers feature value data in command
 */
class ProductFeatureValue
{
    /**
     * @var FeatureId
     */
    private $featureId;

    /**
     * @var FeatureValueId|null
     */
    private $featureValueId;

    /**
     * @var string[]|null
     */
    private $localizedCustomValues;

    /**
     * @param int $featureId
     * @param int|null $featureValueId
     * @param array|null $localizedCustomValues
     */
    public function __construct(int $featureId, ?int $featureValueId = null, ?array $localizedCustomValues = null)
    {
        $this->featureId = new FeatureId($featureId);
        $this->featureValueId = null !== $featureValueId ? new FeatureValueId($featureValueId) : null;
        $this->localizedCustomValues = $localizedCustomValues;
    }

    /**
     * @return FeatureId
     */
    public function getFeatureId(): FeatureId
    {
        return $this->featureId;
    }

    /**
     * @return FeatureValueId|null
     */
    public function getFeatureValueId(): ?FeatureValueId
    {
        return $this->featureValueId;
    }

    /**
     * @param FeatureValueId $featureValueId
     */
    public function setFeatureValueId(FeatureValueId $featureValueId): void
    {
        $this->featureValueId = $featureValueId;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedCustomValues(): ?array
    {
        return $this->localizedCustomValues;
    }
}
