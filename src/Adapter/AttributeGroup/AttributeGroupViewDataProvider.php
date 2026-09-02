<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\AttributeGroup;

use AttributeGroup;
use PrestaShop\PrestaShop\Core\AttributeGroup\AttributeGroupViewDataProviderInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Exception\AttributeGroupException;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Exception\AttributeGroupNotFoundException;
use PrestaShopException;

/**
 * Provides data required for attribute group view action using legacy object models
 */
final class AttributeGroupViewDataProvider implements AttributeGroupViewDataProviderInterface
{
    /**
     * @var int
     */
    private $contextLangId;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param int $contextLangId
     * @param ConfigurationInterface $configuration
     */
    public function __construct($contextLangId, ConfigurationInterface $configuration)
    {
        $this->contextLangId = $contextLangId;
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function isColorGroup($attributeGroupId)
    {
        $attributeGroup = $this->getAttributeGroupById($attributeGroupId);

        return (bool) $attributeGroup->is_color_group;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeGroupNameById($attributeGroupId)
    {
        $attributeGroup = $this->getAttributeGroupById($attributeGroupId);

        if (!isset($attributeGroup->name[$this->contextLangId])) {
            return $attributeGroup->name[$this->configuration->get('PS_LANG_DEFAULT')];
        }

        return $attributeGroup->name[$this->contextLangId];
    }

    /**
     * Gets legacy AttributeGroup object by provided id
     *
     * @param int $attributeGroupId
     *
     * @return AttributeGroup
     *
     * @throws AttributeGroupException
     * @throws AttributeGroupNotFoundException
     */
    private function getAttributeGroupById($attributeGroupId)
    {
        try {
            $attributeGroup = new AttributeGroup($attributeGroupId);

            if ($attributeGroup->id !== $attributeGroupId) {
                throw new AttributeGroupNotFoundException(sprintf('Attribute group with id "%s" was not found.', $attributeGroupId));
            }
        } catch (PrestaShopException) {
            throw new AttributeGroupException(sprintf('An error occurred when trying to get attribute group with id %s', $attributeGroupId));
        }

        return $attributeGroup;
    }
}
