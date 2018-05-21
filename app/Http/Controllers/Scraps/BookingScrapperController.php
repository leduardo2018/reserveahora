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
    public function scrapSearchByCityAndDate(Request $request)
    {
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
            ]);

          

            $pages = preg_replace( '/[^A-Za-z0-9\-]/', '', ($crawler->filter('.results-paging .x-list li')->count() > 0)
                ? $crawler->filter('.results-paging .x-list li:nth-last-child(2)')->text()
                : 0);

            for ($i = 0; $i < 3; $i++) {
                if ( $i != 0 ) {
                    $p = $i * 15;
                    $crawler = $crawl->request('GET', $url.$endpoint.http_build_query($data).'&rows=15&offset='.$p,
                        ['stream' => true,
                        'read_timeout' => 100,]);
                }

                $nodescount = $crawler->filter( '.hotellist_wrap .sr_item')->count();
                if($nodescount > 0){
                    try{
                        $crawler->filter( '.hotellist_wrap .sr_item')
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


                                $clink = $node->filter( '.sr_item_photo_link' )->count();
                                if($clink != '0'){
                                    $link =$node->filter( '.sr_item_photo_link')->extract(array('href') ) ;
                                }else{
                                    $link = "";
                                }

                                $cimage = $node->filter( '.hotel_image' )->count();
                                if($cimage != '0'){
                                    $image =$node->filter( '.hotel_image')->extract(array('src') ) ;
                                }else{
                                    $image = "";
                                }




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
                                        'price' =>  $price,
                                        'link' =>   $link,
                                        'image' =>  $image
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
        }  catch(\Exception $e){
            return  $e;
        }
    }



    public function scrapSearchByhotel(Request $request)
    {
       // try{
        $var = $request->json()->all();

     
         $url = 'https://www.booking.com/hotel/co/altamar-cartagena.es.html?label=gen173nr-1DCAEoggJCAlhYSDNYBGgyiAEBmAEKuAEGyAEM2AED6AEBkgIBeagCAw;sid=bc3e43896557080384f6fc1969225d5e;all_sr_blocks=27699803_101112910_0_1_0;bshb=2;checkin=2018-05-25;checkout=2018-05-26;dest_id=-579943;dest_type=city;dist=0;group_adults=2;hapos=1;highlighted_blocks=27699803_101112910_0_1_0;hpos=1;room1=A%2CA;sb_price_type=total;srepoch=1526912134;srfid=fe22ca56580c31d062bd7f91d6e142589000f611X1;srpvid=af0c6442dfc8002e;type=total;ucfs=1&#hotelTmpl';


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


        $crawler = $crawl->request('GET', $url, [
            'stream' => true,
            'read_timeout' => 100,
        ]);


                   //Scrap de el nombre del hotel
        $titulo_Hotel =      $crawler->filter('.hp__hotel-name')->text();
        var_dump($titulo_Hotel);
        echo "<br>";
        echo "<br>";

            // //Scrap de la direccion completa del hotel
        $direccion_hotel =   $crawler->filter('.hp_address_subtitle')->text();  
        var_dump($direccion_hotel); 
        echo "<br>"; 
        echo "<br>";

            // //scrap de la descripcion completa.    
        $descripcion_hotel0 = $crawler->filter('#summary')->text();

        var_dump($descripcion_hotel0);
        echo "<br>";
        echo "<br>";



             //scrap de los servicios 
        $servicios_hotel = $crawler->filter('.hp_desc_important_facilities')->text();

        var_dump($servicios_hotel);
        echo "<br>";
        echo "<br>";





        $nodescount2 = $crawler->filter( '#hp_availability_style_changes .description tbody')->count();
        if($nodescount2 > 0){


                    // try{
         $crawler->filter('#hp_availability_style_changes .description table tbody ')
         ->each( function ( $node ) {
            if(!empty($node)){



                $node->filter('tr')->filter('td')->filter('.hprt-roomtype-link')
                ->each(function($noderooms){

                    $listado_noderroms = $noderooms->text();            

                    var_dump($listado_noderroms );
                    echo "<br>";

                });


                $node->filter('tr')->filter('td')->filter('.hprt-occupancy-occupancy-info')->filter('i')
                ->each(function($noderooms2){

                   $listado_noderroms2 = $noderooms2->count();

                   var_dump($listado_noderroms2);
                   echo "<br>";

               });


                 $node->filter('tr')->filter('td')->filter('.hprt-price-price')
                ->each(function($noderooms3){

                   $listado_noderroms3 = $noderooms3->text();

                   var_dump($listado_noderroms3);
                   echo "<br>";

               });

                  $node->filter('tr')->filter('td')->filter('.hprt-conditions')
                ->each(function($noderooms4){

                   $listado_noderroms4 = $noderooms4->text();

                   var_dump($listado_noderroms4);
                   echo "<br>";

               });

                  $node->filter('tr')->filter('td')->filter('.hprt-nos-select')
                ->each(function($noderooms5){

                   $listado_noderroms5 = $noderooms5->text();

                   var_dump($listado_noderroms5);
                   echo "<br>";
                   echo "<br>";

               });
                    





                  }


        });


           // $crawler->filter('#hp_availability_style_changes .description table tbody ')
           //     ->each( function ( $node4 ) {    
              
           //            $listado_noderroms4 = $node4->html();
                        
           //              var_dump($listado_noderroms4);
           //              echo "<br>";
            
           //               });


         // $crawler->filter('#hp_availability_style_changes .description tbody')
         // ->each( function ( $nodedisponibility ) {
         //    if(!empty($nodedisponibility)){


         //       $nodedisponibility->filter('tr')->filter('td')->filter('td')
         //       ->each(function($noderooms2){

         //           $listado_disponibilidad = $noderooms2->html();
         //                         // echo "Imprime esta monda";
         //                         // echo "<br>";
         //           var_dump($listado_disponibilidad);
         //                          // var_dump($noderooms2->text());


         //            });

         //          }
         //             });
         //           // }catch(\Exception $e){
         //             //   return response()->json($e);
         //           // }
         //         }else{
         //            return response()->json("No existen nodos");
         //        }

        //     $result =  HelpersController::super_unique($this->reshotels, 'name');
        //     return response(array('scrapped'=>$result));
        //           }  catch(\Exception $e){
        //     return  $e;
          //}
    }

 // {

 //           // $var = $request->json()->all();

 //            $url = 'https://www.booking.com/hotel/co/47-medellin-street.es.html?';

 //            $crawl = new Client();
 //            $guzzleClient = new GuzzleClient(array(
 //                'timeout' => 600,
 //            ));

 //            $crawl->setClient($guzzleClient);

 //            $crawler = $crawl->request('GET', $url, [
 //                'stream' => true,
 //                'read_timeout' => 100,
 //            ]);



 //                                 //Scrap de el nombre del hotel
 //            // $titulo_Hotel =      $crawler->filter('.hp__hotel-name')->text();
 //            // //Scrap de la direccion completa del hotel
 //            // $direccion_hotel =   $crawler->filter('.hp_address_subtitle')->text();    
 //            // //scrap de la descripcion completa.    

 //            // $descripcion_hotel1 = $crawler->filter('#summary')->children()->eq(1)->text();
 //            // $descripcion_hotel2 = $crawler->filter('#summary')->children()->eq(2)->text();
 //            // $descripcion_hotel3 = $crawler->filter('#summary')->children()->eq(3)->text();
 //            // $descripcion_hotel4 = $crawler->filter('#summary')->children()->eq(4)->text();
 //            // $descripcion_hotel5 = $crawler->filter('#summary')->children()->eq(5)->text();
 //            // //scrap de los servicios 
 //            // $servicios_hotel = $crawler->filter('.hp_desc_important_facilities')->text();


 //            $form = $crawler->filter('#hp_availability_style_changes .description #available_rooms .roomArea #hprt-form .hprt-table')->count();

 //            dd($form);
 //             // if($form > 0){

 //             //           return response()->json("Si existen nodos table td");

 //             //           } else{

 //             //        return response()->json("No existen nodos table td");
 //             //           } 

 //            //->children()->filter('div');


 //           // ->children()->filter('thead')->children()->filter('tr')->children()->filter('th')->text();

 //        // $nodescount = $crawler->filter('table')->children()->filter('tbody')->count();


 //        // //->children()->filter('tr')->children()->filter('td')->children()->filter('div')
 //        //          if($nodescount > 0){


 //        //              $nodescount2 =$crawler->filter('tr')->count();
 //        //              if($nodescount2 > 0){


 //        //                  $nodescount3 =$crawler->filter('td')->count();
 //        //                    if($nodescount3 > 0){


 //        //                return response()->json("Si existen nodos td");

 //        //                } else{

 //        //             return response()->json("No existen nodos td");
 //        //                } 



 //        //                return response()->json("Si existen nodos tr");

 //        //                } else{

 //        //             return response()->json("No existen nodos tr");
 //        //                }


 //        //               // $cname = $node->filter( '.sr-hotel__name' )->count();
 //        //               //     if($cname != '0'){
 //        //               //        $name = trim( preg_replace( '/[^;\sa-zA-Z0-9áéíóúüñÁÉÍÓÚÜÑ]+/u', ' ', $node->filter( '.sr-hotel__name' )->text() ) );
 //        //               //           }else{
 //        //               //               $name = "";
 //        //               //           }


 //        //               echo response()->json("Si existen nodos tbody");


 //        //                } else{
 //        //             return response()->json("No existen nodos tbody");
 //        //         }
 //             //                    $ctipodecama = $node->filter( '.hprt-roomtype-icon-link' )->count();
 //             //                    if($ctipodecama != '0'){
 //             //                        $tipodecama = $node->filter( '.hprt-roomtype-icon-link')->text();
 //             //                    }else{
 //             //                        $tipodecama = "";


 //            // //scrap del tipo de habitacion          
 //            //$tipos_habitaciones = $crawler->filter('.hprt-table-cell-roomtype')->children()->filter('.hprt-roomtype-icon-link')->text();

 //           // $a= $crawler->filter('.hprt-table-cell-roomtype')->children()->eq(2)->text();



 //              //$link->text());


 //          //   echo($titulo_Hotel);
 //          //   echo "<br>";
 //          //   echo "<br>";
 //          //   echo($direccion_hotel);
 //          //   echo "<br>";
 //          //   echo "<br>";
 //          //   echo($descripcion_hotel1);
 //          //   echo "<br>";
 //          //   echo "<br>";
 //          //   echo($descripcion_hotel2);
 //          //   echo "<br>";
 //          //   echo "<br>";
 //          //   echo($descripcion_hotel3);
 //          //    echo "<br>";
 //          //   echo "<br>";
 //          //   echo($descripcion_hotel4);
 //          //   echo "<br>";
 //          //   echo "<br>";
 //          //   echo($descripcion_hotel5);
 //          //   echo "<br>";
 //          //   echo "<br>";
 //          //   echo($servicios_hotel);
 //          //   echo "<br>";
 //          //   echo "<br>";
 //          // // var_dump($a);




 //    }

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
     }



}