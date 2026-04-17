<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Country\Validate;

use Country;
use PrestaShop\PrestaShop\Adapter\AbstractObjectModelValidator;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;

/**
 * Validates Country properties using legacy object model
 */
class CountryValidator extends AbstractObjectModelValidator
{
    public function validate(Country $country)
    {
        if (!$country->validateFields(false) || !$country->validateFieldsLang(false)) {
            throw new CountryConstraintException('Country contains invalid field values');
        }
    }
}
