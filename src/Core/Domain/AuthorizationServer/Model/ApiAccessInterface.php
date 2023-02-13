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

namespace PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\Model;

/**
 * @experimental
 */
interface ApiAccessInterface
{
    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId(int $id): ApiAccessInterface;

    /**
     * @return string
     */
    public function getClientId(): string;

    /**
     * @param string $clientId
     *
     * @return $this
     */
    public function setClientId(string $clientId): ApiAccessInterface;

    /**
     * @return string
     */
    public function getClientSecret(): string;

    /**
     * @param $clientSecret
     *
     * @return $this
     */
    public function setClientSecret($clientSecret): ApiAccessInterface;

    /**
     * @return AuthorizedApplicationInterface
     */
    public function getAuthorizedApplication(): AuthorizedApplicationInterface;

    /**
     * @param AuthorizedApplicationInterface $authorizedApplication
     *
     * @return $this
     */
    public function setAuthorizedApplication(AuthorizedApplicationInterface $authorizedApplication): ApiAccessInterface;

    /**
     * @return bool
     */
    public function isActive(): bool;

    /**
     * @param bool $active
     *
     * @return $this
     */
    public function setActive(bool $active): ApiAccessInterface;

    /**
     * @return array
     */
    public function getScopes(): array;

    /**
     * @param array $scopes
     *
     * @return $this
     */
    public function setScopes(array $scopes): ApiAccessInterface;
}
