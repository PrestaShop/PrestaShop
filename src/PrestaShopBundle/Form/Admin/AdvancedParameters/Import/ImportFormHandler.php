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

namespace PrestaShopBundle\Form\Admin\AdvancedParameters\Import;

use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * This class manages the data submitted in first step of import
 * in "Configure > Advanced Parameters > Import" page.
 */
final class ImportFormHandler implements FormHandlerInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(FormFactoryInterface $formFactory, SessionInterface $session)
    {
        $this->formFactory = $formFactory;
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function getForm()
    {
        $data = [
            'csv' => $this->session->get('csv'),
            'entity' => $this->session->get('entity'),
            'iso_lang' => $this->session->get('iso_lang'),
            'separator' => $this->session->get('separator', ImportType::DEFAULT_SEPARATOR),
            'multiple_value_separator' =>
                $this->session->get('multiple_value_separator', ImportType::DEFAULT_MULTIVALUE_SEPARATOR),
        ];

        return $this->formFactory->createNamed('', ImportType::class, $data);
    }

    /**
     * Performs some checked on data that is being passed to legacy controller
     *
     * @param array $data   Form data
     *
     * @return array        Errors if any or empty array if no errors occured
     */
    public function save(array $data)
    {
        $errors = [];

        if (!isset($data['csv']) || empty($data['csv'])) {
            $errors[] = [
                'key' => 'To proceed, please upload a file first.',
                'domain' => 'Admin.Advparameters.Notification',
                'parameters' => [],
            ];
        }

        $this->session->set('csv', $data['csv']);
        $this->session->set('entity', $data['entity']);
        $this->session->set('iso_lang', $data['iso_lang']);
        $this->session->set('separator', $data['separator']);
        $this->session->set('multiple_value_separator', $data['multiple_value_separator']);

        return $errors;
    }
}
