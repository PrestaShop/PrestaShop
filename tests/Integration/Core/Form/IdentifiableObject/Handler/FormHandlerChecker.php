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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Form\IdentifiableObject\Handler;

use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use Symfony\Component\Form\FormInterface;

class FormHandlerChecker implements FormHandlerInterface
{
    /**
     * @var FormHandlerInterface
     */
    private $formHandler;

    /**
     * @var int|null
     */
    private $lastCreatedId;

    /**
     * AddressFormDataHandlerChecker constructor.
     *
     * @param FormHandlerInterface $formHandler
     */
    public function __construct(FormHandlerInterface $formHandler)
    {
        $this->formHandler = $formHandler;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(FormInterface $form)
    {
        $result = $this->formHandler->handle($form);
        $this->lastCreatedId = $result->getIdentifiableObjectId();

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function handleFor($id, FormInterface $form)
    {
        return $this->formHandler->handleFor($id, $form);
    }

    /**
     * @return int|null
     */
    public function getLastCreatedId(): ?int
    {
        return $this->lastCreatedId;
    }
}
