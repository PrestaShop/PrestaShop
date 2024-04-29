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

namespace PrestaShopBundle\Entity\Employee;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use PrestaShopBundle\Entity\Lang;
use PrestaShopBundle\Security\Admin\SessionEmployeeInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\Repository\EmployeeRepository")
 *
 * @ORM\Table(
 *     indexes={
 *
 *         @ORM\Index(name="employee_login", columns={"email", "passwd"}),
 *         @ORM\Index(name="id_employee_passwd", columns={"id_employee", "passwd"}),
 *         @ORM\Index(name="id_profile", columns={"id_profile"}),
 *     },
 *  )
 */
class Employee implements UserInterface, PasswordAuthenticatedUserInterface, EquatableInterface, SessionEmployeeInterface
{
    public const ROLE_EMPLOYEE = 'ROLE_EMPLOYEE';

    /**
     * @ORM\Id
     *
     * @ORM\Column(name="id_employee", type="integer", options={"unsigned": true})
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity="PrestaShopBundle\Entity\Employee\Profile")
     *
     * @ORM\JoinColumn(name="id_profile", referencedColumnName="id_profile", nullable=false, options={"unsigned": true})
     */
    private Profile $profile;

    /**
     * @ORM\ManyToOne(targetEntity="PrestaShopBundle\Entity\Lang")
     *
     * @ORM\JoinColumn(name="id_lang", referencedColumnName="id_lang", nullable=false, options={"default": 0, "unsigned": true})
     */
    private Lang $defaultLanguage;

    /**
     * @var Collection<EmployeeSession>
     *
     * @ORM\OneToMany(targetEntity="PrestaShopBundle\Entity\Employee\EmployeeSession", mappedBy="employee", orphanRemoval=true, cascade={"persist"})
     */
    private Collection $sessions;

    /**
     * @ORM\Column(name="firstname", type="string")
     */
    private string $firstName;

    /**
     * @ORM\Column(name="lastname", type="string")
     */
    private string $lastName;

    /**
     * @ORM\Column(name="email", type="string")
     */
    private string $email;

    /**
     * @ORM\Column(name="passwd", type="string")
     */
    private string $password;

    /**
     * @ORM\Column(name="last_passwd_gen", type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private DateTime $passwordLastGeneration;

    /**
     * @ORM\Column(name="reset_password_token", type="string", length=40, nullable=true)
     */
    private ?string $resetPasswordToken;

    /**
     * @ORM\Column(name="reset_password_validity", type="datetime", nullable=true)
     */
    private ?DateTime $resetPasswordValidity;

    /**
     * @ORM\Column(name="default_tab", type="integer", options={"default": 0, "unsigned": true})
     */
    private int $defaultTabId;

    /**
     * @ORM\Column(name="active", type="boolean", options={"default": 0})
     */
    private bool $active;

    /**
     * @ORM\Column(name="last_connection_date", type="date", nullable=true)
     */
    private ?DateTime $lastConnectionDate;

    /**
     * @ORM\Column(name="has_enabled_gravatar", type="boolean", options={"default": 0})
     */
    private bool $hasEnabledGravatar;

    /**
     * @ORM\Column(name="stats_date_from", type="date", nullable=true)
     */
    private ?DateTime $statsDateFrom;

    /**
     * @ORM\Column(name="stats_date_to", type="date", nullable=true)
     */
    private ?DateTime $statsDateTo;

    /**
     * @ORM\Column(name="stats_compare_from", type="date", nullable=true)
     */
    private ?DateTime $statsCompareFrom;

    /**
     * @ORM\Column(name="stats_compare_to", type="date", nullable=true)
     */
    private ?DateTime $statsCompareTo;

    /**
     * @ORM\Column(name="stats_compare_option", type="integer", options={"default": 1, "unsigned": true})
     */
    private int $statsCompareOption;

    /**
     * @ORM\Column(name="preselect_date_range", type="string", length=32, nullable=true)
     */
    private ?string $preselectDateRange;

    /**
     * @ORM\Column(name="bo_color", type="string", length=32, nullable=true)
     */
    private ?string $boColor;

    /**
     * @ORM\Column(name="bo_theme", type="string", length=32, nullable=true)
     */
    private ?string $boTheme;

    /**
     * @ORM\Column(name="bo_css", type="string", length=64, nullable=true)
     */
    private ?string $boCss;

    /**
     * @ORM\Column(name="bo_width", type="integer", options={"default": 0, "unsigned": true})
     */
    private int $boWidth;

    /**
     * @ORM\Column(name="bo_menu", type="boolean", options={"default": 1})
     */
    private bool $boMenu;

