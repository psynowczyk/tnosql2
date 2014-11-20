<?php

$connection = new MongoClient;
$collection = $connection -> selectDB("getglue") -> selectCollection("gg");

$out = $collection -> aggregate(
   array(
      '$match' => array('modelName' => 'tv_shows')
   ),
    array(
      '$match' => array('action' => 'Liked')
   ),
   array(
      '$group' => array(
         '_id' => '$title',
         'total' => array('$sum' => 1)
      )
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