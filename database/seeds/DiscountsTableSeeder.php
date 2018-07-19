<?php

use App\Discount;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class DiscountsTableSeeder extends Seeder
{
    protected $discounts = array(
                                    array('01.01.2016','','8000'),
                                    array('01.05.2016','01.01.2017','12000'),
                                    array('01.07.2016','10.09.2016','15000'),
                                    array('01.06.2017','20.10.2017','20000'),
                                    array('15.12.2017','31.12.2017','5000')
    );

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=0;$i<count($this->discounts);$i++){

            $discount = new Discount;
            $discount->date_start = strtotime($this->discounts[$i][0]);
            $discount->date_end = strtotime($this->discounts[$i][1]);
            $discount->price = $this->discounts[$i][2];

            if ( ($discount->date_start > 0) && ($discount->date_end > 0) )
                $discount->duration = $discount->date_end - $discount->date_start;

            $discount->save();

        }
    }
}
