<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\EntityField\Provider;

use PrestaShop\PrestaShop\Core\Import\Exception\NotSupportedImportEntityException;

/**
 * Class EntityFieldsProviderFinder defines an entity fields provider finder.
 */
final class EntityFieldsProviderFinder implements EntityFieldsProviderFinderInterface
{
    /**
     * @var array of entity fields providers
     */
    private $entityFieldsProviders;

    /**
     * @param array $entityFieldsProviders
     */
    public function __construct(array $entityFieldsProviders)
    {
        $this->entityFieldsProviders = $entityFieldsProviders;
    }

    /**
     * {@inheritdoc}
     */
    public function find($importEntity)
    {
        if (!isset($this->entityFieldsProviders[$importEntity])) {
            throw new NotSupportedImportEntityException("Entity fields provider does not exist for entity $importEntity.");
        }

        return $this->entityFieldsProviders[$importEntity];
    }
}
