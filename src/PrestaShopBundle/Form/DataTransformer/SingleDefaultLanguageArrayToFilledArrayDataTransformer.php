<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class SingleDefaultLanguageArrayToFilledArrayDataTransformer is responsible for filling empty array values with
 * default language value if such exists.
 */
final class SingleDefaultLanguageArrayToFilledArrayDataTransformer implements DataTransformerInterface
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
