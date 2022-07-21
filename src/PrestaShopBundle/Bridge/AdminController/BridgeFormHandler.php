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

namespace PrestaShopBundle\Bridge\AdminController;

//@todo: naming.
//@todo: add other methods if accepted poc
use ObjectModel;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerResult;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerResultInterface;
use PrestaShopBundle\Bridge\AdminController\Field\FormField;
use PrestaShopBundle\Bridge\Helper\Form\HelperFormConfiguration;
use Symfony\Component\HttpFoundation\Request;

class BridgeFormHandler
{
    //@todo: formHandlerResult dediacted instead of using existing one that was made for vertical migration?
    public function handleRequest(Request $request, HelperFormConfiguration $helperFormConfiguration): FormHandlerResultInterface
    {
        if (!$request->request->get($this->getSubmitActionKey($helperFormConfiguration))) {
            return FormHandlerResult::createNotSubmitted();
        }

        //@todo: where/how do i fill object models with data?
        $objectModel = $this->processSave($helperFormConfiguration);
//        pseudo code
//        if ($errors) {
//            return FormHandlerResult::createSubmittedButNotValid();
//        }

        return FormHandlerResult::createWithId($objectModel->id);
    }

    /**
     * @param HelperFormConfiguration $helperFormConfiguration
     */
    protected function processSave(HelperFormConfiguration $helperFormConfiguration): ?ObjectModel
    {
        $objectModel = $helperFormConfiguration->getObjectModel();

        if ($objectModel->id) {
            return $this->processUpdate($objectModel);
        } else {
            return $this->processAdd($objectModel);
        }
    }

    protected function processAdd(ObjectModel $objectModel): ?ObjectModel
    {
//        @todo:
//        $this->validateRules();
//        if (count($this->errors) <= 0) {
//            $this->object = new $this->className();
//
//            $this->copyFromPost($this->object, $this->table);
//            $this->beforeAdd($this->object);
//            if (method_exists($this->object, 'add') && !$this->object->add()) {
//                $this->errors[] = $this->trans('An error occurred while creating an object.', [], 'Admin.Notifications.Error') .
//                    ' <b>' . $this->table . ' (' . Db::getInstance()->getMsgError() . ')</b>';
//            } elseif (($_POST[$this->identifier] = $this->object->id /* voluntary do affectation here */) && $this->postImage($this->object->id) && count($this->errors) === 0 && $this->_redirect) {
//                PrestaShopLogger::addLog(
//                    $this->trans('%s addition', [$this->className]),
//                    1,
//                    null,
//                    $this->className,
//                    (int) $this->object->id,
//                    true,
//                    (int) $this->context->employee->id
//                );
//                $parent_id = (int) Tools::getValue('id_parent', 1);
//                $this->afterAdd($this->object);
//                $this->updateAssoShop($this->object->id);
//                // Save and stay on same form
//                if (empty($this->redirect_after) && $this->redirect_after !== false && Tools::isSubmit('submitAdd' . $this->table . 'AndStay')) {
//                    $this->redirect_after = self::$currentIndex . '&' . $this->identifier . '=' . $this->object->id . '&conf=3&update' . $this->table . '&token=' . $this->token;
//                }
//                // Save and back to parent
//                if (empty($this->redirect_after) && $this->redirect_after !== false && Tools::isSubmit('submitAdd' . $this->table . 'AndBackToParent')) {
//                    $this->redirect_after = self::$currentIndex . '&' . $this->identifier . '=' . $parent_id . '&conf=3&token=' . $this->token;
//                }
//                // Default behavior (save and back)
//                if (empty($this->redirect_after) && $this->redirect_after !== false) {
//                    $this->redirect_after = self::$currentIndex . ($parent_id ? '&' . $this->identifier . '=' . $this->object->id : '') . '&conf=3&token=' . $this->token;
//                }
//            }
//        }
//
//        $this->errors = array_unique($this->errors);
//        if (!empty($this->errors)) {
//            // if we have errors, we stay on the form instead of going back to the list
//            $this->display = 'edit';
//
//            return false;
//        }

        $objectModel->add();

        if (!$objectModel->id) {
            return null;
        }

        return $objectModel;
    }

