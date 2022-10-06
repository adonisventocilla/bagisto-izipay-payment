<?php $izipay = app('AVS\Izipay\Payment\Izipay') ?>

<body data-gr-c-s-loaded="true" cz-shortcut-listen="true">
    Serás redirigido al sitio web de Izipay en unos segundos.

    <form action="https://secure.micuentaweb.pe/vads-payment/" id="izipay_checkout" method="POST">
        <input value="Click aquí si no has sido redirigido en 10 segundos..." name="pagar" type="submit">

        @foreach ($izipay->getFormFields() as $name => $value)

            <input type="hidden" name="{{ $name }}" value="{{ $value }}">

        @endforeach
    </form>

    <script type="text/javascript">
        document.getElementById("izipay_checkout").submit();
    </script>
</body>