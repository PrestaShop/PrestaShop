<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Administration;

use Cookie;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use PrestaShopBundle\Form\Exception\DataProviderException;
use PrestaShopBundle\Form\Exception\InvalidConfigurationDataError;
use PrestaShopBundle\Form\Exception\InvalidConfigurationDataErrorCollection;

/**
 * This class is responsible of managing the data manipulated using general form
 * in "Configure > Advanced Parameters > Administration" page.
 */
final class GeneralDataProvider implements FormDataProviderInterface
{
    /**
     * If you set cookie lifetime value too high there can be multiple problems.
     * Hours are converted to seconds, so int might be turned to float if it's way to high.
     * Cookie classes crash if lifetime goes beyond year 9999, there are probably multiple other things.
     * So we need to set some sort of max value. 100 years seems like a lifetime beyond reasonable use.
     */
    public const MAX_COOKIE_VALUE = 876000;

    /**
     * @var DataConfigurationInterface
     */
    private $dataConfiguration;

    /**
     * @var bool
     */
    private $sslEnabled;

    /**
     * @var bool
     */
    private $sslEnabledEverywhere;

    public function __construct(
        DataConfigurationInterface $dataConfiguration,
        bool $sslEnabled,
        bool $sslEnabledEverywhere
    ) {
        $this->dataConfiguration = $dataConfiguration;
        $this->sslEnabled = $sslEnabled;
        $this->sslEnabledEverywhere = $sslEnabledEverywhere;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->dataConfiguration->getConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        $this->validate($data);

        return $this->dataConfiguration->updateConfiguration($data);
    }

    /**
     * Perform validations on form data.
     *
     * @param array $data
     */
    private function validate(array $data): void
    {
        $errors = new InvalidConfigurationDataErrorCollection();
        if (isset($data[GeneralType::FIELD_FRONT_COOKIE_LIFETIME])) {
            $frontOfficeLifeTimeCookie = $data[GeneralType::FIELD_FRONT_COOKIE_LIFETIME];
            if (!is_numeric($frontOfficeLifeTimeCookie) || $frontOfficeLifeTimeCookie < 0) {
                $errors->add(new InvalidConfigurationDataError(FormDataProvider::ERROR_NOT_NUMERIC_OR_LOWER_THAN_ZERO, GeneralType::FIELD_FRONT_COOKIE_LIFETIME));
            }

            if ($frontOfficeLifeTimeCookie > self::MAX_COOKIE_VALUE) {
                $errors->add(new InvalidConfigurationDataError(FormDataProvider::ERROR_COOKIE_LIFETIME_MAX_VALUE_EXCEEDED, GeneralType::FIELD_FRONT_COOKIE_LIFETIME));
            }
        }

        if (isset($data[GeneralType::FIELD_BACK_COOKIE_LIFETIME])) {
            $backOfficeLifeTimeCookie = $data[GeneralType::FIELD_BACK_COOKIE_LIFETIME];
            if (!is_numeric($backOfficeLifeTimeCookie) || $backOfficeLifeTimeCookie < 0) {
                $errors->add(new InvalidConfigurationDataError(FormDataProvider::ERROR_NOT_NUMERIC_OR_LOWER_THAN_ZERO, GeneralType::FIELD_BACK_COOKIE_LIFETIME));
            }

            if ($backOfficeLifeTimeCookie > self::MAX_COOKIE_VALUE) {
                $errors->add(new InvalidConfigurationDataError(FormDataProvider::ERROR_COOKIE_LIFETIME_MAX_VALUE_EXCEEDED, GeneralType::FIELD_BACK_COOKIE_LIFETIME));
            }
        }

        if (isset($data[GeneralType::FIELD_COOKIE_SAMESITE])) {
            if (!$this->validateSameSite($data[GeneralType::FIELD_COOKIE_SAMESITE])) {
                $errors->add(new InvalidConfigurationDataError(FormDataProvider::ERROR_COOKIE_SAMESITE_NONE, GeneralType::FIELD_COOKIE_SAMESITE));
            }
        }

        if (!$errors->isEmpty()) {
            throw new DataProviderException('Administration general data is invalid', 0, null, $errors);
        }
    }

    /**
     * Validate SameSite.
     * The SameSite=None is only working when Secure is settled
     *
     * @param string $sameSite
     *
     * @return bool
     */
    protected function validateSameSite(string $sameSite): bool
    {
        if ($sameSite === Cookie::SAMESITE_NONE) {
            return $this->sslEnabled && $this->sslEnabledEverywhere;
        }

        return true;
    }
}
