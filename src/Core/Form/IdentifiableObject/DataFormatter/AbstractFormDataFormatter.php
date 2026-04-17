<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataFormatter;

use PrestaShop\PrestaShop\Core\Util\String\ModifyAllShopsUtil;
use Symfony\Component\PropertyAccess\Exception\AccessException;
use Symfony\Component\PropertyAccess\PropertyAccess;

abstract class AbstractFormDataFormatter
{
    /**
     * @var string
     */
    protected $modifyAllNamePrefix;

    public function __construct(
        string $modifyAllNamePrefix = ''
    ) {
        $this->modifyAllNamePrefix = $modifyAllNamePrefix;
    }

    protected function formatByPath(array $formData, array $pathAssociations): array
    {
        // @todo: a hook system should be integrated in this formatter abstract class for extendability
        $formattedData = [];

        $propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->enableExceptionOnInvalidIndex()
            ->enableExceptionOnInvalidPropertyPath()
            ->disableMagicCall()
            ->getPropertyAccessor()
        ;
        foreach ($pathAssociations as $bulkFormPath => $editFormPath) {
            try {
                $bulkValue = $propertyAccessor->getValue($formData, $bulkFormPath);
                $propertyAccessor->setValue($formattedData, $editFormPath, $bulkValue);
            } catch (AccessException) {
                // When the bulk data is not found it means the field was disabled, which is the expected behaviour
                // as the bulk request is a partial request not every data is expected And when it's not present
                // it means there is no modification to do so this field is simply ignored
            }

            try {
                $modifyAllShopsPath = ModifyAllShopsUtil::prefixFieldPathWithAllShops($bulkFormPath, $this->modifyAllNamePrefix);
                $modifyAllShopsValue = $propertyAccessor->getValue($formData, $modifyAllShopsPath);
                $propertyAccessor->setValue(
                    $formattedData,
                    ModifyAllShopsUtil::prefixFieldPathWithAllShops($editFormPath, $this->modifyAllNamePrefix),
                    $modifyAllShopsValue
                );
            } catch (AccessException) {
                // this means the field does not have related modify_all_shops field, so it is not multiShop field
                // therefore we don't need to re-format its related modify_all_shops field name
            }
        }

        return $formattedData;
    }
}
