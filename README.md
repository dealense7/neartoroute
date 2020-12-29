# New Concept for Tourism Websites

if you want to give more hotels, places to your user by the type of his interest, but some of them are too far from the road and he don't have time, you can filter them also by the route  he will use and then show your user the hotels, restaurants or anything by the distance from the road as the user wants.

```
composer require dealense/neartoroute
```

To do that you need coordinates of places he will visit (format of the coordinates must be same type which is in the example), for example I have
``` 
$routes = [
[
'lat' => 42.2679,
'lon' => 42.6946
], //Kutaisi, Georgi
[
'lat' => 41.69411,
'lon' => 44.83368
], //Tbilisi, Georgi
[
'lat' => 41.64228,
'lon' => 41.63392
], //Batumi, Georgia
];
```

Also we need coordinates of the places you want to show (format of the coordinates must be same type which is in the example), for example I have
``` 
$locations = [
[
'id' => 1
'lat' => 41.3802,
'lon' => 43.2862
], //Vardzia, Sourth of Georgia
[
'id' => 2
'lat' => 42.27739,
'lon' => 42.70394
], //Bagrati, Kutaisi, Georgia
[
'id' => 3,
'lat' => 41.4448,
'lon' => 45.3751
], //Davit Gareji, Georgia
];
```

Also we can pass type of transport the tourist is using, but it's optional. Type of it is enum and please use any of there: foot, bike, car
```
$type = 'car';
```

if you want you can send search area from the route in meters, it's optional and by default it's 10km
```
$redius = 5000;
```

At the end, if you have installed the package that will create new route ```/neartoroute```, because of it's api you can use get request for that

```
$array = Http:get(url('/').'/neartoroute', [
'routes' => $routes,
'locations' => $locations,
'type' => $type,
'radius' => $radius])->body();
```

you will get same result

```
$result = [{
'id' => 2,
'distance' => 5457.45 //in meters
];
```
