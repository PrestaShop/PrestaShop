<?php

namespace PrestaShop\PrestaShop\Adapter\State;

use State;

class CountryStateByIsoCodeProvider
{
    /**
     * @param string $isoCode
     *
     * @return int
     */
    public function getStateIdByIsoCode(string $isoCode): int
    {
        return (int) State::getIdByIso($isoCode);
    }
}
