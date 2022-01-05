<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

class WeatherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $apikey = config('services.openweather.key');
        //Default or first load
        $locationarr = ["Tokyo","Yokohama","Kyoto","Osaka","Sapporo","Nagoya"];
        $arrresult = array();
        foreach($locationarr as $location){
    
            $response = Http::get("https://api.openweathermap.org/data/2.5/weather?q={$location}&appid={$apikey}&units=metric");
            $arrresult = $response->json();
    
            $defcityname[$location]     = $arrresult['name'];
            $defweatherdesc[$location]  = $arrresult['weather'][0]['description'];
            $defweathericon[$location]  = $arrresult['weather'][0]['icon'];
            $deftempcelsius[$location]  = $arrresult['main']['temp'];
            $defhumidity[$location]     = $arrresult['main']['humidity']." %";
            $defwindspeed[$location]    = $arrresult['wind']['speed']." km/h";
        }
 
        return view('weather',[
            'error'             => '',
            'from'              => '',
            'bgimg'             => 'landscape',
            'locarr'            => $locationarr,
            'weatherCityName'   => $defcityname,
            'weatherDesc'       => $defweatherdesc,
            'weatherTempCel'    => $deftempcelsius,
            'weatherIcon'       => $defweathericon,
            'weatherHumidity'   => $defhumidity,
            'weatherWindSpeed'  => $defwindspeed,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $date = date('Ymd');
        $apikey         = config('services.openweather.key');
        $clientid       = config('services.foursquare.client_id');
        $cliensecret    = config('services.foursquare.client_secret');

        $location = $request->city;
        $store = $request->store;
        //OpenWeather API
        $response = Http::get("https://api.openweathermap.org/data/2.5/weather?q={$location}&appid={$apikey}&units=metric");
        $responsehour = Http::get("https://api.openweathermap.org/data/2.5/forecast?q={$location}&appid={$apikey}&units=metric");
        $arrresult = $response->json();
        $arrresulthour = $responsehour->json();

        if($arrresult['cod']!=400 && $arrresult['cod']!=404){
            //Foursquare API
            $longlat = $arrresult['coord']['lat'].','. $arrresult['coord']['lon'];
            $url = 'https://api.foursquare.com/v2/venues/suggestCompletion?near=';
            $url .= urlencode($location).'&query='.urlencode($store);
            $url .= '&limit=10';
            $url .= '&ll='.urlencode($longlat);
            $url .= '&client_id='.$clientid;
            $url .= '&client_secret='.$cliensecret;
            $url .= '&v='.$date;
            $responsestore= Http::get($url);
            $arrresultstore = $responsestore->json();
           
            $error = $arrresult['cod'];
            return view('weather',[
                'error'             => $error,
                'from'              => 'post',
                'bgimg'             => $location,
                'locarr'            => $location,
                'postweather'       => $arrresult,
                'postweatherhour'   => $arrresulthour,
                'postfoursquare'    => $arrresultstore,
            ]);

        }else{
            $error = 400;
            return redirect()->action('App\Http\Controllers\WeatherController@index', ['error' => 400]);
        }


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