    /**
     * @ORM\Column(name="optin", type="boolean", nullable=true)
     */
    private ?bool $optIn;

    /**
     * @ORM\Column(name="id_last_order", type="integer", options={"default": 0, "unsigned": true})
     */
    private int $lastOrderId;

    /**
     * @ORM\Column(name="id_last_customer_message", type="integer", options={"default": 0, "unsigned": true})
     */
    private int $lastCustomerMessageId;

    /**
     * @ORM\Column(name="id_last_customer", type="integer", options={"default": 0, "unsigned": true})
     */
    private int $lastCustomerId;

    public function __construct()
    {
        $this->sessions = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }

    public function getRoles(): array
    {
        return array_merge(
            [self::ROLE_EMPLOYEE],
            $this->getProfile()->getAuthorizationRoles()->map(fn (AuthorizationRole $authorizationRole) => $authorizationRole->getSlug())->toArray()
        );
    }

    public function eraseCredentials()
    {
    }

    /**
     * If you change this method you should probably also update the serialize/unserialize methods.
     *
     * @param UserInterface $user
     *
     * @return bool
     */
    public function isEqualTo(UserInterface $user): bool
    {
        return
            $user instanceof Employee
            && $user->getUserIdentifier() === $this->getUserIdentifier()
            && $user->getPassword() === $this->getPassword()
            && $user->getProfile()->getId() === $this->getProfile()->getId()
        ;
    }

    public function getProfile(): Profile
    {
        return $this->profile;
    }

    public function getProfileId(): int
    {
        return $this->getProfile()->getId();
    }

    public function setProfile(Profile $profile): static
    {
        $this->profile = $profile;

        return $this;
    }

    public function getSessions(): Collection
    {
        return $this->sessions;
    }

    public function addSession(EmployeeSession $employeeSession): static
    {
        if (!$this->sessions->contains($employeeSession)) {
            $this->sessions[] = $employeeSession;
            $employeeSession->setEmployee($this);
        }

        return $this;
    }

    public function removeSession(EmployeeSession $employeeSession): static
    {
        if ($this->sessions->contains($employeeSession)) {
            $this->sessions->removeElement($employeeSession);
            // set the owning side to null (unless already changed)
            if ($employeeSession->getEmployee() === $this) {
                $employeeSession->setEmployee(null);
            }
        }
        $this->sessions->removeElement($employeeSession);

        return $this;
    }

    public function removeSessionById(int $sessionId): static
    {
        foreach ($this->getSessions() as $session) {
            if ($session->getId() === $sessionId) {
                $this->sessions->removeElement($session);
            }
        }

        return $this;
    }

    public function hasSession(?int $sessionId, ?string $sessionToken): bool
    {
        foreach ($this->getSessions() as $session) {
            if ($session->getId() === $sessionId && $session->getToken() === $sessionToken) {
                return true;
            }
        }

        return false;
    }

    public function removeAllSessions(): static
    {
        $this->sessions->clear();

        return $this;
    }

    public function getDefaultLanguage(): ?Lang
    {
        return $this->defaultLanguage;
    }

    public function getDefaultLocale(): string
    {
        return $this->getDefaultLanguage()->getLocale();
    }

