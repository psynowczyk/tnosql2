<?php

   $connection = new MongoClient;
   $collection = $connection -> selectDB("getglue") -> selectCollection("gg");
   MongoCursor::$timeout = -1;

   $match1 = array('$match' => array('$or' => array(array('modelName' => 'movies'), array('modelName' => 'tv_shows'))));
   $group = array('$group' => array('_id' => '$title', 'total' => array('$sum' => 1)));
   $match2 = array('$match' => array('total' => array('$lt' => 90000)));
   $sort = array('$sort' => array('total' => -1));
   $limit = array('$limit' => 7);
   $pipeline = array($match1, $group, $match2, $sort, $limit);

   $out = $collection -> aggregate($pipeline);

   echo json_encode($out, JSON_PRETTY_PRINT);

?>