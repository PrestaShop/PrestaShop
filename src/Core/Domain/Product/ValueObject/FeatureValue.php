<?php

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

/**
 * Existing Feature value.
 */
final class FeatureValue
{

    private $featureId;

    private $featureValueId;

    /**
     * @param int $featureId
     * @param int $featureValueId
     */
    public function __construct(
        int $featureId,
        int $featureValueId
    ) {
        $this->setFeatureId($featureId);
        $this->setFeatureValueId($featureValueId);
    }

    /**
     * @return mixed
     */
    public function getFeatureId()
    {
        return $this->featureId;
    }

    /**
     * @return mixed
     */
    public function getFeatureValueId()
    {
        return $this->featureValueId;
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

    private function setFeatureValueId(int $featureValueId): void
    {
        /**
         * @todo: change me when FeatureValueId object is available.
         */
        $this->featureValueId = new class($featureValueId) {

            private $featureValueId;

            public function __construct(int $featureValueId)
            {
                $this->featureValueId = $featureValueId;
            }

            public function getValue(): int
            {
                return $this->featureValueId;
            }
        };
    }
}
