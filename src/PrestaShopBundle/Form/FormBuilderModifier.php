<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form;

use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Form\FormBuilderInterface;

class FormBuilderModifier
{
    /**
     * @param FormBuilderInterface $formBuilder
     * @param string $targetFieldName
     * @param string|FormBuilderInterface $newChild
     * @param string|null $type
     * @param array $options
     */
    public function addAfter(FormBuilderInterface $formBuilder, string $targetFieldName, $newChild, ?string $type = null, array $options = []): void
    {
        $this->assertFieldExists($formBuilder, $targetFieldName);
        $formChildren = $this->cleanAllChildren($formBuilder);

        foreach ($formChildren as $childName => $formType) {
            $formBuilder->add($formType);
            if ($childName === $targetFieldName) {
                $formBuilder->add($newChild, $type, $options);
            }
        }
    }

    /**
     * @param FormBuilderInterface $formBuilder
     * @param string $targetFieldName
     * @param string|FormBuilderInterface $newChild
     * @param string|null $type
     * @param array $options
     */
    public function addBefore(FormBuilderInterface $formBuilder, string $targetFieldName, $newChild, ?string $type = null, array $options = []): void
    {
        $this->assertFieldExists($formBuilder, $targetFieldName);
        $formChildren = $this->cleanAllChildren($formBuilder);

        foreach ($formChildren as $childName => $formType) {
            if ($childName === $targetFieldName) {
                $formBuilder->add($newChild, $type, $options);
            }
            $formBuilder->add($formType);
        }
    }

    /**
     * @param FormBuilderInterface $formBuilder
     *
     * @return array
     */
    private function cleanAllChildren(FormBuilderInterface $formBuilder): array
    {
        $formTypes = [];
        foreach ($formBuilder->all() as $formType) {
            $typeName = $formType->getName();
            // collect all the form child into local variable and remove them from
            $formTypes[$typeName] = $formType;
            $formBuilder->remove($typeName);
        }

        return $formTypes;
    }

    /**
     * @param FormBuilderInterface $formBuilder
     * @param string $name
     */
    private function assertFieldExists(FormBuilderInterface $formBuilder, string $name): void
    {
        if ($formBuilder->has($name)) {
            return;
        }

        throw new InvalidArgumentException(
            sprintf(
                'Form field "%s" does not exist in "%s" form',
                $name,
                $formBuilder->getName()
            )
        );
    }
}
