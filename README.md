#Spis treści
- [Wstęp](#wstęp)
- [Agregacja 1](#agregacja-1)
- [Agregacja 2](#agregacja-2)
- [Agregacja 3](#agregacja-3)
- [Agregacja 4](#agregacja-4)

#Wstęp

Plik z danymi: [GetGlue and Timestamped Event Data](http://getglue-data.s3.amazonaws.com/getglue_sample.tar.gz)

Import danych do bazy:
```sh
$ time mongoimport -d getglue -c gg --type json --file getglue.json

real    8m 11.997s
user    5m 50.901s
sys     0m 59.588s
```

#Agregacja 1
7 najpopularniejszych filmów i seriali<br>
[JS](https://github.com/psynowczyk/tnosql2/blob/master/agg1.js)
```js
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
```
[PHP](https://github.com/psynowczyk/tnosql2/blob/master/agg1.php)
```php
$out = $collection -> aggregate(
   array(
      '$group' => array(
         '_id' => '$title',
         'total' => array('$sum' => 1)
      )
   ),
   array(
      '$match' => array('modelName' => array('$or' => array('modelName' => 'movies', 'modelName' => 'tv_shows')))
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
			"_id" : "The Twilight Saga: Breaking Dawn Part 1",
			"total" : 87521
		},
		{
			"_id" : "The Hunger Games",
			"total" : 79340
		},
		{
			"_id" : "Marvel's The Avengers",
			"total" : 64356
		},
		{
			"_id" : "Harry Potter and the Deathly Hallows: Part II",
			"total" : 33680
		},
		{
			"_id" : "The Muppets",
			"total" : 29002
		},
		{
			"_id" : "Captain America: The First Avenger",
			"total" : 28406
		},
		{
			"_id" : "Avatar",
			"total" : 23238
		}
	],
	"ok" : 1
}
```
| Tytuł                                         | Popularność |
|-----------------------------------------------|-------------|
| The Twilight Saga: Breaking Dawn Part 1       | 87521       |
| The Hunger Games                              | 79340       |
| Marvel's The Avengers                         | 64356       |
| Harry Potter and the Deathly Hallows: Part II | 33680       |
| The Muppets                                   | 29002       |
| Captain America: The First Avenger            | 28406       |
| Avatar                                        | 23238       |
![alt text](https://github.com/psynowczyk/tnosql2/blob/master/img1.png "")

#Agregacja 2
7 reżyserów z największą ilością filmów lub seriali<br>
[JS](https://github.com/psynowczyk/tnosql2/blob/master/agg2.js)
```js
var match = { $match: {"modelName": "movies" || "tv_shows", $and: [{"director": {$ne: "not available"}}, {"director": {$ne: "various directors"}}]} };
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
$out = $collection -> aggregate(
   array(
      '$match' => array('modelName' => array('$or' => array('modelName' => 'movies', 'modelName' => 'tv_shows')))
   ),
   array(
      '$group' => array(
         '_id' => array('dir' => '$director', 'id': '$title'),
         'total' => array('$sum' => 1)
      )
   ),
   array(
      '$group' => array(
         '_id' => '$_id.dir',
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
			"_id" : "alfred hitchcock",
			"total" : 50
		},
		{
			"_id" : "michael curtiz",
			"total" : 48
		},
		{
			"_id" : "woody allen",
			"total" : 47
		},
		{
			"_id" : "jesus franco",
			"total" : 43
		},
		{
			"_id" : "takashi miike",
			"total" : 43
		},
		{
			"_id" : "ingmar bergman",
			"total" : 42
		},
		{
			"_id" : "john ford",
			"total" : 42
		}
	],
	"ok" : 1
}
```
| Reżyser                                       | Ilość dzieł |
|-----------------------------------------------|-------------|
| alfred hitchcock                              | 50          |
| michael curtiz                                | 48          |
| woody allen                                   | 47          |
| jesus franco                                  | 43          |
| takashi miike                                 | 43          |
| ingmar bergman                                | 42          |
| john ford                                     | 42          |
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