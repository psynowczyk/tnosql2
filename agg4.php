<?php

$connection = new MongoClient;
$collection = $connection -> selectDB("getglue") -> selectCollection("gg");
MongoCursor::$timeout = -1;

$match = array('$match' => array('modelName' => 'tv_shows', 'action' => 'Liked'));
$group = array('$group' => array('_id' => '$title', 'total' => array('$sum' => 1)));
$sort = array('$sort' => array('total' => -1));
$limit = array('$limit' => 7);
$pipeline = array($match, $group, $sort, $limit);

$out = $collection -> aggregate($pipeline);

echo json_encode($out, JSON_PRETTY_PRINT);

?>