<?php

namespace App\Action;

class Calculation {

  static public function getYFromSteps(array $thresholds, array $values, int|float $myX):int|float{
    $myY = 0;
    if( $myX < $thresholds[0]){
      $myY = $value[0];
    }elseif( $myX > $thresholds[\count($thresholds)-1]){
      $myY = $values[\count($values)-1];
    }else{
      for ($i=1; $i < \count($thresholds); $i++) { 
        if( $myX < $thresholds[$i]){
          $a = ($values[$i] - $values[$i-1]) / ($thresholds[$i] - $thresholds[$i-1]);
          $b = $values[$i] - $a * $thresholds[$i];
          $myY = $a * $myX + $b ;
        }
      }
    }
    return $myY ;
  }


  // static public function getYFromSteps(array $thresholds, array $values, int|float $myX):int|float{
  //   $myY = 0;
  //   if( $myX < $thresholds[0]){
  //     $myY = $values[0];
  //   }elseif( $myX > $thresholds[\count($thresholds)-1]){
  //     $myY = $values[\count($values)-1];
  //   }else{
  //     for ($i=1; $i < \count($thresholds); $i++) { 
  //       if( $myX < $thresholds[$i]){
  //         $a = ($values[$i] - $values[$i-1]) / ($thresholds[$i] - $thresholds[$i-1]);
  //         $b = $values[$i] - $a * $thresholds[$i];
  //         $myY = $a * $myX + $b ;
  //       }
  //     }
  //   }
  //   return intval($myY*100)/100 ;
  // }

}