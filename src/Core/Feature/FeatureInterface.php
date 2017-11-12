<?php

namespace PrestaShop\PrestaShop\Core\Feature;

/**
 * Defines how we should access to a feature
 */
interface FeatureInterface
{
    /**
     * @return bool
     */
    public function isUsed();

    /**
     * @return bool
     */
    public function isActive();

    /**
     * @return void
     */
    public function enable();

    /**
     * @return void
     */
    public function disable();

    /**
     * @param $status bool
     * @return void
     */
    public function update($status);
}
