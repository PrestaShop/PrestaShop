<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Currency\Command;

use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\AlphaIsoCode;

class EditUnofficialCurrencyCommand extends AbstractEditCurrencyCommand
{
    private ?AlphaIsoCode $isoCode = null;

    public function getIsoCode(): ?AlphaIsoCode
    {
        return $this->isoCode;
    }

    /**
     * @throws CurrencyConstraintException
     */
    public function setIsoCode(string $isoCode): self
    {
        $this->isoCode = new AlphaIsoCode($isoCode);

        return $this;
    }
}
