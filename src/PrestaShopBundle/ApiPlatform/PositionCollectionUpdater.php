<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\ApiPlatform;

use PrestaShopBundle\ApiPlatform\Metadata\PositionCollection;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;

/**
 * Used to adapt the data from array of positions, so far all it needs to do is transform
 * ['attributeId' => 5] into ['rowId' => 5]. To do that it detects the array properties with
 * the PositionCollection attribute.
 */
class PositionCollectionUpdater
{
    public function __construct(
        protected ClassMetadataFactoryInterface $classMetadataFactory,
    ) {
    }

    public function normalizePositionCollection(array $normalizedData, string $type): array
    {
        if (!$this->classMetadataFactory->hasMetadataFor($type)) {
            return $normalizedData;
        }

        $metadata = $this->classMetadataFactory->getMetadataFor($type);
        foreach ($metadata->getAttributesMetadata() as $attributeMetadata) {
            $attributeContext = $attributeMetadata->getDenormalizationContexts()['*'] ?? [];
            // This is not an attribute with PositionCollection attribute
            if (!isset($attributeContext[PositionCollection::ROW_ID_FIELD])) {
                continue;
            }

            // The associated data is not set or is not an array
            if (!isset($normalizedData[$attributeMetadata->getName()]) || !is_array($normalizedData[$attributeMetadata->getName()])) {
                continue;
            }

            $rowIdField = $attributeContext[PositionCollection::ROW_ID_FIELD];
            // Default name no need to adapt
            if ($rowIdField === PositionCollection::DEFAULT_ROW_ID_FIELD) {
                continue;
            }

            // Loop through all position updates, and replace the ID field with homogenized rowId expected by the GridPositionUpdater
            // example ['attributeId' => 5] becomes ['rowId' => 5]
            foreach (array_keys($normalizedData[$attributeMetadata->getName()]) as $positionIndex) {
                $normalizedData[$attributeMetadata->getName()][$positionIndex][PositionCollection::DEFAULT_ROW_ID_FIELD] = $normalizedData[$attributeMetadata->getName()][$positionIndex][$rowIdField];
                unset($normalizedData[$attributeMetadata->getName()][$positionIndex][$rowIdField]);
            }
        }

        return $normalizedData;
    }
}
