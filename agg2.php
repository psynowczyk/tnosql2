<?php

$connection = new MongoClient;
$collection = $connection -> selectDB("getglue") -> selectCollection("gg");

$out = $collection -> aggregate(
   array(
      '$match' => array('modelName' => array('$or' => array('modelName' => 'movies', 'modelName' => 'tv_shows')))
   ),
   array(
      '$group' => array(
         '_id' => array('dir' => '$director', 'id': '$title'),
         'total' => array('$sum' => 1)
      )
   ),
   array(
      '$group' => array(
         '_id' => '$_id.dir',
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