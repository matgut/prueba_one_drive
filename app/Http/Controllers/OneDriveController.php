<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use App\TokenStore\TokenCache;
use Storage;

class OneDriveController extends Controller
{
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

        $events = $graph->createRequest('PUT', '/me/drive/root/children/'.$nombre_archivo.'/content')
                ->upload($ruta_a_subir);

        return response()->json($events);
            /*$viewData['one_drive'] = $events;
            return view('calendar', $viewData);*/
    }

    public function deleteFile($itemFile)
    {
        $viewData = $this->loadViewData();

        // Get the access token from the cache
        $tokenCache = new TokenCache();
        $accessToken = $tokenCache->getAccessToken();

        // Create a Graph client
        $graph = new Graph();
        $graph->setAccessToken($accessToken);

        $arr = explode(".",$itemFile);

        $events = $graph->createRequest("GET", "/me/drive/root/children?\$filter=startswith(name, '".$arr[0]."')")
    						          ->setReturnType(Model\DriveItem::class)
    						          ->execute();
    	$itemToShare = $events[0];
    	$itemId = $itemToShare->getId();
    	$delete = $graph->createRequest("DELETE", "/me/drive/items/" . $rareSearchItem->getId())
                            ->execute();
    	
        return response()->json($delete);

    }

    public function getSharingLink($itemFile)
    {
        $viewData = $this->loadViewData();

        // Get the access token from the cache
        $tokenCache = new TokenCache();
        $accessToken = $tokenCache->getAccessToken();

        // Create a Graph client
        $graph = new Graph();
        $graph->setAccessToken($accessToken);

        $arr = explode(".",$itemFile);

        $events = $graph->createRequest("GET", "/me/drive/root/children?\$filter=startswith(name, '".$arr[0]."')")
    						          ->setReturnType(Model\DriveItem::class)
    						          ->execute();
    	$itemToShare = $events[0];
    	$itemId = $itemToShare->getId();
    	$permission = $graph->createRequest("POST", "/me/drive/items/$itemId/createLink")
    						        ->attachBody(array("type" => "edit", "scope" => "anonymous"))
    						        ->setReturnType(Model\Permission::class)
    						        ->execute();
    	$link = $permission->getLink();
        return $link->getWebUrl();
    }

    public function getDownloadLink($itemFile)
    {
        $viewData = $this->loadViewData();

        // Get the access token from the cache
        $tokenCache = new TokenCache();
        $accessToken = $tokenCache->getAccessToken();

        // Create a Graph client
        $graph = new Graph();
        $graph->setAccessToken($accessToken);

        $arr = explode(".",$itemFile);

        $events = $graph->createRequest("GET", "/me/drive/root/children?\$filter=startswith(name, '".$arr[0]."')")
    						          ->setReturnType(Model\DriveItem::class)
    						          ->execute();
    	$itemToShare = $events[0];
    	$itemId = $itemToShare->getId();
    	$permission = $graph->createRequest("POST", "/me/drive/items/$itemId/createLink")
    						        ->attachBody(array("type" => "embed"))
    						        ->setReturnType(Model\Permission::class)
    						        ->execute();
        $link = $permission->getLink();
        $finalLink = str_replace("embed","download",$link->getWebUrl());
        return $finalLink;
        
    }
    

    public function uploadFilePost(Request $request){
        $request->validate([
            'fileToUpload' => 'required|file|max:1024',
        ]);
    
        $fileName = request()->fileToUpload->getClientOriginalName();
        $storagePath  = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix();

        if ($request->fileToUpload->storeAs('arc_subidos_temp',$fileName)) {
           if($this->oneDrive($storagePath."\arc_subidos_temp\\".$fileName,$fileName)){
               $link = $this->getSharingLink($fileName);
               $linkDonwload = $this->getDownloadLink($fileName);
               if ($link) {
                    return back()->with('success','Se ha subido correctamente su archivo a oneDrive, su links son: ')->with(compact('link','linkDonwload'));
               } else {
                 unlink($storagePath."\arc_subidos_temp\\".$fileName) or die("no se pudo borar archivo");
                 if($this->deleteFile($fileName)){
                    return back()->with('success','archivo borrado correctamente');
                 }else{
                    return back()->with('error','Error, no se ha borrado archivo desde oneDrive, favor avisar a su administrador');
                 }
               }
           }else{
               unlink($storagePath."\arc_subidos_temp\\".$fileName) or die("no se pudo borar archivo");
               return back()->with('error','Error al cargar archivo a onDrive');
           }
        } else {
            return back()->with('error','Error al cargar archivo al servidor');
        }
        
    
    }


    
}
