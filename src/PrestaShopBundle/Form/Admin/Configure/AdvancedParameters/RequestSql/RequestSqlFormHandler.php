<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\RequestSql;

use PrestaShop\PrestaShop\Adapter\SqlManager\RequestSqlManager;
use PrestaShop\PrestaShop\Adapter\SqlManager\RequestSqlValidator;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Class RequestSqlFormHandler is responsible for creating RequestSql form
 */
class RequestSqlFormHandler
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var RequestSqlManager
     */
    private $requestSqlManager;

    /**
     * @var RequestSqlValidator
     */
    private $requestSqlValidator;

    /**
     * @param FormFactoryInterface $formFactory
     * @param RequestSqlManager $requestSqlManager
     * @param RequestSqlValidator $requestSqlValidator
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        RequestSqlManager $requestSqlManager,
        RequestSqlValidator $requestSqlValidator
    ) {
        $this->formFactory = $formFactory;
        $this->requestSqlManager = $requestSqlManager;
        $this->requestSqlValidator = $requestSqlValidator;
    }

    /**
     * {@inheritdoc}
     */
    public function getForm(array $data = [])
    {
        $builder = $this->formFactory->createBuilder()
            ->add('request_sql', RequestSqlType::class)
            ->setData($data)
        ;

        return $builder->getForm();
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function save(array $data)
    {
        if ($errors = $this->requestSqlValidator->validateSql($data['request_sql']['sql'])) {
            return $errors;
        }

        return $this->requestSqlManager->createOrUpdateFromData($data['request_sql']);
    }
}
