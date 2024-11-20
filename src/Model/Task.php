<?php

namespace App\Model;

class Task extends Collection {// implements DataModelInterface{
  function __construct(){
    parent::__construct("lists");
  }
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

  public function set(array $data):static{
    foreach ($data as $key => $value) {
      $this->data[$key]=$value;
    }
    return $this;
  }
  public function get(array $key=[]):array{
    return $key === [] ? $this->data : (isset($this->data[$key])?$this->data[$key]:[]);
  }
  //On peut ajouter un updateData() qui met à jour la BDD et un fillData() OU read() qui récupêre depuis la BDD

}