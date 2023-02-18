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

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\Model\ApiAccessInterface;
use PrestaShop\PrestaShop\Core\Domain\AuthorizationServer\Model\AuthorizedApplicationInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\Repository\ApiAccessRepository")
 * @ORM\Table()
 * @UniqueEntity("name")
 *
 * @experimental
 */
class ApiAccess implements ApiAccessInterface
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id_api_access", type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="client_id", type="string", length=255)
     */
    private $clientId;

    /**
     * @var string
     *
     * @ORM\Column(name="client_secret", type="string", length=255)
     */
    private $clientSecret;

    /**
     * @var AuthorizedApplicationInterface
     *
     * @ORM\ManyToOne(targetEntity=AuthorizedApplication::class)
     * @ORM\JoinColumn(name="id_authorized_application", referencedColumnName="id_authorized_application", nullable=false, onDelete="CASCADE")
     */
    private $authorizedApplication;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * @ORM\Column(name="scopes", type="array")
     */
    private $scopes = [];

    /**
     * {@inheritdoc}
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId(int $id): ApiAccessInterface
    {
        $this->id = $id;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * {@inheritdoc}
     */
    public function setClientId(string $clientId): ApiAccessInterface
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    /**
     * {@inheritdoc}
     */
    public function setClientSecret($clientSecret): ApiAccessInterface
    {
        $this->clientSecret = $clientSecret;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizedApplication(): AuthorizedApplicationInterface
    {
        return $this->authorizedApplication;
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthorizedApplication(AuthorizedApplicationInterface $authorizedApplication): ApiAccessInterface
    {
        $this->authorizedApplication = $authorizedApplication;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * {@inheritdoc}
     */
    public function setActive(bool $active): ApiAccessInterface
    {
        $this->active = $active;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getScopes(): array
    {
        return $this->scopes;
    }

    /**
     * {@inheritdoc}
     */
    public function setScopes(array $scopes): ApiAccessInterface
    {
        $this->scopes = $scopes;

        return $this;
    }
}
