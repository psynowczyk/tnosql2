<?php

$connection = new MongoClient;
$collection = $connection -> selectDB("getglue") -> selectCollection("gg");

$out = $collection -> aggregate(
   array(
      '$group' => array(
         '_id' => '$userId',
         'total' => array('$sum' => 1)
      )
   ),
   array(
      '$match' => array('total' => array('$gte' => 50000))
   ),
   array(
   	'sort' => array('$total' => -1)
   ),
   array(
   	'limit' => 7
   )
);

echo json_encode($out);

?>