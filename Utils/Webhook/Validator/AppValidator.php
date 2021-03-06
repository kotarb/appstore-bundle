<?php


namespace DreamCommerce\ShopAppstoreBundle\Utils\Webhook\Validator;


use DreamCommerce\ShopAppstoreBundle\Handler\Application;
use DreamCommerce\ShopAppstoreBundle\Model\ShopInterface;

class AppValidator extends ValidatorAbstract implements AppValidatorInterface
{

    /**
     * @var ShopInterface
     */
    protected $shop;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @param ShopInterface $shop
     */
    public function setShop(ShopInterface $shop)
    {
        $this->shop = $shop;
    }

    /**
     * @param Application $app
     * @param string $webhookName
     */
    public function setConfig(Application $app, $webhookName)
    {
        $config = $app->getWebhook($webhookName);

        $this->app = $app;
        $this->secret = $config['secret'];
        $this->events = $config['events'];
    }
}