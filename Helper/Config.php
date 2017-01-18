<?php
/**
 * @author      Tsvetan Stoychev <ceckoslab@gmail.com>
 * @website     http://www.ceckoslab.com
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software Licence 3.0 (OSL-3.0)
 */
namespace Twinsen\QuickLogin\Helper;
class Config
    extends \Magento\Framework\App\Helper\AbstractHelper
{

    const DEFAULT_AUTO_LOGIN_ADMIN_USERNAME_XML_PATH = 'dev/quicklogin/autologin_admin_username';
    const AUTO_LOGIN_ADMIN_ENABLED_XML_PATH = 'dev/quicklogin/enabled';

    /**
     * @return bool
     */
    public function canAutoLogin()
    {
        $enabled = (bool)$this->scopeConfig->getValue(self::AUTO_LOGIN_ADMIN_ENABLED_XML_PATH);
        $autologinUsername = $this->scopeConfig->getValue(self::DEFAULT_AUTO_LOGIN_ADMIN_USERNAME_XML_PATH);

        return ($enabled && !empty($autologinUsername));
    }

    /**
     * @return string
     */
    public function getAutoLoginUsername()
    {
        return $this->scopeConfig->getValue(self::DEFAULT_AUTO_LOGIN_ADMIN_USERNAME_XML_PATH);
    }

}