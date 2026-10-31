<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\CustomerService;

use PrestaShop\PrestaShop\Adapter\CustomerService\Repository\CustomerThreadRepository;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\Exception\CustomerThreadNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\ValueObject\CustomerThreadId;
use PrestaShop\PrestaShop\Core\Util\Url\UrlCleaner;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

/**
 * Renders the latest message of a thread for the "forward" email.
 *
 * @internal
 */
final class ForwardedMessageRenderer
{
    public function __construct(
        private readonly CustomerThreadRepository $customerThreadRepository,
        private readonly Environment $twig,
        private readonly UrlGeneratorInterface $router,
    ) {
    }

    /**
     * @param bool $withThreadLink only an employee can open the back office thread
     *
     * @throws CustomerThreadNotFoundException
     */
    public function render(CustomerThreadId $customerThreadId, bool $withThreadLink): string
    {
        $message = $this->customerThreadRepository->getLatestMessage($customerThreadId);
        $message['message'] = html_entity_decode((string) $message['message'], ENT_QUOTES, 'UTF-8');

        return $this->twig->render('@PrestaShop/Admin/Sell/CustomerService/CustomerThread/Block/forwarded_message.html.twig', [
            'message' => $message,
            'threadUrl' => $withThreadLink ? $this->getThreadUrl($customerThreadId) : null,
        ]);
    }

    /**
     * The link is emailed: the sender's CSRF token must not travel with it.
     */
    private function getThreadUrl(CustomerThreadId $customerThreadId): string
    {
        return UrlCleaner::cleanUrl(
            $this->router->generate(
                'admin_customer_threads_view',
                ['customerThreadId' => $customerThreadId->getValue()],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
            ['_token']
        );
    }
}
