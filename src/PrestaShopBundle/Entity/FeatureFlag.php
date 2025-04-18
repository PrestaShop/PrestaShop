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
use InvalidArgumentException;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\Repository\FeatureFlagRepository")
 *
 * @ORM\Table()
 *
 * @UniqueEntity("name")
 */
class FeatureFlag
{
    /**
     * @ORM\Id
     *
     * @ORM\Column(name="id_feature_flag", type="integer", options={"unsigned":true})
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(name="name", type="string", length=191, unique=true)
     */
    private string $name;

    /**
     * @ORM\Column(name="type", type="string", length=64, options={"default": FeatureFlagSettings::TYPE_DEFAULT})
     */
    private string $type;

    /**
     * @ORM\Column(name="state", type="boolean", options={"default":0, "unsigned":true})
     */
    private bool $state;

    /**
     * @ORM\Column(name="label_wording", type="string", length=191, options={"default":""})
     */
    private string $labelWording;

    /**
     * @ORM\Column(name="label_domain", type="string", length=255, options={"default":""})
     */
    private string $labelDomain;

    /**
     * @ORM\Column(name="description_wording", type="string", length=191, options={"default":""})
     */
    private string $descriptionWording;

    /**
     * @ORM\Column(name="description_domain", type="string", length=255, options={"default":""})
     */
    private string $descriptionDomain;

    /**
     * @ORM\Column(name="stability", type="string", length=64, options={"default":"beta"})
     */
    private string $stability;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        if ('' === $name) {
            throw new InvalidArgumentException('Feature flag name cannot be empty');
        }
        $this->name = $name;
        $this->type = FeatureFlagSettings::TYPE_DEFAULT;
        $this->state = false;
        $this->descriptionWording = '';
        $this->descriptionDomain = '';
        $this->labelWording = '';
        $this->labelDomain = '';
        $this->stability = FeatureFlagSettings::STABILITY_BETA;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isEnabled(): bool
    {
        return $this->state;
    }

    public function disable(): static
    {
        $this->state = false;

        return $this;
    }

    public function enable(): static
    {
        $this->state = true;

        return $this;
    }

    public function getLabelWording(): string
    {
        return $this->labelWording;
    }

    public function setLabelWording(string $labelWording): static
    {
        $this->labelWording = $labelWording;

        return $this;
    }

    public function getLabelDomain(): string
    {
        return $this->labelDomain;
    }

    public function setLabelDomain(string $labelDomain): static
    {
        $this->labelDomain = $labelDomain;

        return $this;
    }

    public function getDescriptionWording(): string
    {
        return $this->descriptionWording;
    }

    public function setDescriptionWording(string $descriptionWording): static
    {
        $this->descriptionWording = $descriptionWording;

        return $this;
    }

    public function getDescriptionDomain(): string
    {
        return $this->descriptionDomain;
    }

    public function setDescriptionDomain(string $descriptionDomain): static
    {
        $this->descriptionDomain = $descriptionDomain;

        return $this;
    }

    public function getStability(): string
    {
        return $this->stability;
    }

    public function setStability(string $stability): static
    {
        $this->stability = $stability;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getOrderedTypes(): array
    {
        return explode(',', $this->type);
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }
}
