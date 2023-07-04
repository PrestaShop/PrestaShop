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

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\Repository\FeatureFlagRepository")
 * @ORM\Table()
 * @UniqueEntity("name")
 * @ApiResource()
 */
class FeatureFlag
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id_feature_flag", type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=191, unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=64, options={"default": FeatureFlagSettings::TYPE_DEFAULT})
     */
    private $type;

    /**
     * @var bool
     *
     * @ORM\Column(name="state", type="boolean", options={"default":0, "unsigned":true})
     */
    private $state;

    /**
     * @var string
     *
     * @ORM\Column(name="label_wording", type="string", length=512, options={"default":""})
     */
    private $labelWording;

    /**
     * @var string
     *
     * @ORM\Column(name="label_domain", type="string", length=255, options={"default":""})
     */
    private $labelDomain;

    /**
     * @var string
     *
     * @ORM\Column(name="description_wording", type="string", length=512, options={"default":""})
     */
    private $descriptionWording;

    /**
     * @var string
     *
     * @ORM\Column(name="description_domain", type="string", length=255, options={"default":""})
     */
    private $descriptionDomain;

    /**
     * @var string
     *
     * @ORM\Column(name="stability", type="string", length=64, options={"default":"beta"})
     */
    private $stability;

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

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->state;
    }

    /**
     * @return self
     */
    public function disable(): self
    {
        $this->state = false;

        return $this;
    }

    /**
     * @return self
     */
    public function enable(): self
    {
        $this->state = true;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabelWording(): string
    {
        return $this->labelWording;
    }

    /**
     * @param string $labelWording
     *
     * @return self
     */
    public function setLabelWording(string $labelWording): self
    {
        $this->labelWording = $labelWording;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabelDomain(): string
    {
        return $this->labelDomain;
    }

    /**
     * @param string $labelDomain
     *
     * @return self
     */
    public function setLabelDomain(string $labelDomain): self
    {
        $this->labelDomain = $labelDomain;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescriptionWording(): string
    {
        return $this->descriptionWording;
    }

    /**
     * @param string $descriptionWording
     *
     * @return self
     */
    public function setDescriptionWording(string $descriptionWording): self
    {
        $this->descriptionWording = $descriptionWording;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescriptionDomain(): string
    {
        return $this->descriptionDomain;
    }

    /**
     * @param string $descriptionDomain
     *
     * @return self
     */
    public function setDescriptionDomain(string $descriptionDomain): self
    {
        $this->descriptionDomain = $descriptionDomain;

        return $this;
    }

    /**
     * @return string
     */
    public function getStability(): string
    {
        return $this->stability;
    }

    /**
     * @param string $stability
     *
     * @return self
     */
    public function setStability(string $stability): self
    {
        $this->stability = $stability;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Retrieve order of feature flags type
     *
     * @return array
     */
    public function getOrderedTypes(): array
    {
        return explode(',', $this->type);
    }

    /**
     * @param string $type
     *
     * @return self
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
