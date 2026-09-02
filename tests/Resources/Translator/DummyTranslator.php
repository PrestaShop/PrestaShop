<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Resources\Translator;

use Symfony\Contracts\Translation\TranslatorInterface;

class DummyTranslator implements TranslatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function transChoice($id, $number, array $parameters = [], $domain = null, $locale = null): string
    {
        return 'not implemented yet';
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale($locale)
    {
        // not implemented yet
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale(): string
    {
        return 'not implemented yet';
    }

    /**
     * {@inheritdoc}
     */
    public function trans($id, array $parameters = [], $domain = null, $locale = null): string
    {
        return str_replace(
            array_keys($parameters),
            array_values($parameters),
            $id
        );
    }
}
