<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\BaseController;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Services\PaymentServices;
use App\Services\MercadoPagoService;

class PaymentMpController extends BaseController
{
    public function verify(Request $request, Response $response)
    {
        $success = true;
        $message = 'Mercado Pago: Consulta exitosa';
        $status = 200;
        $list = [];

        try {
            $getPay = new PaymentServices();
            $getPay->dataValidPay(env('SEARCH_PAYMENTS_URI'));
        } catch (Exception $e) {
            $success = false;
            $message = $e->getMessage();
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : (method_exists($e, 'getCode') ? $e->getCode() : 500);
        }

        return $this->json($response, [
            'success'   => $success,
            'message'   => $message
        ], $status);
    }
}