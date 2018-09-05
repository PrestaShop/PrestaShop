<?php
/**
 * 2007-2018 PrestaShop.
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

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\RequestSql;

use PrestaShop\PrestaShop\Adapter\SqlManager\SqlRequestFormDataValidator;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class RequestSqlFormHandler is responsible for creating RequestSql form.
 */
class SqlRequestFormHandler
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var SqlRequestFormDataValidator
     */
    private $requestSqlFormDataValidator;

    /**
     * @var SqlRequestFormDataProvider
     */
    private $requestSqlFormDataProvider;

    /**
     * @param FormFactoryInterface $formFactory
     * @param SqlRequestFormDataValidator $requestSqlFormDataValidator
     * @param SqlRequestFormDataProvider $requestSqlFormDataProvider
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        SqlRequestFormDataValidator $requestSqlFormDataValidator,
        SqlRequestFormDataProvider $requestSqlFormDataProvider
    ) {
        $this->formFactory = $formFactory;
        $this->requestSqlFormDataValidator = $requestSqlFormDataValidator;
        $this->requestSqlFormDataProvider = $requestSqlFormDataProvider;
    }

    /**
     * Get RequestSql form.
     *
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->getRequestSqlForm();
    }

    /**
     * Get for for given RequestSql.
     *
     * @param int $requestSqlId
     *
     * @return FormInterface
     */
    public function getFormFor($requestSqlId)
    {
        $requestSqlFormData = $this->requestSqlFormDataProvider->getData($requestSqlId);

        return $this->getRequestSqlForm($requestSqlFormData);
    }

    /**
     * Save RequestSql form data.
     *
     * @param array $data
     *
     * @return array[] Array of errors if any
     */
    public function save(array $data)
    {
        $errors = $this->requestSqlFormDataValidator->validate($data['request_sql']);

        if (!empty($errors)) {
            return $errors;
        }

        return $this->requestSqlFormDataProvider->saveData($data['request_sql']);
    }

    /**
     * @param array $formData
     *
     * @return FormInterface
     */
    private function getRequestSqlForm(array $formData = [])
    {
        $builder = $this->formFactory->createBuilder()
            ->add('request_sql', SqlRequestType::class)
            ->setData([
                'request_sql' => $formData,
            ])
        ;

        return $builder->getForm();
    }
}
