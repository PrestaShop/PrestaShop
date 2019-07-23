<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;

final class CustomizableFeatureValue
{
    public const MAX_SIZE = 255;

    private $featureId;

    /**
     * @param int $featureId
     * @param string $featureValue
     *
     * @throws ProductConstraintException
     */
    public function __construct(int $featureId, string $featureValue)
    {
        $this->setFeatureId($featureId);

        $this->assertValueNameIsValid($featureValue);
        $this->assertValueSizeIsValid($featureValue);
    }

    private function setFeatureId(int $featureId): void
    {
        /**
         * @todo: change me when FeatureId object is available.
         */
        $this->featureId = new class($featureId) {

            private $featureId;

            public function __construct(int $featureId)
            {
                $this->featureId = $featureId;
            }

            public function getValue(): int
            {
                return $this->featureId;
            }
        };
    }

    /**
     * @param string $featureValue
     *
     * @throws ProductConstraintException
     */
    private function assertValueNameIsValid(string $featureValue): void
    {
        $pattern = '/^[^<>={}]*$/u';
        if (!preg_match($pattern, $featureValue)) {
            throw new ProductConstraintException(
                sprintf(
                    'Customizable feature value name "%s" did not matched given regex pattern "%s"',
                    $featureValue,
                    $pattern
                ),
                ProductConstraintException::INVALID_CUSTOMIZABLE_FEATURE_VALUE
            );
        }
    }

    /**
     * @param string $featureValue
     *
     * @throws ProductConstraintException
     */
    private function assertValueSizeIsValid(string $featureValue): void
    {
        if (strlen($featureValue) > self::MAX_SIZE) {
            throw new ProductConstraintException(
                sprintf(
                    'Customizable feature value name "%s" is too long. Max size is %d',
                    $featureValue,
                    self::MAX_SIZE
                ),
                ProductConstraintException::CUSTOMIZABLE_FEATURE_VALUE_TOO_LONG
            );
        }
    }
}
