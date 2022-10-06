<?php

namespace AVS\Izipay\Payment;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Webkul\Payment\Payment\Payment;

class Izipay extends Payment
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $code  = 'izipay';

    public function getRedirectUrl()
    {
        return route('izipay.payment.redirect');
    }

    /**
     * Return form field array.
     *
     * @return array
     */
    public function getFormFields()
    {
        $cart = $this->getCart();

        $fields = [
            'vads_site_id'          => core()->getConfigData('sales.paymentmethods.izipay.vads_site_id'),
            'vads_ctx_mode'         => core()->getConfigData('sales.paymentmethods.izipay.vads_ctx_mode'),
            'vads_currency'         => core()->getConfigData('sales.paymentmethods.izipay.vads_currency'),
            'vads_version'          => 'V2',
            'vads_action_mode'      => 'INTERACTIVE',
            'vads_page_action'      => 'PAYMENT',
            'vads_payment_config'   => 'SINGLE',
            'vads_url_return'       => route('izipay.payment.cancel'),
            'vads_url_success'      => route('izipay.payment.success'),
            'vads_url_cancel'       => route('izipay.payment.cancel'),
            'vads_url_refused'      => route('izipay.payment.refused'),
            'vads_url_check'        => route('izipay.payment.ipn'),
            'vads_return_mode'      => 'GET',
            'vads_trans_date'       => date('YmdHis', strtotime(Carbon::now()->timezone('UTC'))),
            'vads_trans_id'         => substr(md5(uniqid(rand(), true)), 0, 6),
            'vads_ext_info_id_cart' => $cart->id,
            'vads_amount'           => intval($cart->grand_total)*100,
            'vads_available_languages' => 'es',
        ];

        $dataCartCustomer = $this->getCustomerData();
        $fields = array_merge($fields, $dataCartCustomer);

        $signature = $this->calculateSignature($fields);

        $fields = array_merge($fields, array(
            'signature' => $signature,
        ));

        Log::info('Izipay getFormFields:', array(
            'fields' => $fields,
            'date' => date('YmdHis'),
        ));

        return $fields;
    }

    /**
     * Función que realiza la firma de la data.
     * @param array $params: matriz que contiene los campos que se enviarán en el formulario.
     * @param string $key: clave de PRUEBA o PRODUCCION.
     * @return string
     */
    public function calculateSignature($params)
    {
        // Inicialización de la variable que contendrá el string a cifrar
        $content_signature = "";
        $key = core()->getConfigData('sales.paymentmethods.izipay.clave');
        // Ordenar los campos alfabéticamente
        ksort($params);

        foreach ($params as $name => $value) {
            // Recuperación de los campos vads_
            if (substr($name, 0, 5) == 'vads_') {
                // Concatenación con el separador "+"
                $content_signature .= $value . "+";
            }
        }

        // Añadir la clave al final del string
        $content_signature .= $key;

        // Codificación base64 del string cifrada con el algoritmo HMAC-SHA-256
        $signature = base64_encode(hash_hmac('sha256', $content_signature, $key, true));

        return $signature;
    }

    /**
     * Devuelve datos de la persona que hace la compra.
     *
     * @return array
     */
    public function getCustomerData()
    {
        $cart = $this->getCart();

        if ($cart->shipping_address) {
            $dataShipping = [
                "vads_ship_to_first_name" => utf8_encode($cart->shipping_address->first_name),
                "vads_ship_to_phone_num" => utf8_encode($cart->shipping_address->phone),
                "vads_ship_to_last_name" => utf8_encode($cart->shipping_address->last_name),
                "vads_ship_to_street" => utf8_encode($cart->shipping_address->address1),
                "vads_ship_to_zip" => utf8_encode($cart->shipping_address->postcode),
                "vads_ship_to_country" => utf8_encode($cart->shipping_address->country),
                "vads_ship_to_city" => utf8_encode($cart->shipping_address->city),
                "vads_ship_to_state" => utf8_encode($cart->shipping_address->state),
            ];
        } else {
            $dataShipping = [
                "vads_ship_to_first_name" => utf8_encode($cart->billing_address->first_name),
                "vads_ship_to_phone_num" => utf8_encode($cart->billing_address->phone),
                "vads_ship_to_last_name" => utf8_encode($cart->billing_address->last_name),
                "vads_ship_to_street" => utf8_encode($cart->billing_address->address1),
                "vads_ship_to_zip" => utf8_encode($cart->billing_address->postcode),
                "vads_ship_to_country" => utf8_encode($cart->billing_address->country),
                "vads_ship_to_city" => utf8_encode($cart->billing_address->city),
                "vads_ship_to_state" => utf8_encode($cart->billing_address->state),
            ];
        }

        $dataBilling = [
            "vads_cust_first_name" => utf8_encode($cart->shipping_address->first_name),
            "vads_cust_phone" => utf8_encode($cart->shipping_address->phone),
            "vads_cust_last_name" => utf8_encode($cart->shipping_address->last_name),
            "vads_cust_email" => utf8_encode($cart->shipping_address->email),
            "vads_cust_address" => utf8_encode($cart->shipping_address->address1),
            "vads_cust_zip" => utf8_encode($cart->shipping_address->postcode),
            "vads_cust_country" => utf8_encode($cart->shipping_address->country),
            "vads_cust_city" => utf8_encode($cart->shipping_address->city),
            "vads_cust_state" => utf8_encode($cart->shipping_address->state),
        ];

        return array_merge($dataBilling, $dataShipping);
    }
}
