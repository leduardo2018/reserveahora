<?php

namespace App\Http\Controllers;

use Goutte\Client;

use Illuminate\Http\Request;


class ScrapingController extends Controller
{
    //


    public function example(Client $client)
    {

    		$crawler = $client->request( 'GET','https://www.booking.com/hotel/co/calle-10-express.es.html?label=gen173nr-1FCAsoMkINc3VpdGVzLXJlY3Jlb0gKWARoMogBAZgBCsIBA3gxMcgBDNgBAegBAfgBA5ICAXmoAgM;sid=bc3e43896557080384f6fc1969225d5e;all_sr_blocks=176446704_109834274_0_0_0;bshb=2;checkin=2018-05-07;checkout=2018-05-24;dest_id=-592318;dest_type=city;dist=0;dotd_fb=1;group_adults=2;group_children=0;hapos=1;highlighted_blocks=176446704_109834274_0_0_0;hpos=1;no_rooms=1;room1=A%2CA;sb_price_type=total;srepoch=1525731828;srfid=60dcc3547c69412b7d90f061a11dff5d730297f7X1;srpvid=3b4e9d79a8fd02d9;type=total;ucfs=1&#hotelTmpl/');
    		dd($crawler);

   //  		$crawler = $client->request('GET', 'https://github.com/');
			// $crawler = $client->click($crawler->selectLink('Sign in')->link());
			// $form = $crawler->selectButton('Sign in')->form();
			// $crawler = $client->submit($form, array('login' => 'dev1@marketinghotelero.co', 'password' => 'leduardo2018.*'));
			// $crawler->filter('.flash-error')->each(function ($node) {
  	// 		 print $node->text()."\n";
			// });

			// dd($crawler);

    }


    public function vista(){

        return view('vista');
    }

}
