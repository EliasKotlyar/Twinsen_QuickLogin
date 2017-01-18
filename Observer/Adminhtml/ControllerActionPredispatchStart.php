<?php


namespace Twinsen\QuickLogin\Observer\Adminhtml;

class ControllerActionPredispatchStart implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var \Twinsen\QuickLogin\Helper\Config
     */
    private $config;
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    private $authSession;
    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    private $url;
    /**
     * @var \Magento\Backend\Model\Session
     */
    private $session;

    public function __construct(
        \Twinsen\QuickLogin\Helper\Config $config,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Backend\Model\UrlInterface $url,
        \Magento\Backend\Model\Session $session
    )
    {

        $this->config = $config;
        $this->authSession = $authSession;
        $this->url = $url;
        $this->session = $session;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    )
    {

        if (!$this->_getHelper()->canAutoLogin()) {
            return;
        }

        if ($this->authSession->isLoggedIn()) {
            return;
        }

        $user = Mage::getModel('admin/user')->loadByUsername($this->_getHelper()->getAutoLoginUsername());

        if ($this->url->useSecretKey()) {
            $this->url->renewSecretUrls();
        }

        $session = $this->session;
        $session->setIsFirstVisit(true);
        $session->setUser($user);
        $session->setAcl(Mage::getResourceModel('admin/acl')->loadAcl());

        //Mage::dispatchEvent('admin_session_user_login_success', array('user' => $user));

        if ($session->isLoggedIn()) {
            $this->_getSession()->addWarning(
                $this->_getHelper()->__('You were automatically logged in by the extension CeckosLab_QuickLogin! Please don\'t use CeckosLab_QuickLogin on production environment! It may lead to serious security issues!')
            );

            $redirectUrl = Mage::getSingleton('adminhtml/url')
                ->getUrl(Mage::getModel('admin/user')->getStartupPageUrl(), array('_current' => false));

            header('Location: ' . $redirectUrl);
            exit;
        }
    }

    /**
     * @return \Twinsen\QuickLogin\Helper\Config
     */
    private function _getHelper()
    {
        return $this->config;
    }

    /**
     * Retrieve adminhtml session model object
     *
     * @return Mage_Adminhtml_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }
}
