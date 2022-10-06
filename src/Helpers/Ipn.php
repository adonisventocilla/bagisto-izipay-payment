<?php

namespace AVS\Izipay\Helpers;

use AVS\Izipay\Payment\Izipay;
use Illuminate\Support\Facades\Log;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Repositories\InvoiceRepository;

class Ipn
{
    /**
     * IPN post data.
     *
     * @var array
     */
    protected $post;

    /**
     * Izipay $izipay
     *
     * @var \AVS\Izipay\Payment\Izipay
     */
    protected $iziPay;

    /**
     * Order $order
     *
     * @var \Webkul\Sales\Contracts\Order
     */
    protected $order;

    /**
     * OrderRepository $orderRepository
     *
     * @var \Webkul\Sales\Repositories\OrderRepository
     */
    protected $orderRepository;

    /**
     * InvoiceRepository $invoiceRepository
     *
     * @var \Webkul\Sales\Repositories\InvoiceRepository
     */
    protected $invoiceRepository;

    /**
     * Create a new helper instance.
     *
     * @param  \Webkul\Sales\Repositories\OrderRepository  $orderRepository
     * @param  \Webkul\Sales\Repositories\InvoiceRepository  $invoiceRepository
     * @param  \AVS\Izipay\Payment\Izipay  $iziPay
     * @return void
     */
    public function __construct(
        Izipay $iziPay,
        OrderRepository $orderRepository,
        InvoiceRepository $invoiceRepository
    )
    {
        $this->iziPay = $iziPay;
        $this->orderRepository = $orderRepository;
        $this->invoiceRepository = $invoiceRepository;
    }

    /**
     * This function process the IPN sent from paypal end.
     *
     * @param  array  $post
     * @return null|void|\Exception
     */
    public function processIpn($post)
    {
        $this->post = $post;
        if (! $this->validateIpn()) {
            return;
        }
        try {
            if(isset($this->post['vads_trans_status'])){
                //...
            } else {
                $this->getOrder();

                $this->processOrder();
            }
        } catch (\Exception $e) {
            throw $e;
        }

        Log::info('Izipay IPN request: ' . print_r($post, true));
    }

    /**
     * Load order via IPN invoice id.
     *
     * @return void
     */
    protected function getOrder()
    {
        if (empty($this->order)) {
            $this->order = $this->orderRepository->findOneByField(['cart_id' => $this->post['vads_ext_info_id_cart']]);
        }
    }

    /**
     * Process order and create invoice.
     *
     * @return void
     */
    protected function processOrder()
    {
        if ($this->post['vads_trans_status'] == 'AUTHORISED') {
            if ($this->post['vads_amount'] != intval($this->order->grand_total)*100) {
                return;
            } else {
                $this->orderRepository->update(['status' => 'processing'], $this->order->id);

                if ($this->order->canInvoice()) {
                    $invoice = $this->invoiceRepository->create($this->prepareInvoiceData());
                }
            }
        }
    }

    /**
     * Prepares order's invoice data for creation.
     *
     * @return array
     */
    protected function prepareInvoiceData()
    {
        $invoiceData = ['order_id' => $this->order->id];

        foreach ($this->order->items as $item) {
            $invoiceData['invoice']['items'][$item->id] = $item->qty_to_invoice;
        }

        return $invoiceData;
    }

    /**
     * Validate IPN request.
     *
     * @return array
     */
    protected function validateIpn()
    {
        $signature = $this->post['signature'];

        $calculatedSignature = $this->iziPay->calculateSignature($this->post);

        if ($signature == $calculatedSignature) {
            return true;
        }

        return false;
    }
}