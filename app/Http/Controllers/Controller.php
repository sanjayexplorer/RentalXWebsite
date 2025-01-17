<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\UserMetas;
use App\Models\carsMeta;
use App\Models\bookingMeta;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    protected function getBaseUrlSystem()
    {
        return str_replace('index.php','',url('/'));
    }
    
    protected function insertOrUpdateUsermeta($userId,$userMetaKey,$userMetaValue)
    {
        if(strcmp($userMetaValue,'')!=0)
        {
            UserMetas::updateOrCreate(['userId'=>$userId,'meta_key'=>$userMetaKey],['partnerId'=>$userId,'meta_key'=>$userMetaKey,'meta_value'=>$userMetaValue]);
        }
        elseif(strcmp($userMetaValue,'')==0){
          $userMetaValue = '';
          UserMetas::updateOrCreate(['userId'=>$userId,'meta_key'=>$userMetaKey],['partnerId'=>$userId,'meta_key'=>$userMetaKey,'meta_value'=>$userMetaValue]);
        }
        
    }
    protected function insertOrUpdateCarsmeta($carId,$carMetaKey,$carMetaValue)
    {
        if(strcmp($carMetaValue,'')!=0)
        {
            carsMeta::updateOrCreate(['carId'=>$carId,'meta_key'=>$carMetaKey],['carId'=>$carId,'meta_key'=>$carMetaKey,'meta_value'=>$carMetaValue]);
        }
        elseif(strcmp($carMetaValue,'')==0){
          $carMetaValue = '';
          carsMeta::updateOrCreate(['carId'=>$carId,'meta_key'=>$carMetaKey],['carId'=>$carId,'meta_key'=>$carMetaKey,'meta_value'=>$carMetaValue]);
        }
        
    }
    protected function insertOrUpdateCarBookingMeta($carId,$carMetaKey,$carMetaValue)
    {
        if(strcmp($carMetaValue,'')!=0)
        {
            bookingMeta::updateOrCreate(['bookingId'=>$carId,'meta_key'=>$carMetaKey],['bookingId'=>$carId,'meta_key'=>$carMetaKey,'meta_value'=>$carMetaValue]);
        }
        elseif(strcmp($carMetaValue,'')==0){
          $carMetaValue = '';
          bookingMeta::updateOrCreate(['bookingId'=>$carId,'meta_key'=>$carMetaKey],['bookingId'=>$carId,'meta_key'=>$carMetaKey,'meta_value'=>$carMetaValue]);
        }
        
    }
}
