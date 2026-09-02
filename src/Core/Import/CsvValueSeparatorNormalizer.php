<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import;

/**
 * Class CsvValueSeparatorNormalizer normalizes import separator before usage.
 */
final class CsvValueSeparatorNormalizer implements StringNormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function normalize($value)
    {
        $value = trim($value);
        $value = substr($value, 0, 1);

        return $value ?: ImportSettings::DEFAULT_SEPARATOR;
    }
}
