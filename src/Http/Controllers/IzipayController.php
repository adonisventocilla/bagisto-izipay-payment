<?php

namespace AVS\Izipay\Http\Controllers;

use AVS\Izipay\Helpers\Ipn;
use Webkul\Checkout\Facades\Cart;
use Webkul\Sales\Repositories\OrderRepository;

/**
 * Izipay controller
 *
 * @author  Adonis Ventocilla <ventocilla.adonis@gmail.com>
 */
class IzipayController extends Controller
{
    /**
     * OrderRepository $orderRepository
     *
     * @var \Webkul\Sales\Repositories\OrderRepository
     */
    protected $orderRepository;

    /**
     * IPN $ipnHelper
     *
     * @var \AVS\Izipay\Helpers\Ipn
     */
    protected $ipnHelper;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Attribute\Repositories\OrderRepository  $orderRepository
     * @param  \AVS\Izipay\Helpers\Ipn  $ipnHelper
     * @return void
     */

    public function __construct(
        OrderRepository $orderRepository,
        Ipn $ipnHelper
    ) {
        $this->orderRepository = $orderRepository;
        $this->ipnHelper = $ipnHelper;
    }
    
    /**
     * Redirects to the izipay.
     *
     * @return \Illuminate\View\View
     */
    public function redirect()
    {
        return view('izipay::redirect');
    }

    /**
     * Cancel payment from izipay.
     *
     * @return \Illuminate\Http\Response
     */
    public function cancel()
    {
        session()->flash('error', 'Se ha cancelado el pago por Izipay');

        return redirect()->route('shop.checkout.cart.index');
    }
    /**
     * Success payment.
     *
     * @return \Illuminate\Http\Response
     */
    public function success()
    {
        $order = $this->orderRepository->create(Cart::prepareDataForOrder());

        Cart::deActivateCart();

        session()->flash('order', $order);

        return redirect()->route('shop.checkout.success');
    }

    /**
     * Failure payment.
     *
     * @return \Illuminate\Http\Response
     */
    public function failure()
    {
        session()->flash('error', 'Se ha producido un error en el pago por Izipay');

        return redirect()->route('shop.checkout.cart.index');
    }

    /**
     * Refused payment.
     *
     * @return \Illuminate\Http\Response
     */
    public function refused()
    {
        session()->flash('error', 'Se ha rechazado el pago por Izipay');

        return redirect()->route('shop.checkout.cart.index');
    }

    /**
     * Izipay IPN listener.
     *
     * @return \Illuminate\Http\Response
     */
    public function ipn()
    {
        $this->ipnHelper->processIpn(request()->all());
    }
}
