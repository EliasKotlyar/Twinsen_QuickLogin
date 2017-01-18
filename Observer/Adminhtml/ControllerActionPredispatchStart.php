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
    /**
     * @var \Magento\User\Model\ResourceModel\User
     */
    private $user;
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;
    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $eventManager;

    public function __construct(
        \Twinsen\QuickLogin\Helper\Config $config,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Backend\Model\UrlInterface $url,
        \Magento\Backend\Model\Session $session,
        \Magento\User\Model\User $user,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Event\ManagerInterface $eventManager
    )
    {

        $this->config = $config;
        $this->authSession = $authSession;
        $this->url = $url;
        $this->session = $session;
        $this->user = $user;
        $this->messageManager = $messageManager;
        $this->eventManager = $eventManager;
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
        $userName = $this->_getHelper()->getAutoLoginUsername();
        $user = $this->user->loadByUsername($userName);
        //var_dump($user);

        $this->eventManager->dispatch(
            'admin_user_authenticate_before',
            ['username' => $userName, 'user' => $user]
        );

        $result = true;
        $password = "";
        $this->eventManager->dispatch(
            'admin_user_authenticate_after',
            ['username' => $userName, 'password' => $password, 'user' => $user, 'result' => $result]
        );
        $this->authSession->setUser($user);
        $this->authSession->processLogin();
        $this->eventManager->dispatch(
            'backend_auth_user_login_success',
            ['user' => $user]
        );



        if ($this->authSession->isLoggedIn()) {
            //die("logged in");
            $this->messageManager->addWarning(
                __('You were automatically logged in by the extension CeckosLab_QuickLogin! Please don\'t use CeckosLab_QuickLogin on production environment! It may lead to serious security issues!')
            );

            $redirectUrl = $this->url
                ->getUrl($this->url->getStartupPageUrl(), array('_current' => false));

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
     * @return \Magento\Backend\Model\Session
     */
    protected function _getSession()
    {
        return $this->session;
    }
}
