<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Security\Annotation;

use PrestaShopBundle\Security\Attribute\AdminSecurity as AdminSecurityAttribute;

/**
 * Annotation based on the AdminSecurity attribute
 *
 * @deprecated
 *
 * @Annotation
 */
class AdminSecurity extends AdminSecurityAttribute
{
}
