<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Behaviour\Features\Context\Configuration;

use Mail;

class EmailConfigurationFeatureContext extends AbstractConfigurationFeatureContext
{
    /**
     * @Given /^email sending is disabled$/
     */
    public function disableEmail()
    {
        $this->setConfiguration('PS_MAIL_METHOD', Mail::METHOD_DISABLE);
    }
}
