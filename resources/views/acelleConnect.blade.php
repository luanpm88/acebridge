<link rel='stylesheet' href='{{ acebridge_public_url('css/bootstrap.min.css') }}' />
<link rel='stylesheet' href='{{ acebridge_public_url('css/acellesync.css') }}' />

<div style="width: 600px" class="p-5">
    <img class="mb-4" style="margin-left:-14px" width="160px" src='<?php echo acebridge_public_url('image/saas.svg') ?>' />
    <h1>{{ esc_html__('WordPress Plugin for Acelle is activated', 'beemail') }}</h1>
    <p class="mt-3">{{ esc_html__('Your WordPress site is now available for access from Acelle Mail.
        Use the following Connection URL if you are asked by Acelle.
    ') }}</p>
    <div class="input-group mb-3">
        <input type="text" class="form-control bg-danger text-white readonly link"
            placeholder="" readonly
            value="{{ get_rest_url() }}acelle/bridge"
        >
        <div class="input-group-append">
            <button class="button button-primary bg-dark button-copy" type="button">Copy</button>
        </div>
    </div>
    <p>
        {{ esc_html__('Click here to temporarily', 'beemail') }}
        <a href="">{{ esc_html__('disable', 'beemail') }}</a>
        {{ esc_html__('it', 'beemail') }}
    </p>

    <p class="mt-5">
        {{ esc_html__('Last connection', 'beemail') }}: {{ Carbon\Carbon::now()->subMinute(10)->diffForHumans() }}
        <br />
        {{ esc_html__('From', 'beemail') }} "<strong>My Acelle site</strong>"
        {{ esc_html__('with IP address of', 'beemail') }} {{ $_SERVER['SERVER_ADDR'] }}
    </p>
</div>

<script>
"use strict";

    var $ = jQuery;
    function copyToClipboard(text) {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val(text).select();
        document.execCommand("copy");
        $temp.remove();
    }

    // copy shortcode
    $(document).on('click', '.button-copy', function() {        
        copyToClipboard($(this).closest('.input-group').find('.link').val());

        alert('{{ esc_html__('The connection url was copied to clipboard!', 'beemail') }}');
    });
</script>