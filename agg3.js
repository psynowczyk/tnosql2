var connection = new Mongo();
var db = connection.getDB('getglue');

var match1 = { $match: {"comment": {$ne: ""}} };
var group = { $group: {"_id": "$userId", "total": {$sum: 1}} };
var match2 = { $match: {"total": {$gte: 5000}} };
var sort = { $sort: {total: -1} };
var limit = { $limit: 7 };

var results = db.gg.aggregate(
	match1,
	group,
	match2,
	sort,
	limit
);

printjson(results);