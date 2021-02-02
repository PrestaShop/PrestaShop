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

namespace PrestaShop\PrestaShop\Adapter\Feature\Repository;

use FeatureValue;
use PrestaShop\PrestaShop\Adapter\AbstractObjectModelRepository;
use PrestaShop\PrestaShop\Adapter\Feature\Validate\FeatureValueValidator;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\CannotAddFeatureValueException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\CannotUpdateFeatureValueException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureValueNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\InvalidFeatureValueIdException;
use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureValueId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;

/**
 * Methods to access data storage for FeatureValue
 */
class FeatureValueRepository extends AbstractObjectModelRepository
{
    /**
     * @var FeatureValueValidator
     */
    private $featureValueValidator;

    /**
     * @param FeatureValueValidator $featureValueValidator
     */
    public function __construct(FeatureValueValidator $featureValueValidator)
    {
        $this->featureValueValidator = $featureValueValidator;
    }

    /**
     * @param FeatureValue $featureValue
     * @param int $errorCode
     *
     * @return FeatureValueId
     *
     * @throws CannotAddFeatureValueException
     * @throws InvalidFeatureValueIdException
     * @throws CoreException
     */
    public function add(FeatureValue $featureValue, int $errorCode = 0): FeatureValueId
    {
        $this->featureValueValidator->validate($featureValue);
        $id = $this->addObjectModel($featureValue, CannotAddFeatureValueException::class, $errorCode);

        return new FeatureValueId($id);
    }

    /**
     * @param FeatureValue $featureValue
     *
     * @throws CannotUpdateFeatureValueException
     * @throws CoreException
     */
    public function update(FeatureValue $featureValue): void
    {
        $this->featureValueValidator->validate($featureValue);
        $this->updateObjectModel(
            $featureValue,
            CannotUpdateFeatureValueException::class
        );
    }

    /**
     * @param FeatureValueId $featureValueId
     *
     * @return FeatureValue
     *
     * @throws FeatureValueNotFoundException
     */
    public function get(FeatureValueId $featureValueId): FeatureValue
    {
        /** @var FeatureValue $featureValue */
        $featureValue = $this->getObjectModel(
            $featureValueId->getValue(),
            FeatureValue::class,
            FeatureValueNotFoundException::class
        );

        return $featureValue;
    }
}
