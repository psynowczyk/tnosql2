var connection = new Mongo();
var db = connection.getDB('getglue');

var match = { $match: {"modelName": "movies" || "tv_shows"} };
var group = { $group: {"_id": "$title", "total": {$sum: 1}} };
var sort = { $sort: {"total": -1} };
var limit = { $limit : 7};

var results = db.gg.aggregate(
	match,
	group,
	sort,
	limit
);

printjson(results);