<?php
/**
 * Created by PhpStorm.
 * User: eRIZ
 * Date: 2015-03-24
 * Time: 16:37
 */

namespace DreamCommerce\ShopAppstoreBundle\EventListener;


use DreamCommerce\Client;
use DreamCommerce\ShopAppstoreBundle\Controller\FilteredControllerInterface;
use DreamCommerce\ShopAppstoreBundle\EntityManager\ShopManagerInterface;
use DreamCommerce\ShopAppstoreBundle\Utils\InvalidRequestException;
use DreamCommerce\ShopAppstoreBundle\Utils\RequestValidator;
use DreamCommerce\ShopAppstoreBundle\Utils\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

// todo: invalid token handling/refreshing
class FilterControllerListener{

    protected $applications;
    protected $shopManager;

    public function __construct($applications, ShopManagerInterface $shopManager){
        $this->applications = $applications;
        $this->shopManager = $shopManager;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        /**
         * @var $controller FilteredControllerInterface
         */
        $controller = $event->getController();
        /*
         * $controller passed can be either a class or a Closure.
         * This is not usual in Symfony but it may happen.
         * If it is a class, it comes in array format
         */
        if (!is_array($controller)) {
            return;
        }

        if ($controller[0] instanceof FilteredControllerInterface) {

            $request = $event->getRequest();

            $requestValidator = new RequestValidator($request);

            try{
                $appName = $requestValidator->getApplicationName($this->applications);
                $appData = $this->applications[$appName];
                $requestValidator->setApplication($appData);
                $params = $requestValidator->validateAppRequest();
            }catch(InvalidRequestException $ex){
                throw new BadRequestHttpException('Invalid request');
            }

            $shop = $this->shopManager->findShopByNameAndApplication($params['shop'], $appName);

            if(!$shop){
                throw new AccessDeniedHttpException('Application not found');
            }

            $token = $shop->getToken();

            $client = new Client($shop->getShopUrl(), $appData['app_id'], $appData['app_secret']);
            $client->setAccessToken($token->getAccessToken());

            $controller[0]->injectClient($client, $shop);
        }
    }

}