var connection = new Mongo();
var db = connection.getDB('getglue');

var results = db.gg.aggregate(
	{ $match: {"modelName": "movies" || "tv_shows"  } },
   { $group: {_id: {"dir": "$director", id: "$title"}, total: {$sum: 1}} },
   { $group: {_id: "$_id.dir" , total: {$sum: 1}} },
   { $sort: {total: -1} },
   { $limit : 7}
);

printjson(results);