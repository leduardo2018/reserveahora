<?php

namespace App\Http\Controllers\Scraps;


//use App\Models\CityDestiny;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Helpers\HelpersController;
use Illuminate\Http\JsonResponse;
use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;
//use App\Dao\BookingCityDestinyDao;

class BookingScrapperController extends Controller
{

    private $reshotels = array();

    /**
     * Function to scrap a general page
     * @return json object
     */
    public function scrapSearchByCityAndDate(Request $request){
        try{
            $var = $request->json()->all();
            $url    =   'https://www.booking.com/';
            $endpoint   =   'searchresults.es.html?';

            $crawl = new Client();
            $guzzleClient = new GuzzleClient(array(
                'timeout' => 600,
            ));
            $crawl->setClient($guzzleClient);

            $crawl->setHeader('Accept', '*/*');
            $crawl->setHeader('Cache-Control', 'max-age=0');
            $crawl->setHeader('Connection', 'keep-alive');
            $crawl->setHeader('Keep-Alive', '600');
            $crawl->setHeader('Accept-Charset','ISO-8859-1,utf-8;q=0.7,*;q=0.7' );
            $crawl->setHeader('Accept-Language','en-us,en;q=0.5' );
            $crawl->setHeader('Pragma','');
            $data = array(
                            'label'     =>  'gen173nr-1DCAsoMkINc3VpdGVzLXJlY3Jlb0gKWARoMogBAZgBCsIBA3gxMcgBDNgBA-gBAfgBA5ICAXmoAgM',
                            'city' => $var['destiny']['idcity'],
                            'checkin_monthday'  =>      substr($var['checkin'], 8,2)  ,
                            'checkin_month'     =>      substr($var['checkin'], 5,2),
                            'checkin_year'      =>      substr($var['checkin'], 0,4),
                            'checkout_monthday' =>      substr($var['checkout'], 8,2),
                            'checkout_month'    =>      substr($var['checkout'], 5,2),
                            'checkout_year'     =>      substr($var['checkout'], 0,4),
                            'group_adults'      =>      ($var['adult']['quantity'] != null ? $var['adult']['quantity'] : 0 ),
                            'group_children'    =>      ($var['child']['quantity'] != null ? $var['child']['quantity'] : 0),
                            'no_rooms'          =>      ($var['destiny']['idcity']  !=  null ? $var['destiny']['idcity'] : 0),
                            'ss_raw'            =>      $var['destiny']['city'],
                            'ac_position'       =>      0,
                            'ac_langcode'       =>      'es',
                            'dest_id'           =>      $var['destiny']['idcity'],
                            'dest_type'         =>      $var['destiny']['type'],
                            'search_selected'   =>      'true',
                    );

            $crawler = $crawl->request('GET', $url.$endpoint.http_build_query($data), [
                'stream' => true,
                'read_timeout' => 100,
            ] );

            $pages = preg_replace( '/[^A-Za-z0-9\-]/', '', ($crawler->filter('.results-paging .x-list li')->count() > 0)
                ? $crawler->filter('.results-paging .x-list li:nth-last-child(2)')->text()
                : 0);

            for ($i = 0; $i < 1; $i++) {
                if ( $i != 0 ) {
                    $p = $i * 15;
                    $crawler = $crawl->request('GET', $url.$endpoint.http_build_query($data).'&rows=15&offset='.$p,['stream' => true,'read_timeout' => 100,]);
                }

                $nodescount = $crawler->filter( '.hotellist_wrap  .sr_item')->count() ;
                if($nodescount > 0){
                    try{
                    $crawler->filter( '.hotellist_wrap  .sr_item')
                             ->each( function ( $node ) {
                            if(!empty($node)){

                                $cname = $node->filter( '.sr-hotel__name' )->count();
                                if($cname != '0'){
                                    $name = trim( preg_replace( '/[^;\sa-zA-Z0-9áéíóúüñÁÉÍÓÚÜÑ]+/u', ' ', $node->filter( '.sr-hotel__name' )->text() ) );
                                }else{
                                    $name = "";
                                }

                                $cprice = $node->filter( '.price' )->count();
                                if($cprice != '0'){
                                    $price = str_replace( 'COP', '', preg_replace( '/[^A-Za-z0-9\-]/', '', $node->filter( '.price' )->text() ) );
                                }else{
                                    $price = "";
                                }

                                $hotelid = $node->filter('.sr_item')->attr('data-hotelid');

                                if($name === "" && $price === ""){
                                    $name = "No disponible";
                                    $price = "No disponibile";
                                }else if($name != "" && $price === ""){
                                    $price = 'No disponible';
                                }else if($name === "" && $price != ""){
                                    $name = "No disponible";
                                }

                                if(!in_array($name, $this->reshotels)){
                                    $this->reshotels[] = array(
                                        'id'    =>  $hotelid,
                                        'name'  =>  $name,
                                        'price' =>  $price
                                    );
                                }
                            }
                        });

                    }catch(\Exception $e){
                        return response()->json($e);
                    }
                }else{
                    return response()->json("No existen nodos");
                }
            }
            $result =  HelpersController::super_unique($this->reshotels, 'name');
            return response(array('scrapped'=>$result));
        } catch(\Exception $e){
            return  $e;
        }
    }

