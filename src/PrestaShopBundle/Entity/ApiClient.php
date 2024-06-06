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
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\InstalledApiResourceScope;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\Repository\ApiClientRepository")
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="api_client_client_id_idx", fields={"clientId", "externalIssuer"}), @ORM\UniqueConstraint(name="api_client_client_name_idx", fields={"clientName", "externalIssuer"})})
 */
#[UniqueEntity(fields: ['clientId', 'externalIssuer'], ignoreNull: false)]
#[UniqueEntity(fields: ['clientName', 'externalIssuer'], ignoreNull: false)]
class ApiClient implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     *
     * @ORM\Column(name="id_api_client", type="integer", options={"unsigned": true})
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    #[Assert\Positive]
    private int $id;

    /**
     * @ORM\Column(name="client_id", type="string", length=255)
     */
    #[Assert\Length(max: 255)]
    #[Assert\NotBlank]
    private string $clientId;

    /**
     * @ORM\Column(name="client_name", type="string", length=255)
     */
    #[Assert\Length(max: 255)]
    #[Assert\NotBlank]
    private string $clientName;

    /**
     * We make the secret nullable for the moment because it prevents the first step of the feature to be implemented.
     *
     * @ORM\Column(name="client_secret", type="string", length=255, nullable=true)
     */
    #[Assert\Length(max: 255)]
    private ?string $clientSecret = null;

    /**
     * @ORM\Column(name="enabled", type="boolean")
     */
    #[Assert\NotNull]
    private bool $enabled;

    /**
     * @ORM\Column(name="scopes", type="json")
     */
    #[Assert\NotNull]
    #[InstalledApiResourceScope]
    private array $scopes = [];

    /**
     * @ORM\Column(name="description", type="string", options={"default": ""})
     */
    #[Assert\Length(max: 21844)]
    private string $description = '';

    /**
     * @ORM\Column(name="external_issuer", type="string", nullable=true)
     */
    #[Assert\Length(max: 255)]
    private ?string $externalIssuer = null;

    /**
     * Lifetime is in milliseconds. Default value is 3600 ms.
     *
     * @ORM\Column(name="lifetime", type="integer", options={"default": "3600"})
     */
    #[Assert\NotNull]
    #[Assert\Positive]
    private int $lifetime;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function setClientId(string $clientId): static
    {
        $this->clientId = $clientId;

        return $this;
    }

    public function getClientName(): string
    {
        return $this->clientName;
    }

    public function setClientName(string $clientName): static
    {
        $this->clientName = $clientName;

        return $this;
    }

    public function getClientSecret(): ?string
    {
        return $this->clientSecret;
    }

    public function setClientSecret(?string $clientSecret): static
    {
        $this->clientSecret = $clientSecret;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getScopes(): array
    {
        return $this->scopes;
    }

    public function setScopes(array $scopes): static
    {
        $this->scopes = $scopes;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getLifetime(): int
    {
        return $this->lifetime;
    }

    public function setLifetime(int $lifetime): static
    {
        $this->lifetime = $lifetime;

        return $this;
    }

    public function getExternalIssuer(): ?string
    {
        return $this->externalIssuer;
    }

    public function setExternalIssuer(?string $externalIssuer): static
    {
        $this->externalIssuer = $externalIssuer;

        return $this;
    }

    public function getRoles(): array
    {
        return array_map(fn (string $scope): string => 'ROLE_' . strtoupper($scope), $this->getScopes());
    }

    public function getPassword(): string
    {
        return $this->getClientSecret();
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->getClientId();
    }
}
