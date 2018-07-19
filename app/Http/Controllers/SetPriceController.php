<?php

namespace App\Http\Controllers;

use App\Charts\PriceChart;
use App\Discount;
use App\PriceHistory;
use App\Product;
use Illuminate\Http\Request;

class SetPriceController extends Controller
{
    /***
     * функция, которая отображает информацию о продукте с возможностью выбора цены
     */
    public function index(){

        $products = Product::all();
        $discounts = Discount::all();

        if (!isset($_COOKIE['setPriceWay'])){
            setcookie("setPriceWay",1);
            $_COOKIE['setPriceWay'] = 1;
        }

        //если выбран первый способ определения цены
        if ($_COOKIE['setPriceWay'] == 1)
            foreach ($products as $prod)
                $this->setPriceFirstWay($prod);
        else
            //если выбран второй способ определения цены
            foreach ($products as $prod)
                $this->setPriceSecondWay($prod);


        //получение данных (дата установки цены и цена) для построения графика
        //в качестве параметра для выбора данных используется тип определения цены
        $ph = PriceHistory::where('type','=',$_COOKIE['setPriceWay'])->get();
        $labels = [];
        $dataset = [];
        foreach ($ph as $value){
            $labels[] = date("d.m.Y H:i",$value->date);
            $dataset[] = $value->price;
        }


        $chart = new PriceChart;
        $chart->labels($labels)
            ->dataset('График цены', 'line', $dataset)
            ->options([
                'color' => '#ff0000'
            ]);


        return view('setPrice',[
            'products' => $products,
            'discounts' => $discounts,
            'chart' => $chart
        ]);
    }

    /***
     * данная функция получает по ajax'у id скидки и id продукта, цену для которого необходимо изменить
     */
    public function setPrice(Request $request){

        $discountID = $request->all()['discountID'];
        $prodID = $request->all()['prodID'];
        $prod = Product::find($prodID);

        $history = new PriceHistory;

        //если цена определяется не скидкой, а базовой ценой
        if ($discountID == 'default'){
            $prod->discount_id = null;
            $prod->save();

            //запись в таблицу истории установки цены
            $history->product_id = $prod->id;
            $history->price = $prod->price;
            $history->date = time();
            $history->type = $_COOKIE['setPriceWay'];

            $history->save();

            return response()->json(['discount'=>'default']);
        } else{
            //если цена определяется скидкой
            $discount = Discount::find($discountID);
            if ($discount->date_start !== 0)
                $discount->date_start = date("d.m.Y",$discount->date_start);
            else
                $discount->date_start = "";

            if ($discount->date_end !== 0)
                $discount->date_end = date("d.m.Y",$discount->date_end);
            else
                $discount->date_end = "";

            $prod->discount_id = $discountID;
            $prod->save();

            //запись в таблицу истории установки цены
            $history->product_id = $prod->id;
            $history->price = $discount->price;
            $history->date = time();
            $history->type = $_COOKIE['setPriceWay'];

            $history->save();

            return response()->json(['discount'=>$discount]);
        }
    }

    /***
     * две функции для определения цены
     *
     * первая определяет цену исходя из текущей даты.
     * Для более наглядного примера, исходя из данных в ТЗ, определена конкретная дата
     *
     *вторая функция определяет цену исходя из истории цен, выбирая самую последнюю
     */

    public function setPriceFirstWay(Product $prod){
//        $curentDate = time();
        $curentDate = strtotime("01.08.2016");
        $discount = Discount::where('date_start','<',$curentDate)
                            ->where('date_end','>',$curentDate)
                            ->get()->sortBy('duration')->first();

        $prod->discount_id = $discount->id;
        $prod->save();
    }

    public function setPriceSecondWay(Product $prod){
        $historyPrice = $prod->history->sortByDesc('date')->first();

        $prod->discount_id = Discount::where('price','=',$historyPrice->price)->first()->id ?? null;
        $prod->save();

    }

}