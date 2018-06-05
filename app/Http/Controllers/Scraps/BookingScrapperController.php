<?php

namespace App\Http\Controllers\Scraps;


//use App\Models\CityDestiny;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Helpers\HelpersController;
use Illuminate\Http\JsonResponse;
use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;
use Carbon\Carbon;
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
                 'age'               =>      ($var['child']['age1'] != null ? $var['child']['age1'] : 0),
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



                                // if(){
                                //     $precio = $node->filter( '.totalPrice' )->text();
                                //     $elprecio= explode('COP',$precio);
                                //     $price= $elprecio[1];

                                //   }
                                //    else{
                                //     $precio = $node->filter( '.Price' )->text();
                                //     $elprecio= explode('COP',$precio);
                                //     $price= $elprecio[1];

                                //  }

                                $cprice2 = $node->filter( '.price' )->count();
                                if($cprice2 != '0'){
                                    $signo_peso = '$';
                                    $precio2 = preg_replace( '/\n/', ' ', $node->filter( '.price' )->text());
                                    $elprecio2= explode('COP',$precio2);
                                    $price2 = $signo_peso.' '.$elprecio2[1];

                                }else{
                                    $price2 = "";
                                }


                                 $cprice = $node->filter( '.totalPrice' )->count();
                                if($cprice != '0'){
                                    $signo_peso = '$';
                                    $precio =preg_replace( '/\n/', ' ', $node->filter( '.totalPrice' )->text());
                                    $elprecio= explode('COP',$precio);
                                    $price = $signo_peso.' '.$elprecio[1];

                                }else{
                                    $price = "";
                                }

                                   $cprice3 = $node->filter( '.sr_gs_price_total' )->count();
                                if($cprice3 != '0'){
                                    $signo_peso3 = '$';
                                    $precio3 = preg_replace( '/\n/', ' ',$node->filter( '.sr_gs_price_total' )->text());
                                    $elprecio3= explode('COP',$precio3);
                                    $price3 = $signo_peso3.' '.$elprecio3[1];

                                }else{
                                    $price3 = "";
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



                                  $ckilometer = $node->filter( '.distfromdest' )->count();
                                if($ckilometer != '0'){
                                    $kilometers = preg_replace( '/\n/', ' ', $node->filter( '.distfromdest')->text());
                                }else{
                                    $kilometers = "";
                                }


                                   $crecomentation = $node->filter( '.room_link' )->count();
                                if($crecomentation != '0'){
                                  $recommendation = preg_replace( '/\n/', ' ',$node->filter( '.room_link')->text());
                                }
                                else{
                                    $recommendation = "";
                                }

                                  $cservices = $node->filter( '.sr_room_reinforcement' )->count();
                                if($cservices != '0'){
                                  $services = preg_replace( '/\n/', ' ',$node->filter( '.sr_room_reinforcement')->text());
                                }
                                else{
                                    $services = "";
                                }

                                // sr_room_reinforcement

                                // .sr_room_reinforcement
                                

                                if($name === "" && $price === ""){
                                    $name = "No disponible";                                
                                }else if($name != "" && $price === ""){
                                    
                                }else if($name === "" && $price != ""){
                                    $name = "No disponible";
                                }else if ($price2 ==="" && $price != "" ){
                                    
                                }


                                if(!in_array($name, $this->reshotels)){
                                    $this->reshotels[] = array(
                                        'id'    =>  $hotelid,
                                        'name'  =>  $name,
                                        'price' =>  $price,
                                        'price2' => $price2,
                                        'price3' => $price3,
                                        'link' =>   $link,
                                        'image' =>  $image,
                                        'score' =>  $score,
                                        'direccion' => $direccion,
                                        'kilometers' => $kilometers,
                                        'recommendation' => $recommendation,
                                        'services' => $services

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

     
                   // $url = 'https://www.booking.com/hotel/co/hostal-buena-onda.es.html?aid=304142;label=gen173nr-1DCAEoggJCAlhYSDNYBGgyiAEBmAEKuAEGyAEM2AED6AEBkgIBeagCAw;sid=ca03b3753db3fc2a0e630f398ad96d08;all_sr_blocks=186068211_100552968_2_0_0;bshb=2;checkin=2018-06-01;checkout=2018-06-02;dest_id=900054926;dest_type=city;dist=0;group_adults=2;hapos=1;highlighted_blocks=186068211_100552968_2_0_0;hpos=1;room1=A%2CA;sb_price_type=total;srepoch=1527526233;srfid=b2d08a285d1982f1c2694eb97c326769f31e7df2X1;srpvid=fb0d766c0a130221;type=total;ucfs=1&#hotelTmpl';

                $url = $var['url'];
                $id = $var['id'];


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


                 //Scrap del  id del hotel
                $hotel_id = $crawler->filter('.hp-lists')->attr('data-hotel-id');



                 //Scrap de el nombre del hotel
               $titulo_Hotel = trim( preg_replace( '/[^;\sa-zA-Z0-9áéíóúüñÁÉÍÓÚÜÑ\n]\n+/u', ' ',  $crawler->filter('.hp__hotel-name')->text()));
             

                //Puntuacion HOtel
                 $puntuacion_Hotel =   trim( preg_replace( '/[^;\sa-zA-Z0-9áéíóúüñÁÉÍÓÚÜÑ]\n+/u', ' ',   $crawler->filter('#js--hp-gallery-scorecard')->text()));
                 $puntuacion2= explode('comentarios',$puntuacion_Hotel);
                  $puntuacion =preg_replace( '/\n/', ' ',$puntuacion2);
                
               







              

                  //Scrap de la imagenes del hotel
                      $cimagenes_hotel= $crawler->filter('.bh-photo-grid-thumb-cell')->count();
                    if($cimagenes_hotel !=0){
                      $imagenes_hotel= $crawler->filter( '.bh-photo-grid-thumb-cell')
                     ->each(function($fotografia){
                        $nodofotografia = $fotografia->children('a')->extract(array('href')); 
                        return $nodofotografia;
                      });
                    }
                    else{
                     $imagenes_hotel= $crawler->filter( '#photos_distinct')->children('a')->extract(array('href') ) ;                                     
                      }
                      


                    //   $cimagenes_hotel2= $crawler->filter('.bh-photo-grid-thumbs')->children()->extract(array('href') ) ;
                    //   if($cimagenes_hotel2 !=0){
                    //       print_r('Imprime esta monda');
                    //      //$imagenes_hotel2= $crawler->filter( '.bh-photo-grid-thumbs')->children('a')->extract(array('href') ) ; 
                    //      }else{
                    //          $imagenes_hotel2="";
                    //      }
               // $imagenes_hotel= $crawler->filter( '')->children('a')->extract(array('href') ) ; 













                        // //Scrap de la direccion completa del hotel
                 $direccion_hotel = trim( preg_replace( '/[^;\sa-zA-Z0-9áéíóúüñÁÉÍÓÚÜÑ]\n+/u', ' ', $crawler->filter('.hp_address_subtitle')->text()));  
                


                        // //scrap de la descripcion completa.    
                $descripcion_hotel = trim( preg_replace( '/[^;\sa-zA-Z0-9áéíóúüñÁÉÍÓÚÜÑ]\n+/u', ' ',$crawler->filter('#summary')->text()));              
                 $descripcion_hotel0 = preg_replace( '/\n/', ' ',$descripcion_hotel);





                         //scrap de los servicios 
                   $servicios_hotel = $crawler->filter('.hp_desc_important_facilities')->filter('div')->children()
                  ->each(function($servicesitems){
                   return  $listado_de_servicios = preg_replace( '/\n/', ' ', $servicesitems->text());       
                            });
                
                 
           

                      //scrap de las estrellas del hotel
                    $estrellas = $crawler->filter('.bk-icon-stars')->attr('title');





                        // comentarios
                $comentarios_hotel = $crawler->filter('.hp-social-proof-review_score')->filter('div')->filter('.hp-social_proof-quote_bubble')
                     ->each(function($social){
                  //    $autor_comentario = preg_replace( '/\n/', ' ', $social
                  // // ->filter('.hp-social_proof-quote_author-details')
                  //    ->text());
                     $listado_comentarios = preg_replace( '/\n/', ' ', $social
                            ->last()
                     ->text());    
                    return $listado_comentarios
                    // .'-'.$autor_comentario
                    ;  
                 //  
                });



                     //autor de comentarios
                 $comentarios_autor = $crawler->filter('.hp-social-proof-review_score')->filter('div')->filter('.hp-social_proof-quote_author-details')
                     ->each(function($social){
        
                     $listado_autores = preg_replace( '/\n/', ' ', $social
                            ->last()
                     ->text());    
                    return $listado_autores
                    // .'-'.$autor_comentario
                    ;  
                 //  
                });
                           



                    //Contador de la tabla descriptiva de booking
                    $nodescount2 = $crawler->filter( '#hp_availability_style_changes .description tbody')->count();
                    if($nodescount2 > 0){

               try{
                    
                     $crawler->filter('#hp_availability_style_changes .description table tbody ')
                     ->each( function ( $node ) use ($titulo_Hotel, $puntuacion, $direccion_hotel, $descripcion_hotel0,$servicios_hotel,$imagenes_hotel
               //,$imagenes_hotel2
                     ,$hotel_id,$estrellas
                        ,$comentarios_hotel
                        ,$comentarios_autor
                     ) {
                        if(!empty($node)){



                    $tipo_de_habitacion =   $node->filter('tr')->filter('td')->filter('.hprt-roomtype-link')
                    ->each(function($noderooms) {
                         $listado_noderroms = preg_replace( '/\n/', ' ', $noderooms->text()); 
                             $myoptions = explode('Ver', $listado_noderroms);
                             $misopciones= $myoptions[0];
                      return  $misopciones;                                             
                    });

                

                     $servicios_por_tipo_habitacion=  $node->filter('tr')->filter('.hprt-facilities-block')
                    ->each(function($noderooms7){
                       // $listado_noderroms7 = preg_replace( '/\n/', ' ', $noderooms7->text());
                       // return $listado_noderroms7.',';      
                       return $listado_noderroms7 = $noderooms7->filter('span')->each(function($items){

                             $items_noderooms7 = preg_replace( '/\n/', ' ', $items->text());

                             return $items_noderooms7;

                        });
                   });

                 




                 
                    //Scrap del precio por tipo de habitacion
                    //se agrgrefa un mensaje al costado de cada resultado, para definir cual es el ultimo nodo que pertenece a cada tipo de habitacion
                   $precio_de_tipo_habitacion =   $node->filter('tr')->filter('td')->filter('.hprt-price-price')
                    ->each(function($noderooms3){
                       $listado_noderroms3 =  preg_replace( '/\n/', ' ',$noderooms3->text());
                       $last_row =  preg_replace( '/\n/', ' ', $noderooms3->parents()->parents()->filter('tr')->filter('.hprt-table-last-row')->filter('td')->filter('.hprt-price-price')->text());
                         $vacio = 'this is the last Node';
                         $vacio2 =' ';
                      if($listado_noderroms3 === $last_row){
                    return $listado_noderroms3.'-'.$vacio;
                          }else{
                     return $listado_noderroms3.' '.$vacio2;
                                 }                      
                   });

                        //scrap de ocupacion por tipos de habitacion
                      $ocupacion_de_tipo_habitacion=  $node->filter('tr')->filter('.hprt-occupancy-occupancy-info')
                    ->each(function($noderooms2){
                       $listado_noderroms2 = preg_replace( '/\n/', ' ', $noderooms2->filter('i')->count());
                       $multiplicador =  preg_replace( '/\n/', ' ', $noderooms2->text());
                       return ($listado_noderroms2.' '.$multiplicador);
                      

                   });



                    //srap de las opciones por tipo de habitacion
                  $condiciones_de_tipo_habitacion =     $node->filter('tr')->filter('td')->filter('.hprt-conditions')
                    ->each(function($noderooms4){
                       $listado_noderroms4 = preg_replace( '/\n/', ' ', $noderooms4->text());
                       $myoptions = explode('Cancelación', $listado_noderroms4);
                       $misopciones= $myoptions[0];
                       return $listado_noderroms4;                    
                   });



                    //scrap de la disponibilidad por el tipo de habitacion, Recordemos que se agrega un mensaje a los ultimos nodos que pertenecen a cada tipo de habitacion
                     $disponibilidad_de_tipo_habitacion =  $node->filter('tr')->filter('td')->filter('.hprt-nos-select')
                    ->each(function($noderooms5){
                       $listado_noderroms5 =  preg_replace( '/\n/', ' ', $noderooms5->text());


                     $last_row =  preg_replace( '/\n/', ' ', $noderooms5->parents()->parents()->filter('tr')->filter('.hprt-table-last-row')->filter('td')->filter('.hprt-nos-select')->text());
                       $vacio = 'this is the last Node';
                       $vacio2 =' ';
                         if($listado_noderroms5 === $last_row){
                    return $listado_noderroms5.'-'.$vacio;
                          }else{
                     return $listado_noderroms5.' '.$vacio2;
                                 }
                         });


                   




                    



                      if(!in_array($titulo_Hotel, $this->reshotels)){
                       $this->reshotels[] = array(
                         'hotel_id'        => $hotel_id,
                         'Nombre_hotel'    => $titulo_Hotel,
                          'estrellas'       => $estrellas,
                         'puntuacion'      => $puntuacion[1],
                         'direccion'       => $direccion_hotel,
                         'descripcion'     => $descripcion_hotel0,
                         'servicios'       => $servicios_hotel,
                         'imagenes'        => $imagenes_hotel,
                         //'imagenes2'        => $imagenes_hotel2,
                         'Tipo_habitacion' =>  $tipo_de_habitacion,
                         'servicios_por_tipo_habitacion' => $servicios_por_tipo_habitacion,
                         'precio'          =>  $precio_de_tipo_habitacion ,
                         'Ocupacion'       =>  $ocupacion_de_tipo_habitacion ,
                         'opciones'        =>  $condiciones_de_tipo_habitacion ,
                         'disponibilidad'  =>  $disponibilidad_de_tipo_habitacion,
                         'comentarios'     =>  $comentarios_hotel,
                         'autor'           =>  $comentarios_autor
                        
                         
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

                // DB::table('hotel_details')->insert([

                // "hotel_id"                       => $result['0']['hotel_id'],
                // "nombre_hotel"                   => $result['0']['Nombre_hotel'],
                // "puntuacion"                     => $result['0']['puntuacion'],
                // "direccion"                      => $result['0']['direccion'],
                // "descripcion"                    => $result['0']['descripcion'],
                // "servicios"                      => $result['0']['servicios'],
                // "imagenes"                       => $result['0']['puntuacion'],
                // "tipo_habitacion"                => $result['0']['puntuacion'],
                // "servicios_por_tipo_habitacion"  => $result['0']['puntuacion'],
                // "precio"                         => $result['0']['puntuacion'],
                // "ocupacion"                      => $result['0']['puntuacion'],
                // "opciones"                       => $result['0']['puntuacion'],
                // "disponibilidad"                 => $result['0']['puntuacion'],
                // "created_at"                     => Carbon::now(),
                // "updated_at"                     => Carbon::now(),
     

                // ]);
               
                
               return response()->json(['data'=>$result], 200);



           }  catch(\Exception $e){
            return  $e;
        }

     }//End de la funcion



     //funcion para recuperar la información de las ciudades 
      public function autocomplete(Request $request)
     {
        $data = [];
       if($request->has('q')){
           $search = $request->q;
           $data = DB::table("cities")
                   ->select("id","city")
                   ->where('city','LIKE',"%$search%")
                   ->get();
       }
       return response()->json($data); 
   }




   public function allcity(){

           $data = [];

           $data = DB::table("cities")->get(); 

           return response()->json($data);
   }


   public function reservashotelerasendpoints(){




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