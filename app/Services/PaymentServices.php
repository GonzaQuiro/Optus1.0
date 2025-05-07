<?php

namespace App\Services;

use GuzzleHttp\Client;
use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;
use App\Services\MercadoPagoService;
use function GuzzleHttp\json_decode;
use Symfony\Component\Validator\Constraints\Length;
use App\Models\Payment;
use App\Models\Concurso;
use App\Models\Participante;
use App\Listeners\AddPaymentsListeners;
use Illuminate\Http\Request;
use MercadoPago\Item;
use MercadoPago\MerchantOrder;
use MercadoPago\Payer;
use MercadoPago\Preference;
use MercadoPago\SDK;


class PaymentServices
{

    public $id_usuario;

    public function __construct()
    {
        $this->client = new MercadoPagoService();

    }

    /**
     * generatePreference
     * Result data url generatePreference
     **/
    public static function generatePreference($id_concurso)
    {
        try {
            $id_usuario = user()->id;
            $concurso = Concurso::find($id_concurso);
            
            // Verificar si el concurso existe
            if (!$concurso) {
                return false; // O manejar el error de otra manera
            }

            $oferente = $concurso->oferentes->where('id_usuario', $id_usuario)->first();

            // Verificar si el oferente existe
            if (!$oferente) {
                return false; // O manejar el error de otra manera
            }

            $token = env('ACCESS_TOKEN', null) && !empty(env('ACCESS_TOKEN', null)) ? env('ACCESS_TOKEN', null) : null;

            if ($token) {
                SDK::initialize();
                SDK::setAccessToken($token);
                $preference = new Preference();
                $preference->external_reference = (int)$oferente[0]->id;
                $preference->additional_info = (int)$concurso->id;
                $item = new Item();
                $item->id = (int)$concurso->id;//id consurso -> id item API MP
                $item->title = "Compra concurso nº " . $concurso->id;
                $item->quantity = 1;
                $item->unit_price = 600;
                $preference->items = array($item);
                $pay = new Payment();
                $pay->id = (int)$oferente[0]->id . $concurso->id;//id concurso + id participante -> id pago API MP
                $preference->save();
                if (!$oferente[0]->payment->participante_id) {
                    // Verificar si el objeto payment existe
                    $payments = new Payment();
                    $payments->participante_id = $oferente[0]->id;
                    $payments->link = $preference->init_point;
                    $payments->paid = 'standby';
                    $payments->preference = $preference->id;
                    $payments->title = $item->title;
                    $payments->itemid = $concurso->id;
                    $payments->created_at = Carbon::now();
                    $payments->updated_at = Carbon::now();
                    $payments->save();

                }
                return $preference->init_point;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * $client->getPago("/v1/payments/search")->results[0]->status;
     * Result data payments
     **/
    public function dataValidPay($uri)
    {
        try {
            $top = (int)$this->client->getPago($uri)->paging->total;
            $topnumber = $top - 1;

            //$external_reference  : equal id participante
            $external_reference = $this->client->getPago($uri)->results[$topnumber]->external_reference;

            //Result type : ORDER
            $order = $this->client->getPago($uri)->results[$topnumber]->order->id;

            // exist participante_id?true:false
            $paymentsTable = Payment::where('participante_id', '=', $external_reference)->count();

            // get id participante
            $participante = Payment::where(['participante_id' => $external_reference])->first();

            // Verificar si hay resultados
            if ($top <= 0) {
                return false; // O manejar el error de otra manera
            }

            // get order id
            $orderid = (string)$this->client->getPago(env('SEARCH_ORDER_URI') . $order)->id;

            //get item id whit api mercado pago, return true
            $iditemReference = (string)$this->client->getPago(env('SEARCH_ORDER_URI') . $order)->items[0]->id;
            $iditemResult = Payment::where(['itemid' => $iditemReference])->count();
            $iditemResult = $iditemResult == 1 ? true : false;

            if ($paymentsTable == 1) {
                //Result approved
                if ($iditemResult == true) {

                    $payid = (string)$this->client->getPago(env('SEARCH_ORDER_URI') . $order)->payments[0]->id;

                    $status = (string)$this->client->getPago(env('SEARCH_PAYMENTS_ID') . $payid)->status;

                    Payment::where('participante_id', '=', $participante[0]->participante_id)->update(['paid' => $status, 'payid' => $payid, 'orderid' => $orderid, 'updated_at' => Carbon::now()]);
                }
            }

            return true;

        } catch (\Exception $e) {
            return false;
        }
    }
}


