<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use App\TokenStore\TokenCache;

class CalendarController extends Controller
{
  public function calendar()
  {
    $viewData = $this->loadViewData();

    // Get the access token from the cache
    $tokenCache = new TokenCache();
    $accessToken = $tokenCache->getAccessToken();

    // Create a Graph client
    $graph = new Graph();
    $graph->setAccessToken($accessToken);

    $queryParams = array(
      '$select' => 'subject,organizer,start,end',
      '$orderby' => 'createdDateTime DESC'
    );

    // Append query parameters to the '/me/events' url
    $getEventsUrl = '/me/events?'.http_build_query($queryParams);

    $events = $graph->createRequest('GET', $getEventsUrl)
      ->setReturnType(Model\Event::class)
      ->execute();

    //return response()->json($events);
    $viewData['events'] = $events;
    return view('calendar', $viewData);
  }

  public function oneDrive($ruta_a_subir,$nombre_archivo)
  {
    $viewData = $this->loadViewData();

    // Get the access token from the cache
    $tokenCache = new TokenCache();
    $accessToken = $tokenCache->getAccessToken();

    // Create a Graph client
    $graph = new Graph();
    $graph->setAccessToken($accessToken);

    /*$queryParams = array(
      '$select' => 'subject,organizer,start,end',
      '$orderby' => 'createdDateTime DESC'
    );*/

    // Append query parameters to the '/me/events' url
    //$getEventsUrl = '/me/events?'.http_build_query($queryParams);

    $events = $graph->createRequest('PUT', '/me/drive/root/children/'.$nombre_archivo.'/content')
            ->upload($ruta_a_subir);

    if(empty(get_object_vars(response()->json($events)))){
        echo "no subido";

    }else{
        echo "subido correctamente";
    }
        //return response()->json($events);
        /*$viewData['one_drive'] = $events;
        return view('calendar', $viewData);*/
  }
}