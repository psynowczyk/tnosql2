var connection = new Mongo();
var db = connection.getDB('getglue');

var results = db.gg.aggregate(
	{ $group: {_id: "$title", total: {$sum: 1}} },
	{ $match: {"modelName": "movies" || "tv_shows"} },
	{ $sort: {total: -1} },
	{ $limit : 7}
);

printjson(results);