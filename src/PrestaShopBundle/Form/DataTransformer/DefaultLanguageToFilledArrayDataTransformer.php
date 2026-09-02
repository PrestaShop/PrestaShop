<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class DefaultLanguageToFilledArrayDataTransformer is responsible for filling empty array values with
 * default language value if such exists.
 */
final class DefaultLanguageToFilledArrayDataTransformer implements DataTransformerInterface
{
    /**
     * @var int
     */
    private $defaultLanguageId;

    /**
     * @param int $defaultLanguageId
     */
    public function __construct($defaultLanguageId)
    {
        $this->defaultLanguageId = $defaultLanguageId;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        // No transformation is required here due to this data is being sent to template
        return $value;
    }

    /**
     * {@inheritdoc}
     *
     * @param array $values
     */
    public function reverseTransform($values)
    {
        if (!$this->assertIsValidForDataTransforming($values)) {
            return $values;
        }

        $defaultValue = $values[$this->defaultLanguageId];
        foreach ($values as $languageId => $item) {
            if (!$item) {
                $values[$languageId] = $defaultValue;
            }
        }

        return $values;
    }

    /**
     * Checks if the value is array and default language key exists in array.
     *
     * @param array $values
     *
     * @return bool
     */
    private function assertIsValidForDataTransforming($values)
    {
        return is_array($values) && isset($values[$this->defaultLanguageId]) && $values[$this->defaultLanguageId];
    }
}
