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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 *
 * @ORM\Table
 */
class Profile
{
    public const ADMIN_PROFILE_ID = 1;

    /**
     * @ORM\Id
     *
     * @ORM\Column(name="id_profile", type="integer", options={"unsigned": true})
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToMany(targetEntity="PrestaShopBundle\Entity\Employee\AuthorizationRole")
     *
     * @ORM\JoinTable(
     *     options={"ps_table"="access"},
     *     joinColumns={@ORM\JoinColumn(name="id_profile", referencedColumnName="id_profile")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="id_authorization_role", referencedColumnName="id_authorization_role")}
     *  )
     */
    private Collection $authorizationRoles;

    public function __construct(?int $id = null)
    {
        $this->id = $id;
        $this->authorizationRoles = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function isAdmin(): bool
    {
        return $this->id === self::ADMIN_PROFILE_ID;
    }

    public function getAuthorizationRoles(): Collection
    {
        return $this->authorizationRoles;
    }
}
