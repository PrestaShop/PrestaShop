<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Translation\Provider;

/**
 * Translation provider specific to email subjects.
 */
class MailsBodyProvider extends AbstractProvider implements UseDefaultCatalogueInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTranslationDomains()
    {
        return ['EmailsBody*'];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return ['#EmailsBody*#'];
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'mails_body';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultResourceDirectory()
    {
        return $this->resourceDirectory . DIRECTORY_SEPARATOR . 'default';
    }
}
