<?php

namespace App\Library;

/**
 * summary
 */
class FakeOrder
{

    /**
     * summary
     */
    public function __construct()
    {
    
    }

    public static function success($uuid = null)
    {
    	$order = new \stdClass();
        $order->success=true;
        $order->message="";
        $order->result = new \stdClass();
        $uuid ? $order->result->uuid = $uuid : $order->result->uuid = "7c6db929-6c4f-4711-b99b-01c9697330ce";
        $order->result->PricePerUnit = 0.00001234;
        $order->result->Closed = "2014-07-13T07:45:46.27";

        return $order;
    }

    public static function notClosed($uuid = null)
    {
    	$order = new \stdClass();
        $order->success=true;
        $order->message="";
        $order->result = new \stdClass();
        $uuid ? $order->result->uuid = $uuid : $order->result->uuid = "7c6db929-6c4f-4711-b99b-01c9697330ce";
        $order->result->PricePerUnit = 0.00001234;
        $order->result->Closed = "";

        return $order;
    }

    public static function fail($message = null)
    {
    	$order = new \stdClass();
        $order->success=false;
        $message ? $order->message=$message : $order->message="Invalid API credentials";
        $order->result = new \stdClass();
        $order->result->uuid = "";

        return $order;
    }
}