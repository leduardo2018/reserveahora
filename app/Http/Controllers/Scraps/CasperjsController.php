<?php

namespace App\Http\Controllers\Scraps;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Browser\Casper;

class CasperjsController extends Controller
{
    private static $casperBinPath = '/usr/local/bin/';

    public static function setUpBeforeClass()
    {
        if (!file_exists(self::$casperBinPath . 'casperjs')) {
            self::$casperBinPath = '~/node_modules/casperjs/bin/';
        }
    }     

    public function scrapCasper(){
        
        $this->setUpBeforeClass();

        $casper = new Casper(self::$casperBinPath);
     
        $casper->setOptions([
            'ignore-ssl-errors' => 'yes',
            //'engine' => 'slimerjs',
            'debug' =>  true
        ]);        

        // navigate to google web page
            $casper->start('https://www.booking.com/searchresults.es.html?city=-592318&checkin_monthday=24&checkin_month=2&checkin_year=2018&checkout_monthday=23&checkout_month=3&checkout_year=2018&group_adults=2&group_children=0&no_rooms=1&from_sf=1');

            $casper->fillFormSelectors(
                    'form.form-class',
                    array(
                            'input#ss' => 'Medellín',
                            "input[name*='checkin_monthday']"   =>  '24',
                            "input[name*='checkin_month']"      =>  '2',
                            "input[name*='checkin_year']"       =>  '2018',
                            "input[name*='checkout_monthday']"  =>  '28',
                            "input[name*='checkout_month']"     =>  '2',
                            "input[name*='checkout_year']"      =>  '2018',

                    ),true);

            $casper->wait(5000);    

            $casper->click('button.sb-searchbox__button');

            $start = $casper->run();
            // check the urls casper get through
            var_dump($casper->getRequestedUrls());

            // need to debug? just check the casper output
            var_dump($casper->getOutput());

    }
}
