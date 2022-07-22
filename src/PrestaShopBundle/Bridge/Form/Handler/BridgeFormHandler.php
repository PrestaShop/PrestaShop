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

namespace PrestaShopBundle\Bridge\Form\Handler;

use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerResultInterface;
use PrestaShopBundle\Bridge\Form\Configuration\FormHelperConfiguration;
use Symfony\Component\HttpFoundation\Request;

// whole class contains pseudo code partially copied from another Pr
class BridgeFormHandler
{
    public function handleRequest(Request $request, FormHelperConfiguration $formHelperConfiguration): BridgeFormHandlerResult
    {
        if (!$request->request->get('fooAction')) {
            return BridgeFormHandlerResult::createNotSubmitted();
        }

//        pseudo code
        $objectModel = $formHelperConfiguration->getObjectModel();
        if (!$objectModel->id) {
            $errors = $this->add($request, $objectModel);
        } else {
            $errors = $this->update($request, $objectModel);
        }

        if ($errors) {
            return BridgeFormHandlerResult::createInvalid($errors);
        }

        return BridgeFormHandlerResult::createSubmittedAndValid();
    }

    public function add(Request $request, \ObjectModel $objectModel): array
    {
        // @todo: filling with data is missing, but its only pseudo code so dont mind it
        $errors = $this->validate($objectModel);

        if (!$errors) {
            $objectModel->add();
        }

        return $errors;
    }

    public function update(Request $request, \ObjectModel $objectModel): array
    {
        // @todo: filling with data is missing, but its only pseudo code so dont mind it
        $errors = $this->validate($objectModel);

        if (!$errors) {
            $objectModel->update();
        }

        return $errors;
    }

    private function validate(\ObjectModel $objectModel): array
    {
        $errors = [];

        $definition = $objectModel::$definition;
        foreach ($definition['fields'] as $field) {
            $errors[] = $objectModel->validateField($field, $objectModel->{$field});
        }

        return $errors;
    }
}
