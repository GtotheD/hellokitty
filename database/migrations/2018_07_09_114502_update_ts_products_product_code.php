<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTsProductsProductCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // テーブルのアップデート
        $products =  DB::table('ts_products')->select('id', 'product_code')->get();
        foreach ($products as $product) {
            $baseProductDode = preg_replace('/([A-Z]|[a-z]){1,2}$/','' ,$product->product_code);
            $isDummy = preg_match('/([A-Z]|[a-z]){1,2}$/', $product->product_code, $matches);
            DB::table('ts_products')
                ->where('id', $product->id)
                ->update([
                    'base_product_code' => $baseProductDode,
                    'is_dummy' => $isDummy,
                ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('ts_products')
            ->update([
                'base_product_code' => null,
                'is_dummy' => null,
            ]);
    }
}