    // /**
    //  * Function to scrap by hotel
    //  * 
    //  * @return json object
    //  */
    // public function scrapByHotel(){
    //     try{
    //         $url = 'https://www.expedia.com/Hotel-Search?destination=Medellin%2C+Colombia&latLong=6.234093%2C-75.592979&regionId=2246&startDate=02%2F24%2F2018&endDate=02%2F25%2F2018&_xpid=11905%7C1&adults=2&children=0';
    //         $endpoint   =
    //         $crawl = new Client();
           
    //         $crawler = $crawl->request('GET', $url );
    //         //var_dump($crawler);
    //        $res =  array();
    //        $resarray = array();
    //         //$crawler->filter('.sr-hotel__name')
    //         $crawler->filter('.hotelTitle')
    //                 ->each(function ($node, $index ) {
    //                       dump($node->text());
    //                       $this->res[$index] =  $node->text();
    //                      }); 
        
    //        return response($this->res);
    //     } catch(\Exception $e){
    //         return response()->json($e);
    //     }
    // }

    // /**
    //  * Function to fill an array
    //  * 
    //  * @return array
    //  */
    // public function getCityDestinationsInfo(){
    //     try{
    //         $url = 'http://crawl.reserveahora.com/';
    //         $endpoint   = '/destinationfinder/countries/co.es.html?';

    //         $crawl = new Client();
    //         $guzzleClient = new GuzzleClient(array(
    //             'timeout' => 600,
    //         ));
    //         $crawl->setClient($guzzleClient);

    //         $crawl->setHeader('Accept', '*/*');
    //         $crawl->setHeader('Cache-Control', 'max-age=0');
    //         $crawl->setHeader('Connection', 'keep-alive');
    //         $crawl->setHeader('Keep-Alive', '600');
    //         $crawl->setHeader('Accept-Charset','ISO-8859-1,utf-8;q=0.7,*;q=0.7' );
    //         $crawl->setHeader('Accept-Language','en-us,en;q=0.5' );
    //         $crawl->setHeader('Pragma','');
    //         $data = array(
    //             'label'     =>  'gen173nr-1DCAsoMkINc3VpdGVzLXJlY3Jlb0gKWARoMogBAZgBCsIBA3gxMcgBDNgBA-gBAfgBA5ICAXmoAgM',
    //         );

    //         $crawler = $crawl->request('GET', $url, [
    //             'stream' => true,
    //             'read_timeout' => 100,
    //         ] );

    //         $crawler->filter( '.dcard_fake > .dsf_city')->each(function ($node, $i) {
    //                 $this->city   =   $node->filter('.card_border > .min_tile_link > .min_tile_container > .gradual_gradient > h2')->count();
    //                 $this->reshotels[] = [
    //                     'id'    =>  $node->attr('data-ufi'),
    //                     'city'  =>  trim( preg_replace( '/[^;\sa-zA-ZáéíóúüñÁÉÍÓÚÜÑ]+/u', ' ', $node->filter('.card_border > .min_tile_link > .min_tile_container > .gradual_gradient > h2')->text() ) )
    //                 ];
    //         });
    //        $citydestinydao = new BookingCityDestinyDao(new CityDestiny());
    //         $rtn = array();
    //         foreach ($this->reshotels as $item ){
    //             $rtn[] = $citydestinydao->create($item);
    //         }
    //        return response()->json(array('data'=>$this->reshotels, 'resp'=>$rtn));

    //     }catch(\Exception $e){
    //         return $e;
    //     }
    // }

 }