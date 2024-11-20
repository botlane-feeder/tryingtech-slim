<?php

namespace App\Model;

interface DataModelInterface{
  public function create():static;
  public function findMany():array;
  public function findOne(array $filter):static;
  public function findByID(string $id):static;

  public function get(array $data=[]):array;
  public function set(array $data):static;
  public function unset(array $data):static;
  public function delete():null;
  
  public function save():static;
}