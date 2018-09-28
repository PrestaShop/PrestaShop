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

namespace PrestaShop\PrestaShop\Adapter\Meta;

use Meta;

/**
 * Class MetaFormDataValidator is responsible for validating meta form fields according to entity.
 */
class MetaFormDataValidator
{
    /**
     * Validates meta form data and returns formatted error.
     *
     * @param array $data
     *
     * @return array
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function validate(array $data)
    {
        $entity = new Meta();

        $entity->page = $data['page_name'];
        $entity->title = $data['page_title'];
        $entity->description = $data['meta_description'];
        $entity->keywords = (array) $data['meta_keywords']; //todo: remove casting once multilang field is available
        $entity->url_rewrite = $data['url_rewrite'];

        if (true !== $error = $entity->validateFields(false, true)) {
            return [$error];
        }

        if (true !== $error = $entity->validateFieldsLang(false, true)) {
            return [$error];
        }

        return [];
    }
}
