<?php

namespace App\Http\Controllers;

use Goutte\Client;

use Illuminate\Http\Request;


class ScrapingController extends Controller
{
    //


    public function example(Client $client)
    {

    		$crawler = $client->request( 'GET','https://www.booking.com');

    		dd($crawler);

    }




}
