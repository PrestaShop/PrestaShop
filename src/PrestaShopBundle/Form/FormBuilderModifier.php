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
