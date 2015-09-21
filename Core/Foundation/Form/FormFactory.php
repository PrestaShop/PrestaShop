<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Foundation\Form;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Extension\Csrf\CsrfExtension;
use Symfony\Component\Form\Extension\Csrf\CsrfProvider\DefaultCsrfProvider;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;

/**
 * FormFactory
 *
 * This factory class is responsible to create a SF2 Form Factory Builder
 * Also add 3 extensions :
 * - ValidatorExtension : for SF2 form validation
 * - ValidatorExtension : for SF2 CSRF form validation/protection
 * - HttpFoundationExtension : for SF2 form request handle
 */
class FormFactory
{
    private $builder;

    /**
     * Constructor
     *
     * Create a SF2 form factory builder
     */
    public function __construct()
    {
        // Set up the CSRF provider
        $csrfProvider = new DefaultCsrfProvider(_COOKIE_KEY_);

        // Set up the Form component
        $this->builder = Forms::createFormFactoryBuilder()
            ->addExtension(new ValidatorExtension(Validation::createValidator()))
            ->addExtension(new CsrfExtension($csrfProvider))
            ->addExtension(new HttpFoundationExtension())
            ->getFormFactory();

        return $this->builder;
    }

    /**
     * Return a form builder
     *
     * @param string $name The name of the form
     * @param array $datas The initial data
     * @param array $options The form options
     *
     * @return FormBuilderInterface The form builder
     */
    public function createBuilder($name = 'form', $datas = null, $options = array())
    {
        return $this->builder->createBuilder($name, $datas, $options);
    }

    /**
     * Return a form
     *
     * @param FormTypeInterface $formType The type of the form
     * @param array $datas The initial data
     * @param array $options The form options
     *
     * @return FormInterface The form type
     */
    public function create($formType, $datas = null, $options = array())
    {
        return $this->builder->create($formType, $datas, $options);
    }
}
