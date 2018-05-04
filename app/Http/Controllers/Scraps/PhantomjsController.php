<?php

namespace App\Http\Controllers\Scraps;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use JonnyW\PhantomJs\Client;
  
class PhantomjsController extends Controller
{
    /**
     * Function to scrap with phatomjs
     * 
     */
    public function scrapPhantom(){
        try{
               // $urldst = 'https://www.booking.com/hotel/co/suites-recreo.es.html';
                $urldst = 'https://www.expedia.com/Hotel-Search?destination=Medellin%2C+Colombia&latLong=6.234093%2C-75.592979&regionId=2246&startDate=02%2F24%2F2018&endDate=02%2F25%2F2018&_xpid=11905%7C1&adults=2&children=0';
      
                $client = Client::getInstance();
                $client->getEngine()->setPath('/var/www/api.reserveahora.com/public_html/APIReserveAhora/vendor/bin/phantomjs');

               /* $data = array(
                        'label'     =>  'gen173nr-1DCAsoMkINc3VpdGVzLXJlY3Jlb0gKWARoMogBAZgBCsIBA3gxMcgBDNgBA-gBAfgBA5ICAXmoAgM',
                        'checkin'   =>  '2018-02-26',
                        'checkout'  =>  '2018-02-28',
                        'dest_id'   =>  '-592319',
                        'dest_type' =>  'city',
                        'group_adults'  =>  1,
                        'sb_price_type' =>  'total#hotelTmpl'
                );*/
                
                /** 
                 * @see JonnyW\PhantomJs\Http\Request
                 **/
                $request = $client->getMessageFactory()->createRequest();

                /** 
                 * @see JonnyW\PhantomJs\Http\Response 
                 **/
                $response = $client->getMessageFactory()->createResponse();

                $request->setMethod('GET');
                $request->addHeader('X-CSRF-TOKEN', csrf_token());           
                $request->setUrl($urldst);
               // $request->setRequestData($data); // Set post data

                // Send the request
                 $client->send($request, $response);

                 if($response->getStatus() === 200) {

                    // Dump the requested page content
                    echo $response->getContent();
                }
            return $response->getContent();

        }catch(\Exception $e){
            return  $e;
        }
    }

    public function scrapPhantomBookingSearch(){
        try{
                $urldst = 'https://www.booking.com/searchresults.es.html';
                
                $client = Client::getInstance();
                $client->getEngine()->setPath('/var/www/api.reserveahora.com/public_html/APIReserveAhora/vendor/bin/phantomjs');

                $data = array(
                        
                        'label'     =>  'gen173nr-1DCAsoMkINc3VpdGVzLXJlY3Jlb0gKWARoMogBAZgBCsIBA3gxMcgBDNgBA-gBAfgBA5ICAXmoAgM',
                        'city' => '-592318',
                        'checkin_monthday' =>   '24',
                        'checkin_month' =>  '2',
                        'checkin_year'  =>  '2018',
                        'checkout_monthday' =>  '23',
                        'checkout_month'    =>  '3',
                        'checkout_year' =>  '2018',
                        'group_adults'  =>  '2',
                        'group_children'    =>  '0',
                        'no_rooms'  =>  1,
                        'ss_raw'    => 'Medellín',
                        'ac_position'   => 0,
                        'ac_langcode'   =>  'es',
                        'dest_id'   =>  '-592318',
                        'dest_type' =>  'city',
                        'search_selected'   => 'true',
                        'rows'  =>  40
                );
                
                /** 
                 * @see JonnyW\PhantomJs\Http\Request
                 **/
                $request = $client->getMessageFactory()->createRequest();

                /** 
                 * @see JonnyW\PhantomJs\Http\Response 
                 **/
                $response = $client->getMessageFactory()->createResponse();

                /*$request->setMethod('GET');
                $request->addHeader('X-Booking-AID', '304142');
                $request->addHeader('X-Booking-CSRF', 'ygaOWgAAAAA=kIavSk6vTQlLgqrduMfSnVO3V55nqo7ORDhq88fSPubItbwzI4whGMgQvp_AjQ7m4cTxjoTDFyRYWf_hlltTh2SB0iSfHbjzDuvkj149Fn0bVijEI97mYpN1bW9sonCyy_Rwf-b0MSStBhui1jHIumcmj5le_1i1iiWkFh1xgWD551ROHRc03c-99Dk');
                $request->addHeader('X-Booking-Label', 'gen173nr-1DCAsoMkINc3VpdGVzLXJlY3Jlb0gKWARoMogBAZgBCsIBA3gxMcgBDNgBA-gBAfgBA5ICAXmoAgM');
                $request->addHeader('X-Booking-Pageview-Id', '672c8c05a08a02ab');
                $request->addHeader('X-Booking-Session-Id', 'efbfe1f8e266ded75e5ebc2ecf05ae64');
                $request->addHeader('X-Booking-SiteType-Id', '1');
                $request->addHeader('X-Partner-Channel-Id', '3');
                $request->addHeader('X-Requested-With', 'XMLHttpRequest');
                $request->addHeader('X-Booking-Session-Id', 'efbfe1f8e266ded75e5ebc2ecf05ae64');*/
                $request->addHeader('Accept', '*/*');
                $request->addHeader('Cache-Control', 'max-age=0');
                $request->addHeader('Connection', 'keep-alive');
                $request->addHeader('Keep-Alive', '300');
                $request->addHeader('Accept-Charset','ISO-8859-1,utf-8;q=0.7,*;q=0.7');
                $request->addHeader('Accept-Language','en-us,en;q=0.5');
                $request->addHeader('Pragma','');


                $request->setUrl($urldst);
                $request->setRequestData($data); // Set post data

                // Send the request
                 $client->send($request, $response);

                 if($response->getStatus() === 200) {

                    // Dump the requested page content
                   // echo $response->getContent();
                }
            return $response->getContent();

        }catch(\Exception $e){
            return  $e;
        }
    }
    
}
