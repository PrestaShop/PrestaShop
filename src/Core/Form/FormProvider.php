<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Core\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * This services build form types registered and appended
 * to the selected form.
 */
class FormProvider implements FormProviderInterface
{
    /**
     * @var ParameterBag
     */
    private $fields;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->fields = new ParameterBag();
        $this->formFactory = $formFactory;
    }

    /**
     * @{inheritdoc}
     */
    public function addFieldProvider(FormFieldProviderInterface $fieldProvider)
    {
        foreach ($fieldProvider->getFormFields() as $formName => $fields) {
            if (!$this->fields->has($formName)) {
                $this->fields->set($formName, array());
            }

            $forms = $this->fields->get($formName);

            foreach ($fields as $name => $field) {
                if ($field instanceof FormInterface) {
                    $forms[] = $field;
                    continue;
                }


                $forms[] = $this->formFactory->createNamed(
                    $name,
                    $field['type'],
                    $field['data'],
                    array_merge(
                        $field['options'],
                        array(
                            'auto_initialize' => false,
                        )
                    )
                );
            }

            $this->fields->set($formName, $forms);
        }
    }

    /**
     * @{inheritdoc}
     */
    public function getFields(AbstractType $formType)
    {
        $fields = $this->fields->get($formType->getBlockPrefix());

        return !is_null($fields) ? $fields : array();
    }
}
