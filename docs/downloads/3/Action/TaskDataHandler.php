<?php

namespace App\Action;

class TaskDataHandler {
  private function getTemplate(){
    return [
      "title"=> ["type" => "string", "mandatory" => true],
      "description"=> ["type" => "string", "mandatory" => false, "default"=>""],
      "date"=> ["type" => "int", "mandatory" => false, "default"=>time()],
      "done"=> ["type" => "bool", "mandatory" => false, "default"=>false]
    ];
  }
  public function verifyData(array $data, bool $verifyMandatory=false):bool{
    $ok=true;
    foreach ($this->getTemplate() as $key => $value) {
      // Vérification des données obligatoires
      if($verifyMandatory && $value["mandatory"] === true) $ok = $ok && isset($data[$key]);
      // Vérification des types des données
      if(isset($data[$key])){
        $ok = $ok && (gettype($data[$key]) == $value["type"]);
      }
    }
    return $ok;
  }
  public function getFormattedData(array $data, bool $fromTemplate=false):array{
    $formatedData=[];
    foreach ($this->getTemplate() as $key => $value) {
      // Vérification des données obligatoires
      if(isset($data[$key])){
        $formatedData[$key]=$data[$key];
      }else if($fromTemplate){
        $formatedData[$key]=$value["default"];
      }
    }
    return $formatedData;
  }

}