    public function setDefaultLanguageId(?Lang $defaultLanguage): static
    {
        $this->defaultLanguage = $defaultLanguage;

        return $this;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getPasswordLastGeneration(): DateTime
    {
        return $this->passwordLastGeneration;
    }

    public function setPasswordLastGeneration(DateTime $passwordLastGeneration): static
    {
        $this->passwordLastGeneration = $passwordLastGeneration;

        return $this;
    }

    public function getDefaultTabId(): int
    {
        return $this->defaultTabId;
    }

    public function setDefaultTabId(int $defaultTabId): static
    {
        $this->defaultTabId = $defaultTabId;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function getLastConnectionDate(): DateTime
    {
        return $this->lastConnectionDate;
    }

    public function setLastConnectionDate(DateTime $lastConnectionDate): static
    {
        $this->lastConnectionDate = $lastConnectionDate;

        return $this;
    }

    public function getResetPasswordToken(): ?string
    {
        return $this->resetPasswordToken;
    }

    public function setResetPasswordToken(?string $resetPasswordToken): static
    {
        $this->resetPasswordToken = $resetPasswordToken;

        return $this;
    }

    public function getResetPasswordValidity(): ?DateTime
    {
        return $this->resetPasswordValidity;
    }

    public function setResetPasswordValidity(?DateTime $resetPasswordValidity): static
    {
        $this->resetPasswordValidity = $resetPasswordValidity;

        return $this;
    }

    public function hasValidResetPasswordToken(): bool
    {
        return
            !empty($this->resetPasswordToken)
            && !empty($this->resetPasswordValidity)
            && $this->resetPasswordValidity->getTimestamp() > time()
        ;
    }

    public function isHasEnabledGravatar(): bool
    {
        return $this->hasEnabledGravatar;
    }

    public function setHasEnabledGravatar(bool $hasEnabledGravatar): static
    {
        $this->hasEnabledGravatar = $hasEnabledGravatar;

        return $this;
    }

    public function getStatsDateFrom(): ?DateTime
    {
        return $this->statsDateFrom;
    }

    public function setStatsDateFrom(?DateTime $statsDateFrom): static
    {
        $this->statsDateFrom = $statsDateFrom;

        return $this;
    }

    public function getStatsDateTo(): ?DateTime
    {
        return $this->statsDateTo;
    }

    public function setStatsDateTo(?DateTime $statsDateTo): static
    {
        $this->statsDateTo = $statsDateTo;

        return $this;
    }

    public function getStatsCompareFrom(): ?DateTime
    {
        return $this->statsCompareFrom;
    }

    public function setStatsCompareFrom(?DateTime $statsCompareFrom): static
    {
        $this->statsCompareFrom = $statsCompareFrom;

        return $this;
    }

    public function getStatsCompareTo(): ?DateTime
    {
        return $this->statsCompareTo;
    }

    public function setStatsCompareTo(?DateTime $statsCompareTo): static
    {
        $this->statsCompareTo = $statsCompareTo;

        return $this;
    }

    public function getStatsCompareOption(): int
    {
        return $this->statsCompareOption;
    }

    public function setStatsCompareOption(int $statsCompareOption): static
    {
        $this->statsCompareOption = $statsCompareOption;

        return $this;
    }

    public function getPreselectDateRange(): ?string
    {
        return $this->preselectDateRange;
    }

    public function setPreselectDateRange(?string $preselectDateRange): static
    {
        $this->preselectDateRange = $preselectDateRange;

        return $this;
    }

    public function getBoColor(): ?string
    {
        return $this->boColor;
    }

    public function setBoColor(?string $boColor): static
    {
        $this->boColor = $boColor;

        return $this;
    }

    public function getBoTheme(): ?string
    {
        return $this->boTheme;
    }

    public function setBoTheme(?string $boTheme): static
    {
        $this->boTheme = $boTheme;

        return $this;
    }

    public function getBoCss(): ?string
    {
        return $this->boCss;
    }

    public function setBoCss(?string $boCss): static
    {
        $this->boCss = $boCss;

        return $this;
    }

    public function getBoWidth(): int
    {
        return $this->boWidth;
    }

    public function setBoWidth(int $boWidth): static
    {
        $this->boWidth = $boWidth;

        return $this;
    }

    public function isBoMenu(): bool
    {
        return $this->boMenu;
    }

    public function setBoMenu(bool $boMenu): static
    {
        $this->boMenu = $boMenu;

        return $this;
    }

    public function getOptIn(): ?bool
    {
        return $this->optIn;
    }

    public function setOptIn(?bool $optIn): static
    {
        $this->optIn = $optIn;

        return $this;
    }

    public function getLastOrderId(): int
    {
        return $this->lastOrderId;
    }

    public function setLastOrderId(int $lastOrderId): static
    {
        $this->lastOrderId = $lastOrderId;

        return $this;
    }

    public function getLastCustomerMessageId(): int
    {
        return $this->lastCustomerMessageId;
    }

    public function setLastCustomerMessageId(int $lastCustomerMessageId): static
    {
        $this->lastCustomerMessageId = $lastCustomerMessageId;

        return $this;
    }

    public function getLastCustomerId(): int
    {
        return $this->lastCustomerId;
    }

    public function setLastCustomerId(int $lastCustomerId): static
    {
        $this->lastCustomerId = $lastCustomerId;

        return $this;
    }

    /**
     * Optimize the way the employee is serialized in the session, it is important to return
     * all the required info to later check that the serialized data is equal to the Employee
     * in DB (including the profile). If you change the isEqualTo method you should probably
     * update this serialization as well.
     */
    public function __serialize(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'password' => $this->password,
            'profileId' => $this->getProfile()->getId(),
            // This is added in the serialized data so that early listeners can get the value from session early
            'defaultLocale' => $this->getDefaultLocale(),
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->id = $data['id'] ?? 0;
        $this->email = $data['email'] ?? '';
        $this->password = $data['password'] ?? '';
        $this->profile = new Profile($data['profileId'] ?? 0);
        $this->defaultLanguage = new Lang();
        $this->defaultLanguage->setLocale($data['defaultLocale'] ?? 'en');
    }
}
