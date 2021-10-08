<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WoocommerceSession extends Model
{
    protected $connection = 'mysql_wp';

    /**
     * Get abandonded carts.
     *
     * @var collect
     */
    public static function getAbandondedCarts()
    {
        $sessions = self::where('session_value', 'LIKE', '%"cart"%')
            ->where('session_value', 'NOT LIKE', '%"cart";s:6%')
            ->get();

        $carts = $sessions->map(function($session) {
            $data = unserialize($session->session_value);
            $customer = unserialize($data["customer"]);
            $cart = unserialize($data["cart"]);

            return [
                'customer' => $customer,
                'cart' => $cart,
            ];
        });

        return $carts;
    }
}
