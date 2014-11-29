var connection = new Mongo();
var db = connection.getDB('getglue');

var match = {
	$match: {
		$or: [{"modelName": "movies"}, {"modelName": "tv_shows"}],
		$and: [
			{"director": {$ne: "not available"}},
			{"director": {$ne: "various directors"}},
			{"director": {$ne: null}}
		]
	}
};
var group1 = { $group: {"_id": {"director": "$director", "title": "$title"}, "total": {$sum: 1}} };
var group2 = { $group: {"_id": "$_id.director", "total": {$sum: 1}} };
var sort = { $sort: {total: -1} };
var limit = { $limit : 7};

var results = db.gg.aggregate(
	match,
	group1,
	group2,
	sort,
	limit
);

printjson(results);