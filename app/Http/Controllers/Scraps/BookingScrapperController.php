<?php

namespace App\Http\Controllers\Scraps;


//use App\Models\CityDestiny;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Helpers\HelpersController;
use Illuminate\Http\JsonResponse;
use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;
use App\City;
use DB;
use Illuminate\Support\Facades\Input;

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

            for ($i = 0; $i <1; $i++) {
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


                                $cscore = $node->filter( ' a .review-score-badge' )->count();
                                if($cscore != '0'){
                                    $score = $node->filter( 'a .review-score-badge')->text();
                                }else{
                                    $score = "No posee puntuacion";
                                }

                                $caddress = $node->filter( ' .district_link ' )->count();
                                if($caddress != '0'){
                                    $address2 = $node->filter( '.district_link ')->text();
                                    $address= explode('Mostrar en el mapa',$address2);
                                    $direccion= $address[0];
                                    

                                }else{
                                    $address = "no hay direccion";
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
                                        'image' =>  $image,
                                        'score' => $score,
                                        'direccion' => $direccion
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
        try{
        $var = $request->json()->all();

     
                  // $url = 'https://www.booking.com/hotel/co/aixo-suites.es.html?label=gen173nr-1DCAEoggJCAlhYSDNYBGgyiAEBmAEKuAEGyAEM2AED6AEBkgIBeagCAw;sid=bc3e43896557080384f6fc1969225d5e;all_sr_blocks=284285304_107731904_0_1_0;checkin=2018-05-25;checkout=2018-05-26;dest_id=-579943;dest_type=city;dist=0;group_adults=3;group_children=0;hapos=4;highlighted_blocks=284285304_107731904_0_1_0;hpos=4;no_rooms=1;req_adults=3;req_children=0;room1=A%2CA%2CA;sb_price_type=total;srepoch=1527092805;srfid=d5ff24cc71158e9350e6e3cb669ef91dffeff1e9X4;srpvid=362e73a1629b00fb;type=total;ucfs=1&#hotelTmpl';

                 $url = $var['url'];


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
               $titulo_Hotel = trim( preg_replace( '/[^;\sa-zA-Z0-9áéíóúüñÁÉÍÓÚÜÑ\n]\n+/u', ' ',  $crawler->filter('.hp__hotel-name')->text()));
             

                //Puntuacion HOtel
                 $puntuacion_Hotel =   trim( preg_replace( '/[^;\sa-zA-Z0-9áéíóúüñÁÉÍÓÚÜÑ]\n+/u', ' ',   $crawler->filter('#js--hp-gallery-scorecard')->text()));
                 $puntuacion2= explode('comentarios',$puntuacion_Hotel);
                  $puntuacion =preg_replace( '/\n/', ' ',$puntuacion2);
                
               
                  //Scrap de la imagenes del hotel
                $imagenes_hotel= $crawler->filter( '#photos_distinct')->children('a')->extract(array('href') ) ; 


                        // //Scrap de la direccion completa del hotel
                 $direccion_hotel = trim( preg_replace( '/[^;\sa-zA-Z0-9áéíóúüñÁÉÍÓÚÜÑ]\n+/u', ' ', $crawler->filter('.hp_address_subtitle')->text()));  
                


                        // //scrap de la descripcion completa.    
                 $descripcion_hotel0 = trim( preg_replace( '/[^;\sa-zA-Z0-9áéíóúüñÁÉÍÓÚÜÑ]\n+/u', ' ',$crawler->filter('#summary')->text()));
                   



                         //scrap de los servicios 
                    $servicios_hotel = preg_replace( '/\n/', ' ', $crawler->filter('.hp_desc_important_facilities')->filter('div')->text());
                    $myexplode = explode('Servicios más populares',$servicios_hotel);
                    $servicios2 =  $myexplode[1];
                    $servicios = $servicios2;
                 
                  





                    //Contador de la tabla descriptiva de booking
                    $nodescount2 = $crawler->filter( '#hp_availability_style_changes .description tbody')->count();
                    if($nodescount2 > 0){

               try{
                    
                     $crawler->filter('#hp_availability_style_changes .description table tbody ')
                     ->each( function ( $node ) use ($titulo_Hotel, $puntuacion, $direccion_hotel, $descripcion_hotel0,$servicios, $imagenes_hotel) {
                        if(!empty($node)){



                    $var1 =   $node->filter('tr')->filter('td')->filter('.hprt-roomtype-link')
                    ->each(function($noderooms) {


                         $listado_noderroms = preg_replace( '/\n/', ' ', $noderooms->text()); 
                         $listado_noderroms2 = preg_replace( '/\n/', ' ', $noderooms->parents()->filter('.hprt-occupancy-occupancy-info')->children()->count());
                         $listado_noderroms3 =  preg_replace( '/\n/', ' ',$noderooms->parents()->filter('.hprt-price-price')->first()->text());
                         $listado_noderroms4 = preg_replace( '/\n/', ' ', $noderooms->parents()->filter('.hprt-conditions')->first()->text());
                         $myoptions = explode('Cancelación', $listado_noderroms4);
                         $misopciones= $myoptions[0];
                         $listado_noderroms5 =  preg_replace( '/\n/', ' ', $noderooms->parents()->filter('.hprt-nos-select')->first()->text());



                          $listado_noderroms2a = preg_replace( '/\n/', ' ', $noderooms->parents()->filter('.hprt-occupancy-occupancy-info')->last()->children()->count());
                         $listado_noderroms3a =  preg_replace( '/\n/', ' ',$noderooms->parents()->filter('.hprt-price-price')->last()->first()->text());
                         $listado_noderroms4 = preg_replace( '/\n/', ' ', $noderooms->parents()->filter('.hprt-conditions')->last()->first()->text());
                         $myoptions = explode('Cancelación', $listado_noderroms4);
                         $misopcionesa= $myoptions[0];
                         $listado_noderroms5a =  preg_replace( '/\n/', ' ', $noderooms->parents()->filter('.hprt-nos-select')->last()->first()->text());

                 

                        
                       return  $listado_noderroms.', '.$listado_noderroms2.', '. $listado_noderroms3.', '. $misopciones.', '. $listado_noderroms5.', '.$listado_noderroms2a ;
                        

                    });



                   $var2=  $node->filter('tr')->filter('td')->filter('.hprt-occupancy-occupancy-info')
                    ->each(function($noderooms2){

                       $listado_noderroms2 = preg_replace( '/\n/', ' ', $noderooms2->children()->count());

                       return $listado_noderroms2;
                      

                   });


                   $var3 =   $node->filter('tr')->filter('td')->filter('.hprt-price-price')
                    ->each(function($noderooms3){

                       $listado_noderroms3 =  preg_replace( '/\n/', ' ',$noderooms3->first()->text());

                       return $listado_noderroms3;
                       
                   });



                  $var4 =     $node->filter('tr')->filter('td')->filter('.hprt-conditions')
                    ->each(function($noderooms4){

                       $listado_noderroms4 = preg_replace( '/\n/', ' ', $noderooms4->first()->text());

                       $myoptions = explode('Cancelación', $listado_noderroms4);

                       $misopciones= $myoptions[0];

                       return $misopciones;
                       

                   });


                     $var5 =  $node->filter('tr')->filter('td')->filter('.hprt-nos-select')
                    ->each(function($noderooms5){

                       $listado_noderroms5 =  preg_replace( '/\n/', ' ', $noderooms5->first()->text());

                    return $listado_noderroms5;
                      

                   });

                    



                      if(!in_array($titulo_Hotel, $this->reshotels)){
                       $this->reshotels[] = array(
                          'Nombre_hotel'    => $titulo_Hotel,
                         'puntuacion'      => $puntuacion[1],
                         'direccion'       => $direccion_hotel,
                         'descripcion'     => $descripcion_hotel0,
                         'servicios'       => $servicios,
                         'imagenes'        => $imagenes_hotel,
                         'Tipo_habitacion' => $var1 ,
                         'Ocupacion'       =>  $var2 ,
                         'precio'          =>  $var3 ,
                         'opciones'        =>   $var4 ,
                         'disponibilidad'  =>  $var5 
                                    );
                             }


                    
                               }//END IF CONTADOR del nodro

                           });//En crawler principal

                        } catch(\Exception $e){
                        return response()->json($e);
                    }            
  
                   }//En contador de la tabla
                   else{
                    return response()->json("No existen nodos");
                }

             $result = $this->reshotels;
         
              return response()->json(['data'=>$result], 200);

           }  catch(\Exception $e){
            return  $e;
        }

     }//End de la funcion

      public function autocomplete(Request $request)
     {


     $searchquery = $request->searchquery;
       $data = City::where('city','like','%'.$searchquery.'%')->get();


      echo json_encode($data);




    //   if($request->ajax())
    // {
    //  $output = '';
    //  $query = $request->get('query');
    //  if($query != '')
    //  {
    //   $data = DB::table('cities')->where('city', 'like', '%'.$query.'%')->take(6)->get();
       
    //  }     
    //  $total_row = $data->count();

    //  if($total_row > 0)
    //  {

    //   foreach($data as $row)
    //   {
    //    $output .= $row->city.'-'.$row->id.' ';
    //   }

    //  }
    //       $data = array(
    //   'result'  => $output
    //  );

    //  echo json_encode($data);
    // }

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
    //}



}