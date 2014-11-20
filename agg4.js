var connection = new Mongo();
var db = connection.getDB('getglue');

var results = db.gg.aggregate(
	{ $match: {"modelName": "tv_shows"} },
	{ $match: {"action": "Liked"} },
	{ $group: {_id: "$title", total: {$sum: 1}} },
	{ $sort: {total: -1} },
	{ $limit: 7 }
);

printjson(results);