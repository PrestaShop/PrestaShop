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

namespace PrestaShopBundle\Form\Partial;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Exception\AlreadySubmittedException;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * PartialForm is used to allow getting the data submitted to the Form.
 *
 * Since the parent class Form is hardly extendable because it has a lot of private
 * fields this class is instead a wrapper. The main interest of this class is the
 * getSubmittedData getter.
 */
class PartialForm implements \IteratorAggregate, PartialFormInterface
{
    /**
     * @var Form
     */
    private $innerForm;

    /**
     * @var string|array|null
     */
    private $submittedData;

    /**
     * @param Form $form
     * @param EventDispatcherInterface|null $dispatcher
     */
    public function __construct(Form $form, EventDispatcherInterface $dispatcher = null)
    {
        $this->innerForm = $form;

        if (null !== $dispatcher && $dispatcher->hasListeners(FormEvents::PRE_SUBMIT)) {
            // We set the listener internally so that the callback remains private
            $dispatcher->addListener(
                FormEvents::PRE_SUBMIT,
                function (FormEvent $event) {
                    $this->onSubmittedData($event);
                },
                // We set lowest priority so that it's called last and has latest modifications
                -1000
            );
        }
    }

    /**
     * @return Form
     */
    public function getInnerForm(): Form
    {
        return $this->innerForm;
    }

    /**
     * @return array|string|null
     */
    public function getSubmittedData()
    {
        return $this->submittedData;
    }

    /**
     * @return bool
     */
    public function usePartialUpdate(): bool
    {
        if (!$this->getConfig()->hasOption('use_partial_update')) {
            return false;
        }

        return $this->getConfig()->getOption('use_partial_update');
    }

    public function submit($submittedData, $clearMissing = true)
    {
        if ($this->innerForm->isSubmitted()) {
            throw new AlreadySubmittedException('A form can only be submitted once.');
        }

        $this->submittedData = $submittedData;
        $this->innerForm->submit($submittedData, $clearMissing);

        return $this;
    }

    /**
     * @param FormEvent $event
     */
    private function onSubmittedData(FormEvent $event): void
    {
        $this->submittedData = $event->getData();
    }

    /**
     * All the following functions are proxies to the innerForm
     */
    public function getIterator()
    {
        return $this->innerForm->getIterator();
    }

    public function offsetExists($offset)
    {
        return $this->innerForm->offsetExists($offset);
    }

    public function offsetGet($offset)
    {
        return $this->innerForm->offsetGet($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->innerForm->offsetSet($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->innerForm->offsetUnset($offset);
    }

    public function count()
    {
        return $this->innerForm->count();
    }

    public function setParent(FormInterface $parent = null)
    {
        $this->innerForm->setParent($parent);

        return $this;
    }

    public function getParent()
    {
        return $this->innerForm->getParent();
    }

    public function add($child, $type = null, array $options = [])
    {
        $this->innerForm->add($child, $type, $options);

        return $this;
    }

    public function get($name)
    {
        return $this->innerForm->get($name);
    }

    public function has($name)
    {
        return $this->innerForm->has($name);
    }

    public function remove($name)
    {
        $this->innerForm->remove($name);

        return $this;
    }

    public function all()
    {
        return $this->innerForm->all();
    }

    public function getErrors($deep = false, $flatten = true)
    {
        return $this->innerForm->getErrors($deep, $flatten);
    }

    public function setData($modelData)
    {
        $this->innerForm->setData($modelData);

        return $this;
    }

    public function getData()
    {
        return $this->innerForm->getData();
    }

    public function getNormData()
    {
        return $this->innerForm->getNormData();
    }

    public function getViewData()
    {
        return $this->innerForm->getViewData();
    }

    public function getExtraData()
    {
        return $this->innerForm->getExtraData();
    }

    public function getConfig()
    {
        return $this->innerForm->getConfig();
    }

    public function isSubmitted()
    {
        return $this->innerForm->isSubmitted();
    }

    public function getName()
    {
        return $this->innerForm->getName();
    }

    public function getPropertyPath()
    {
        return $this->innerForm->getPropertyPath();
    }

    public function addError(FormError $error)
    {
        $this->innerForm->addError($error);

        return $this;
    }

    public function isValid()
    {
        return $this->innerForm->isValid();
    }

    public function isRequired()
    {
        return $this->innerForm->isRequired();
    }

    public function isDisabled()
    {
        return $this->innerForm->isDisabled();
    }

    public function isEmpty()
    {
        return $this->innerForm->isEmpty();
    }

    public function isSynchronized()
    {
        return $this->innerForm->isSynchronized();
    }

    public function getTransformationFailure()
    {
        return $this->innerForm->getTransformationFailure();
    }

    public function initialize()
    {
        $this->innerForm->initialize();

        return $this;
    }

    public function handleRequest($request = null)
    {
        $this->getConfig()->getRequestHandler()->handleRequest($this, $request);

        return $this;
    }

    public function getRoot()
    {
        return $this->getParent() ? $this->getParent()->getRoot() : $this;
    }

    public function isRoot()
    {
        return $this->innerForm->isRoot();
    }

    public function createView(FormView $parent = null)
    {
        return $this->innerForm->createView($parent);
    }
}
