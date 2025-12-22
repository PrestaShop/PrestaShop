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

namespace PrestaShop\PrestaShop\Adapter\Employee;

use Employee;
use PhpEncryption;
use PrestaShop\PrestaShop\Core\Employee\ContextEmployeeProviderInterface;

/**
 * Class ContextEmployeeProvider provides context employee data.
 */
final class ContextEmployeeProvider implements ContextEmployeeProviderInterface
{
    private ?Employee $contextEmployee;
    private string $twoFactorSecret;

    /**
     * @param ?Employee $contextEmployee
     */
    public function __construct(
        ?Employee $contextEmployee,
        private string $newCookieKey
    ) {
        $this->contextEmployee = $contextEmployee;

        if ($contextEmployee->two_factor_secret) {
            $cipherTool = new PhpEncryption($this->newCookieKey);
            $this->twoFactorSecret = $cipherTool->decrypt($contextEmployee->two_factor_secret);
        } else {
             $this->twoFactorSecret = '';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isSuperAdmin()
    {
        return $this->contextEmployee && $this->contextEmployee->isSuperAdmin();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return (int) $this->contextEmployee?->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getLanguageId()
    {
        return (int) $this->contextEmployee?->id_lang;
    }

    /**
     * {@inheritdoc}
     */
    public function getProfileId()
    {
        return (int) $this->contextEmployee?->id_profile;
    }

    /**
     * {@inheritdoc}
     */
    public function hasTwoFactorSecret(): bool
    {
        $secret = $this->contextEmployee?->two_factor_secret;

        return is_string($secret) && trim($secret) !== '';
    }

    /**
     * {@inheritdoc}
     */
    public function getTwoFactorSecret(): string
    {
        return $this->twoFactorSecret;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return [
            'id' => (int) $this->contextEmployee?->id,
            'profileId' => (int) $this->contextEmployee?->id_profile,
            'languageId' => (int) $this->contextEmployee?->id_lang,
            'firstname' => $this->contextEmployee?->firstname,
            'lastname' => $this->contextEmployee?->lastname,
            'email' => $this->contextEmployee?->email,
        ];
    }
}
