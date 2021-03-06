<?php


namespace DreamCommerce\ShopAppstoreBundle\Utils\Webhook\Validator;


use Symfony\Component\HttpFoundation\Request;

interface ValidatorInterface
{
    public function isEventSupported($event);
    public function isRequestValid(Request $request);
}