<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Security\Attribute;

use Attribute;
use RuntimeException;
use Symfony\Component\ExpressionLanguage\Expression;

/**
 * Attribute based on the IsGranted attribute, adding information for redirection
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class AdminSecurity
{
    /**
     * Sets the first argument that will be passed to isGranted().
     */
    protected string|Expression $attribute;

    /**
     * If set, will throw HttpKernel's HttpException with the given $statusCode.
     * If null, Security\Core's AccessDeniedException will be used.
     */
    protected ?int $statusCode = null;

    /**
     * If set, will add the exception code to thrown exception.
     */
    protected ?int $exceptionCode = null;

    /**
     * The route for the redirection.
     *
     * @todo: Once the onboarding page is migrated, set default to his route name.
     */
    protected ?string $redirectRoute = null;

    /**
     * Define if a JSON or HTTP Response is expected
     */
    protected bool $hasJsonResponse = false;

    // The translation domain for the message.
    protected string $domain = 'Admin.Notifications.Error';

    /**
     * @deprecated once the back office is migrated, rely only on route.
     * The url for the redirection
     */
    protected string $url = 'admin_domain';

    /**
     * The message of the exception - has a nice default if not set.
     */
    protected string $message = 'Access Denied.';

    // The route params which are used together to generate the redirect route.
    protected array $redirectQueryParamsToKeep = [];

    public function __construct(
        array|string $data = [],
        ?string $message = null,
        ?string $domain = null,
        ?string $url = null,
        ?array $redirectQueryParamsToKeep = null,
        ?int $statusCode = null,
        ?int $exceptionCode = null,
        ?string $redirectRoute = null,
        ?bool $jsonResponse = false
    ) {
        $values = [];
        if (is_string($data)) {
            $values['attribute'] = $data;
        } else {
            $values = $data;
        }

        $values['message'] = $values['message'] ?? $message ?? $this->message;
        $values['domain'] = $values['domain'] ?? $domain ?? $this->domain;
        $values['url'] = $values['url'] ?? $url ?? $this->url;
        $values['redirectQueryParamsToKeep'] = $values['redirectQueryParamsToKeep'] ?? $redirectQueryParamsToKeep ?? $this->redirectQueryParamsToKeep;

        $values['statusCode'] = $values['statusCode'] ?? $statusCode;
        $values['exceptionCode'] = $values['exceptionCode'] ?? $exceptionCode;
        $values['redirectRoute'] = $values['redirectRoute'] ?? $redirectRoute;
        $values['jsonResponse'] = $values['jsonResponse'] ?? $jsonResponse ?? false;

        foreach ($values as $k => $v) {
            if (!method_exists($this, $name = 'set' . $k)) {
                throw new RuntimeException(sprintf('Unknown key "%s" for annotation "@%s".', $k, static::class));
            }

            $this->$name($v);
        }
    }

    public function getAttribute(): Expression|string
    {
        return $this->attribute;
    }

    public function setAttribute(Expression|string $attribute): void
    {
        $this->attribute = $attribute;
    }

    public function setValue(Expression|string $expression): void
    {
        $this->setAttribute($expression);
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    public function setStatusCode(?int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    public function getExceptionCode(): ?int
    {
        return $this->exceptionCode;
    }

    public function setExceptionCode(?int $exceptionCode): void
    {
        $this->exceptionCode = $exceptionCode;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function setDomain(string $domain): void
    {
        $this->domain = $domain;
    }

    public function getRedirectRoute(): ?string
    {
        return $this->redirectRoute;
    }

    public function setRedirectRoute(?string $redirectRoute): void
    {
        $this->redirectRoute = $redirectRoute;
    }

    public function hasJsonResponse(): bool
    {
        return $this->hasJsonResponse;
    }

    public function setJsonResponse(bool $hasJsonResponse): void
    {
        $this->hasJsonResponse = $hasJsonResponse;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getRedirectQueryParamsToKeep(): array
    {
        return $this->redirectQueryParamsToKeep;
    }

    public function setRedirectQueryParamsToKeep(array $redirectQueryParamsToKeep): void
    {
        $this->redirectQueryParamsToKeep = $redirectQueryParamsToKeep;
    }
}
