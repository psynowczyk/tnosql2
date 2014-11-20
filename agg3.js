var connection = new Mongo();
var db = connection.getDB('getglue');

var results = db.gg.aggregate(
	{ $group: {_id: "$userId", total: {$sum: 1}} },
	{ $match: {total: {$gte: 50000}} },
	{ $sort: {total: -1} },
	{ $limit: 7 }
);

printjson(results);