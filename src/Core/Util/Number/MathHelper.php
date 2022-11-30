<?php

namespace PrestaShop\PrestaShop\Core\Util\Number;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;

class MathHelper
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var int|null
     */
    private $defaultRoundMode = null;

    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    public function round(float $value, int $precision = 0, ?int $roundMode = null): float
    {
        if (null === $roundMode) {
            if (null === $this->defaultRoundMode) {
                $this->defaultRoundMode = (int) $this->configuration->get('PS_PRICE_ROUND_MODE');
            }

            return Math::round($value, $precision, $this->defaultRoundMode);
        }

        return Math::round($value, $precision, $roundMode);
    }
}
