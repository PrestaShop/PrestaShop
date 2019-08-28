<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Country\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Country\AbstractCountryHandler;
use PrestaShop\PrestaShop\Adapter\Country\CountryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Country\Query\GetCountryZipCodeRequirements;
use PrestaShop\PrestaShop\Core\Domain\Country\QueryHandler\GetCountryZipCodeRequirementsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\QueryResult\CountryZipCodeRequirements;

/**
 * Handles getting requirements for country zip code
 */
final class GetCountryZipCodeRequirementsHandler extends AbstractCountryHandler implements GetCountryZipCodeRequirementsHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CountryNotFoundException
     */
    public function handle(GetCountryZipCodeRequirements $query): CountryZipCodeRequirements
    {
        $country = $this->getCountry($query->getCountryId());
        $requirements = new CountryZipCodeRequirements(
            $country->need_zip_code
        );

        if ($country->need_zip_code && !empty($country->zip_code_format)) {
            $zipRegexp = '/^' . $country->zip_code_format . '$/ui';
            $zipRegexp = str_replace('N', '[0-9]', $zipRegexp);
            $zipRegexp = str_replace('L', '[a-zA-Z]', $zipRegexp);
            $zipRegexp = str_replace('C', $country->iso_code, $zipRegexp);

            $pattern = $zipRegexp;

            $humanReadablePattern = str_replace(
                'C',
                $country->iso_code,
                str_replace('N', '0', str_replace('L', 'A', $country->zip_code_format))
            );

            $requirements->setPatterns($pattern, $humanReadablePattern);
        }

        return $requirements;
    }
}
