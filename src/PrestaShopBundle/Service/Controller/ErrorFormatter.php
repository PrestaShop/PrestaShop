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

namespace PrestaShopBundle\Service\Controller;

use Exception;
use PrestaShop\PrestaShop\Core\Module\Exception\ModuleErrorInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ErrorFormatter
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly ParameterBagInterface $parameterBag
    ) {
    }

    /**
     * Get error by exception from given messages
     *
     * @param Exception $e
     * @param array $messages
     *
     * @return string
     */
    public function getErrorMessageForException(Exception $e, array $messages): string
    {
        if ($e instanceof ModuleErrorInterface) {
            return $e->getMessage();
        }

        $exceptionType = $e::class;
        $exceptionCode = $e->getCode();

        if (isset($messages[$exceptionType])) {
            $message = $messages[$exceptionType];

            if (is_string($message)) {
                return $message;
            }

            if (is_array($message) && isset($message[$exceptionCode])) {
                return $message[$exceptionCode];
            }
        }

        return $this->getFallbackErrorMessage(
            $exceptionType,
            $exceptionCode,
            $e->getMessage()
        );
    }

    private function getFallbackErrorMessage(string $exceptionType, int $exceptionCode, string $exceptionMessage): string
    {
        $isDebug = $this->parameterBag->get('kernel.debug');
        if ($isDebug && !empty($exceptionMessage)) {
            return $this->translator->trans(
                'An unexpected error occurred. [%type% code %code%]: %message%',
                [
                    '%type%' => $exceptionType,
                    '%code%' => $exceptionCode,
                    '%message%' => $exceptionMessage,
                ],
                'Admin.Notifications.Error',
            );
        }

        return $this->translator->trans(
            'An unexpected error occurred. [%type% code %code%]',
            [
                '%type%' => $exceptionType,
                '%code%' => $exceptionCode,
            ],
            'Admin.Notifications.Error',
        );
    }
}
