#Spis treści
- [Wstęp](#wstęp)
- [Agregacja 1](#agregacja-1)
- [Agregacja 2](#agregacja-2)
- [Agregacja 3](#agregacja-3)
- [Agregacja 4](#agregacja-4)

#Wstęp

Plik z danymi: [GetGlue and Timestamped Event Data](http://getglue-data.s3.amazonaws.com/getglue_sample.tar.gz)

Import danych do bazy:
```
$ time mongoimport -d getglue -c gg --type json --file getglue.json

real    8m 11.997s
user    5m 50.901s
sys     0m 59.588s
```

#Agregacja 1
7 najpopularniejszych filmów i seriali
[JS](https://github.com/psynowczyk/tnosql2/blob/master/agg1.js)
```
db.gg.aggregate(
	{ $group: {_id: "$title", total: {$sum: 1}} },
	{ $match: {"modelName": "movies" || "tv_shows"} },
	{ $sort: {total: -1} },
	{ $limit : 7}
);
```
[PHP](https://github.com/psynowczyk/tnosql2/blob/master/agg1.php)
```
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
```
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

#Agregacja 2
7 reżyserów z największą ilością filmów lub seriali
[JS](https://github.com/psynowczyk/tnosql2/blob/master/agg2.js)
```
db.gg.aggregate(
	{ $match: {"modelName": "movies" || "tv_shows"  } },
   { $group: {_id: {"dir": "$director", id: "$title"}, total: {$sum: 1}} },
   { $group: {_id: "$_id.dir" , total: {$sum: 1}} },
   { $sort: {total: -1} },
   { $limit : 7}
);
```
[PHP](https://github.com/psynowczyk/tnosql2/blob/master/agg2.php)
```
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
```
{
	"result" : [
		{
			"_id" : "not available",
			"total" : 1474
		},
		{
			"_id" : "various directors",
			"total" : 54
		},
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
			"_id" : "takashi miike",
			"total" : 43
		},
		{
			"_id" : "jesus franco",
			"total" : 43
		}
	],
	"ok" : 1
}
```
| Reżyser                                       | Ilość dzieł |
|-----------------------------------------------|-------------|
| not available                                 | 1474        |
| various directors                             | 54          |
| alfred hitchcock                              | 50          |
| michael curtiz                                | 48          |
| woody allen                                   | 47          |
| takashi miike                                 | 43          |
| jesus franco                                  | 43          |

#Agregacja 3
7 użytkowników z ilością komentarzy powyżej 49999 (w tym przedziale zmieściło się 5 użytkowników)
[JS](https://github.com/psynowczyk/tnosql2/blob/master/agg3.js)
```
db.gg.aggregate(
	{ $group: {_id: "$userId", total: {$sum: 1}} },
	{ $match: {total: {$gte: 50000}} },
	{ $sort: {total: -1} },
	{ $limit: 7 }
);
```
[PHP](https://github.com/psynowczyk/tnosql2/blob/master/agg3.php)
```
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
```
{
	"result" : [
		{
			"_id" : "LukeWilliamss",
			"total" : 696782
		},
		{
			"_id" : "demi_konti",
			"total" : 68137
		},
		{
			"_id" : "bangwid",
			"total" : 59261
		},
		{
			"_id" : "zenofmac",
			"total" : 56233
		},
		{
			"_id" : "agentdunham",
			"total" : 55740
		}
	],
	"ok" : 1
}
```
| Użytkownik                                    | komentarze  |
|-----------------------------------------------|-------------|
| LukeWilliamss                                 | 696782      |
| demi_konti                                    | 68137       |
| bangwid                                       | 59261       |
| zenofmac                                      | 56233       |
| agentdunham                                   | 55740       |

#Agregacja 4
7 seriali z największą ilością polubień
[JS](https://github.com/psynowczyk/tnosql2/blob/master/agg4.js)
```
db.gg.aggregate(
	{ $match: {"modelName": "tv_shows"} },
	{ $match: {"action": "Liked"} },
	{ $group: {_id: "$title", total: {$sum: 1}} },
	{ $sort: {total: -1} },
	{ $limit: 7 }
);
```
[PHP](https://github.com/psynowczyk/tnosql2/blob/master/agg4.php)
```
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
```
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