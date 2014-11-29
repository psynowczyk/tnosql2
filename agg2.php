<?php

$connection = new MongoClient;
$collection = $connection -> selectDB("getglue") -> selectCollection("gg");
MongoCursor::$timeout = -1;

$match = array(
   '$match' => array(
      '$or' => array(
         array('modelName' => 'movies'),
         array('modelName' => 'tv_shows')
      ),
      '$and' => array(
         array('director' => array('$ne' => 'not available')),
         array('director' => array('$ne' => 'various directors')),
         array('director' => array('$ne' => null))
      )
   )
);
$group1 = array(
   '$group' => array(
      '_id' => array('director' => '$director', 'title' => '$title'),
      'total' => array('$sum' => 1)
   )
);
$group2 = array(
   '$group' => array(
      '_id' => '$_id.director',
      'total' => array('$sum' => 1)
   )
);
$sort = array('$sort' => array('total' => -1));
$limit = array('$limit' => 7);
$pipeline = array($match, $group1, $group2, $sort, $limit);

$out = $collection -> aggregate($pipeline);

echo json_encode($out, JSON_PRETTY_PRINT);

?>