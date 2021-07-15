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

use RuntimeException;

final class FormBuilder extends \Symfony\Component\Form\FormBuilder implements FormBuilderInterface
{
    public function addBefore(string $targetFieldName, $child, $type = null, array $options = []): FormBuilderInterface
    {
        $this->assertFieldExists($targetFieldName);
        $childFormBuilders = $this->removeAllChild();

        foreach ($childFormBuilders as $childFormBuilder) {
            $this->add($childFormBuilder);

            if ($childFormBuilder->getName() === $targetFieldName) {
                $this->add($child, $type, $options);
            }
        }

        return $this;
    }

    public function addAfter(string $targetFieldName, $child, $type = null, array $options = []): FormBuilderInterface
    {
        $this->assertFieldExists($targetFieldName);
        $childFormBuilders = $this->removeAllChild();

        foreach ($childFormBuilders as $childFormBuilder) {
            if ($childFormBuilder->getName() === $targetFieldName) {
                $this->add($child, $type, $options);
            }

            $this->add($childFormBuilder);
        }

        return $this;
    }

    private function removeAllChild(): array
    {
        $childFormBuilders = [];
        foreach ($this->all() as $formBuilder) {
            $this->remove($formBuilder->getName());
            $childFormBuilders[] = $formBuilder;
        }

        return $childFormBuilders;
    }

    private function assertFieldExists(string $name): void
    {
        if ($this->has($name)) {
            return;
        }

        throw new RuntimeException(
            sprintf(
                'Form field "%s" does not exist in "%s" form',
                $name,
                $this->getName()
            )
        );
    }
}
