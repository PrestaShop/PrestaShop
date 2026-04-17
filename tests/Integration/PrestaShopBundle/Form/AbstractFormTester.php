<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
        return self::getContainer()->get('form.factory');
    }
}
