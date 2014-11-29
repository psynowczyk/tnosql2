#Spis treści
- [Wstęp](#wstęp)
- [Agregacja 1](#agregacja-1)
- [Agregacja 2](#agregacja-2)
- [Agregacja 3](#agregacja-3)
- [Agregacja 4](#agregacja-4)

#Wstęp

Plik z danymi: [GetGlue and Timestamped Event Data](http://getglue-data.s3.amazonaws.com/getglue_sample.tar.gz)<br>
Wykresy wygenerowane za pomocą [Highcharts](http://www.highcharts.com)

Import danych do bazy:
```sh
$ time mongoimport -d getglue -c gg --type json --file getglue.json

real    8m 11.997s
user    5m 50.901s
sys     0m 59.588s
```

#Agregacja 1
7 najpopularniejszych filmów i seriali poniżej 90000 wystąpień<br>
[JS](https://github.com/psynowczyk/tnosql2/blob/master/agg1.js)
```js
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
```
[PHP](https://github.com/psynowczyk/tnosql2/blob/master/agg1.php)
```php
$match1 = array('$match' => array('$or' => array(array('modelName' => 'movies'), array('modelName' => 'tv_shows'))));
$group = array('$group' => array('_id' => '$title', 'total' => array('$sum' => 1)));
$match2 = array('$match' => array('total' => array('$lt' => 90000)));
$sort = array('$sort' => array('total' => -1));
$limit = array('$limit' => 7);
$pipeline = array($match1, $group, $match2, $sort, $limit);

$out = $collection -> aggregate($pipeline);
```
Wynik
```js
{
	"result" : [
		{
			"_id" : "The Twilight Saga: Breaking Dawn Part 1",
			"total" : 87521
		},
		{
			"_id" : "House",
			"total" : 85196
		},
		{
			"_id" : "Pretty Little Liars",
			"total" : 82789
		},
		{
			"_id" : "Family Guy",
			"total" : 82322
		},
		{
			"_id" : "How I Met Your Mother",
			"total" : 80002
		},
		{
			"_id" : "Grey's Anatomy",
			"total" : 79585
		},
		{
			"_id" : "The Hunger Games",
			"total" : 79340
		}
	],
	"ok" : 1
}
```
| Tytuł                                         | Popularność |
|-----------------------------------------------|-------------|
| The Twilight Saga: Breaking Dawn Part 1       | 87521       |
| House                                         | 85196       |
| Pretty Little Liars                           | 82789       |
| Family Guy                                    | 82322       |
| How I Met Your Mother                         | 80002       |
| Grey's Anatomy                                | 79585       |
| The Hunger Games                              | 79340       |
![alt text](https://github.com/psynowczyk/tnosql2/blob/master/img1.png "")

#Agregacja 2
7 reżyserów z największą ilością filmów lub seriali<br>
[JS](https://github.com/psynowczyk/tnosql2/blob/master/agg2.js)
```js
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
```
[PHP](https://github.com/psynowczyk/tnosql2/blob/master/agg2.php)
```php
$match = array(
   '$match' => array(
      '$or' => array(
         array('modelName' => 'movies'),
         array('modelName' => 'tv_shows')
      ),
      '$and' => array(
         array('director' => array('$ne' => 'not available')),
         array('director' => array('$ne' => 'various directors')),
         array('director' => array('$ne' => null))
      )
   )
);
$group1 = array(
   '$group' => array(
      '_id' => array('director' => '$director', 'title' => '$title'),
      'total' => array('$sum' => 1)
   )
);
$group2 = array(
   '$group' => array(
      '_id' => '$_id.director',
      'total' => array('$sum' => 1)
   )
);
$sort = array('$sort' => array('total' => -1));
$limit = array('$limit' => 7);
$pipeline = array($match, $group1, $group2, $sort, $limit);

$out = $collection -> aggregate($pipeline);
```
Wynik
```js
{
	"result" : [
		{
			"_id" : "alfred hitchcock",
			"total" : 136
		},
		{
			"_id" : "garry marshall",
			"total" : 108
		},
		{
			"_id" : "woody allen",
			"total" : 105
		},
		{
			"_id" : "chuck jones",
			"total" : 98
		},
		{
			"_id" : "errol morris",
			"total" : 94
		},
		{
			"_id" : "peter west",
			"total" : 93
		},
		{
			"_id" : "sydney pollack",
			"total" : 86
		}
	],
	"ok" : 1
}
```
| Reżyser                                       | Ilość produkcji |
|-----------------------------------------------|-----------------|
| alfred hitchcock                              | 136             |
| garry marshall                                | 108             |
| woody allen                                   | 105             |
| chuck jones                                   | 98              |
| errol morris                                  | 94              |
| peter west                                    | 93              |
| sydney pollack                                | 86              |
![alt text](https://github.com/psynowczyk/tnosql2/blob/master/img2.png "")

#Agregacja 3
7 użytkowników z ilością komentarzy powyżej 5000<br>
[JS](https://github.com/psynowczyk/tnosql2/blob/master/agg3.js)
```js
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
```
[PHP](https://github.com/psynowczyk/tnosql2/blob/master/agg3.php)
```php
$out = $collection -> aggregate(
   array(
      '$group' => array(
         '_id' => '$userId',
         'total' => array('$sum' => 1)
      )
   ),
   array(
      '$match' => array('total' => array('$gte' => 50000))
   ),
   array(
   	'sort' => array('$total' => -1)
   ),
   array(
   	'limit' => 7
   )
);
```
Wynik
```js
{
	"result" : [
		{
			"_id" : "agentdunham",
			"total" : 18119
		},
		{
			"_id" : "zbj",
			"total" : 11547
		},
		{
			"_id" : "tedi31",
			"total" : 9326
		},
		{
			"_id" : "endika",
			"total" : 9103
		},
		{
			"_id" : "MISSY1",
			"total" : 8874
		},
		{
			"_id" : "alison_peters",
			"total" : 8194
		},
		{
			"_id" : "darylrosemd",
			"total" : 7501
		}
	],
	"ok" : 1
}
```
| Użytkownik                                    | Komentarze  |
|-----------------------------------------------|-------------|
| agentdunham                                   | 18119       |
| zbj                                           | 11547       |
| tedi31                                        | 9326        |
| endika                                        | 9103        |
| MISSY1                                        | 8874        |
| alison_peters                                 | 8194        |
| darylrosemd                                   | 7501        |
![alt text](https://github.com/psynowczyk/tnosql2/blob/master/img3.png "")

#Agregacja 4
7 seriali z największą ilością polubień<br>
[JS](https://github.com/psynowczyk/tnosql2/blob/master/agg4.js)
```js
var match = { $match: {"modelName": "tv_shows", "action": "Liked"} };
var group = { $group: {_id: "$title", total: {$sum: 1}} };
var sort = { $sort: {total: -1} };
var limit = { $limit: 7 };

var results = db.gg.aggregate(
	match,
	group,
	sort,
	limit
);
```
[PHP](https://github.com/psynowczyk/tnosql2/blob/master/agg4.php)
```php
$out = $collection -> aggregate(
   array(
      '$match' => array('modelName' => 'tv_shows')
   ),
    array(
      '$match' => array('action' => 'Liked')
   ),
   array(
      '$group' => array(
         '_id' => '$title',
         'total' => array('$sum' => 1)
      )
   ),
   array(
   	'sort' => array('$total' => -1)
   ),
   array(
   	'limit' => 7
   )
);
```
Wynik
```js
{
	"result" : [
		{
			"_id" : "The Big Bang Theory",
			"total" : 29757
		},
		{
			"_id" : "The Simpsons",
			"total" : 28297
		},
		{
			"_id" : "Family Guy",
			"total" : 28120
		},
		{
			"_id" : "House",
			"total" : 25718
		},
		{
			"_id" : "Glee",
			"total" : 22490
		},
		{
			"_id" : "How I Met Your Mother",
			"total" : 20854
		},
		{
			"_id" : "The Walking Dead",
			"total" : 20601
		}
	],
	"ok" : 1
}
```
| Serial                                        | Polubienia  |
|-----------------------------------------------|-------------|
| The Big Bang Theory                           | 29757       |
| The Simpsons                                  | 28297       |
| Family Guy                                    | 28120       |
| House                                         | 25718       |
| Glee                                          | 22490       |
| How I Met Your Mother                         | 20854       |
| The Walking Dead                              | 20601       |
![alt text](https://github.com/psynowczyk/tnosql2/blob/master/img4.png "")