    /**
     * @param ObjectModel $objectModel
     *
     * @return ObjectModel|null
     */
    protected function processUpdate(ObjectModel $objectModel): ?ObjectModel
    {
        /* Checking fields validity */
//        $this->validateRules();
//        if (empty($this->errors)) {
//            $id = (int) Tools::getValue($this->identifier);
//
//            /* Object update */
//            if (!empty($id)) {
//                /** @var ObjectModel $object */
//                $object = new $this->className($id);
//                if (Validate::isLoadedObject($object)) {
//                    $result = false;
//                    /* Specific to objects which must not be deleted */
//                    if ($this->deleted && $this->beforeDelete($object)) {
//                        // Create new one with old objet values
//                        /** @var ObjectModel $object_new */
//                        $object_new = $object->duplicateObject();
//                        if (Validate::isLoadedObject($object_new)) {
//                            // Update old object to deleted
//                            $object->deleted = true;
//                            $object->update();
//
//                            // Update new object with post values
//                            $this->copyFromPost($object_new, $this->table);
//                            $result = $object_new->update();
//                            if (Validate::isLoadedObject($object_new)) {
//                                $this->afterDelete($object_new, $object->id);
//                            }
//                        }
//                    } else {
//                        $this->copyFromPost($object, $this->table);
//                        $result = $object->update();
//                        $this->afterUpdate($object);
//                    }
//
//                    if ($object->id) {
//                        $this->updateAssoShop($object->id);
//                    }
//
//                    if (!$result) {
//                        $this->errors[] = $this->trans('An error occurred while updating an object.', [], 'Admin.Notifications.Error') .
//                            ' <b>' . $this->table . '</b> (' . Db::getInstance()->getMsgError() . ')';
//                    } elseif ($this->postImage($object->id) && count($this->errors) === 0 && $this->_redirect) {
//                        $parent_id = (int) Tools::getValue('id_parent', 1);
//                        // Specific back redirect
//                        if ($back = Tools::getValue('back')) {
//                            $this->redirect_after = rawurldecode($back) . '&conf=4';
//                        }
//                        // Save and stay on same form
//                        // @todo on the to following if, we may prefer to avoid override redirect_after previous value
//                        if (Tools::isSubmit('submitAdd' . $this->table . 'AndStay')) {
//                            $this->redirect_after = self::$currentIndex . '&' . $this->identifier . '=' . $object->id . '&conf=4&update' . $this->table . '&token=' . $this->token;
//                        }
//                        // Save and back to parent
//                        if (Tools::isSubmit('submitAdd' . $this->table . 'AndBackToParent')) {
//                            $this->redirect_after = self::$currentIndex . '&' . $this->identifier . '=' . $parent_id . '&conf=4&token=' . $this->token;
//                        }
//
//                        // Default behavior (save and back)
//                        if (empty($this->redirect_after) && $this->redirect_after !== false) {
//                            $this->redirect_after = self::$currentIndex . ($parent_id ? '&' . $this->identifier . '=' . $object->id : '') . '&conf=4&token=' . $this->token;
//                        }
//                    }
//                    PrestaShopLogger::addLog(
//                        $this->trans('%s modification', [$this->className]),
//                        1,
//                        null,
//                        $this->className,
//                        (int) $object->id,
//                        true,
//                        (int) $this->context->employee->id
//                    );
//                } else {
//                    $this->errors[] = $this->trans('An error occurred while updating an object.', [], 'Admin.Notifications.Error') .
//                        ' <b>' . $this->table . '</b> ' . $this->trans('(cannot load object)', [], 'Admin.Notifications.Error');
//                }
//            }
//        }
//        $this->errors = array_unique($this->errors);
//        if (!empty($this->errors)) {
//            // if we have errors, we stay on the form instead of going back to the list
//            $this->display = 'edit';
//
//            return false;
//        }
//
//        if (isset($object)) {
//            return $object;
//        }

        $objectModel->update();

        return $objectModel;
    }

    private function getSubmitActionKey(HelperFormConfiguration $formConfiguration): string
    {
        //@todo: not sure about this implementation. It smells. But its a good starting point.
        // when smarty is rendered, all submit buttons are rendered with provided name in form helper field and then only if
        // name is not defined in field, it uses submit_action as button name
        foreach ($formConfiguration->getFormFields() as $formField) {
            if ($formField->getType() === FormField::TYPE_SUBMIT && isset($formField->getConfig()['name'])) {
                return $formField->getConfig()['name'];
            }
        }

        if ($formConfiguration->getSubmitAction()) {
            return $formConfiguration->getSubmitAction();
        }

        $objectModelDefinition = $formConfiguration->getObjectModel()::$definition;

        return 'submitAdd' . $objectModelDefinition['table'];
    }
}
