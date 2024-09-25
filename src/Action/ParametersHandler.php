<?php 

namespace App\Action;

class ParametersHandler{

  public function getParameters(object $bodyRequest, array $args):array{
    $parameters=[];
    if( json_decode($bodyRequest,true) !== null){
      $parameters = array_merge(json_decode($bodyRequest,true), $args);
    }else{
      $parameters = $args;
    }
    return $parameters;
  }
}
