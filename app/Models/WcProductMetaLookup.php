<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WcProductMetaLookup extends Model
{
    protected $connection = 'mysql_wp';
    protected $table = 'wc_product_meta_lookup';

    /**
     * Get abandonded carts.
     *
     * @var collect
     */
    public static function getProductOptions()
    {
        $products = self::select('posts.*')
            ->join('posts', 'posts.id', '=', 'wc_product_meta_lookup.product_id')
            ->get()
            ->map(function ($item, $key) {
                return ['text' => $item->post_title, 'value' => $item->ID];
            })->toArray();

        return $products;
    }

    /**
     * Products select2.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public static function select2($request)
    {
        $page = $request->page;
        $perPage = 15;
        $results = self::select('posts.*')
            ->join('posts', 'posts.id', '=', 'wc_product_meta_lookup.product_id');

        if ($request->q) {
            $results = $results->where('post_title', 'like', '%'.$request->q.'%');
        }
        
        $results = $results->skip(($page-1) * $perPage)->take($perPage)
            ->get()->map(function ($item, $key) {                
                return ['text' => $item->post_title, 'id' => $item->ID];
            })->toArray();

        $json = '{
            "items": ' .json_encode($results). ',
            "more": ' . (empty($results) ? 'false' : 'true') . '
        }';
        return $json;
    }

    /**
     * Products search.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public static function scopeSearch($query, $request)
    {
        $page = $request->page ? : 1;
        $perPage = 15;
        $query = $query->select('posts.*')
            ->join('posts', 'posts.id', '=', 'wc_product_meta_lookup.product_id');

        if ($request->q) {
            $query = $query->where('post_title', 'like', '%'.$request->q.'%');
        }

        if ($request->max) {
            $perPage = $request->max;
        }
        
        return $query->skip(($page-1) * $perPage)->take($perPage)
            ->get()->map(function ($item, $key) {                
                $post = \App\Model\Post::find($item->ID);

                $product   = wc_get_product( $post->ID );
                $image_id  = $product->get_image_id();
                $image_url = wp_get_attachment_image_url( $image_id, 'full' );

                return [
                    'id' => $post->ID,
                    'name' => $post->post_title,
                    'price' => wc_price($product->get_price()),
                    'image' => $image_url,
                    'description' => substr(strip_tags($post->post_content), 0, 100),
                    'link' => get_permalink( $post->ID ),
                ];
            })->toArray();
    }
}
