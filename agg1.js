var connection = new Mongo();
var db = connection.getDB('getglue');

var match1 = { $match: {$or: [{"modelName": "movies"}, {"modelName": "tv_shows"}]} };
var group = { $group: {"_id": "$title", "total": {$sum: 1}} };
var match2 = { $match: {"total": {$lt: 90000}} };
var sort = { $sort: {"total": -1} };
var limit = { $limit : 7};

var results = db.gg.aggregate(
	match1,
	group,
	match2,
	sort,
	limit
);

printjson(results);