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

namespace Tests\Integration\PrestaShopBundle\Form;

use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\Exception\OutOfBoundsException;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;

class FormListenerTestCase extends KernelTestCase
{
    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
    }

    /**
     * @param array $data
     * @param FormInterface $form
     *
     * @return MockObject|FormEvent
     */
    protected function createEventMock(array $data, FormInterface $form)
    {
        $eventMock = $this->getMockBuilder(FormEvent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getData', 'getForm'])
            ->getMock()
        ;

        $eventMock->expects($this->once())->method('getData')->willReturn($data);
        $eventMock->expects($this->once())->method('getForm')->willReturn($form);

        return $eventMock;
    }

    /**
     * @param string $type
     * @param array $options
     * @param mixed|null $data
     *
     * @return FormInterface
     */
    protected function createForm(string $type, array $options = [], $data = null): FormInterface
    {
        return self::getContainer()->get('form.factory')->create($type, $data, $options);
    }

    /**
     * @param FormInterface $form
     * @param string $typeName
     * @param bool $shouldExist
     */
    protected function assertFormTypeExistsInForm(FormInterface $form, string $typeName, bool $shouldExist): void
    {
        if ($shouldExist) {
            $this->assertNotNull($this->getFormChild($form, $typeName));
        } else {
            $expectedException = null;
            try {
                $this->getFormChild($form, $typeName);
            } catch (OutOfBoundsException $e) {
                $expectedException = $e;
            }
            $this->assertNotNull(
                $expectedException,
                sprintf('Exception not triggered meaning the field %s is still present', $typeName)
            );
        }
    }

    /**
     * @param FormInterface $form
     * @param string $typeName
     * @param bool $shouldExist
     */
    protected function assertDataExistsInForm(FormInterface $form, string $typeName, bool $shouldExist): void
    {
        $levels = explode('.', $typeName);
        $data = $form->getData();

        if ($shouldExist) {
            foreach ($levels as $level) {
                $this->assertArrayHasKey($level, $data);
                $data = $data[$level];
            }
        } else {
            foreach ($levels as $level) {
                $data = $data[$level];
            }
            $this->assertNull($data);
        }
    }

    /**
     * @param FormInterface $form
     * @param string $typeName
     *
     * @return FormInterface
     */
    protected function getFormChild(FormInterface $form, string $typeName): FormInterface
    {
        $typeNames = explode('.', $typeName);
        $child = $form;
        foreach ($typeNames as $typeName) {
            $child = $child->get($typeName);
        }

        return $child;
    }
}
