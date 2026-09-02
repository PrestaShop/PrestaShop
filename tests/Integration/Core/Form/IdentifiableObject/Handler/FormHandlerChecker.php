<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
