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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Form;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Abstract class with helper function for form tests
 */
abstract class AbstractFormTester extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
    }

    /**
     * @param string $type
     * @param array $options
     * @param null $data
     *
     * @return FormInterface
     */
    protected function createForm(string $type, array $options = [], $data = null): FormInterface
    {
        return $this->getFormFactory()->create($type, $data, $options);
    }

    /**
     * @param string $type
     * @param array $options
     * @param null $data
     *
     * @return FormBuilderInterface
     */
    protected function createFormBuilder(string $type, array $options = [], $data = null): FormBuilderInterface
    {
        return $this->getFormFactory()->createBuilder($type, $data, $options);
    }

    /**
     * @param string $name
     * @param string $type
     * @param array $options
     * @param null $data
     *
     * @return FormBuilderInterface
     */
    protected function createNamedBuilder(string $name, string $type, array $options = [], $data = null): FormBuilderInterface
    {
        return $this->getFormFactory()->createNamedBuilder($name, $type, $data, $options);
    }

    /**
     * @return FormFactoryInterface
     */
    protected function getFormFactory(): FormFactoryInterface
    {
        return self::$kernel->getContainer()->get('form.factory');
    }
}
