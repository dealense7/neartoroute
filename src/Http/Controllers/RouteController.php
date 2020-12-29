<?php

namespace Dealense\NearToRoute\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RouteController extends Controller
{
    public static function neartoroute(Request $request)
    {
        $route = ((array) $request->routes) ?? array();
        $locations = ((array) $request->locations) ?? array();
        $radius = isset($request->radius) ? intval($request->radius) : 10000;
        $type = isset($request->type) ? strval($request->type) : 'car';
        // Validation
        // Check if there is at least two routes
        if(count($route) < 2){
            return response()->json(array('message' => 'There must be at least two routes.'));
        } 
        // Check if Type is wrote in standard
        if(!self::check_if_type_is_valid($type)){
            return response()->json(array('message' => "Type of movemant is not valid. ['car', 'bike', 'foot]"));
        }
        // Check if Routes are wrote in standard
        if(!self::check_standard_for_routes($route)){
            return response()->json(array('message' => "Array of Coordinates are not recorded in the standard. ['Lat', 'Lon']"));
        }
        // Check if Locations are wrote in standard
        if(!self::check_standard_for_locations($locations)){
            return response()->json(array('message' => "Array of Locations are not recorded in the standard. ['id', 'Lat', 'Lon']"));
        }

        // OSM API
        // Get waypoints from routes
        $waypoints = self::get_waypoints_from_routes_coordinates($route, $type);
        $array = self::get_nearest_by_coordinates($waypoints, $locations, $radius);
        return $array;
        return response()->json(array('message' => 'test'));
    }
    private static function check_if_type_is_valid(string $type)
    {
        $enum = [
            'car',
            'bike',
            'foot'
        ];
        if(!in_array($type, $enum)){
            return false;
        }
        return true;
    }
    private static function check_standard_for_routes($array)
    {
        foreach ($array as $item) {
            if(count($item) != 2){
                return false;
            }
            if(!isset($item['lat']) || $item['lat'] == null || $item['lat'] == ''){
                return false;
            }
            if(!isset($item['lon']) || $item['lon'] == null || $item['lon'] == ''){
                return false;
            }
        }
        return true;
    }
    
    private static function check_standard_for_locations(array $array)
    {
        foreach ($array as $item) {
            if(count($item) != 3){
                return false;
            }
            
            if(!isset($item['lat']) || $item['lat'] == null || $item['lat'] == ''){
                return false;
            }
            if(!isset($item['lon']) || $item['lon'] == null || $item['lon'] == ''){
                return false;
            }
            if(!isset($item['id'])){
                return false;
            }
        }
        return true;
    }
    private static function get_waypoints_from_routes_coordinates(array $array, string $type)
    {
        $locations = array();
        // Parse to FloatVal and design in standard
        $routes = implode(';', array_map(function($el){ return implode(',', $el); }, $array));
        $url = 'https://routing.openstreetmap.de/routed-'.$type.'/route/v1/driving/'.$routes.'?overview=false&alternatives=true&steps=true';
        $response = json_decode(Http::get($url)->body())->routes[0]->legs;
        foreach ($response as $item) {
            if($item->steps === null | count($item->steps) == 0){
                return false;
            }
            foreach ($item->steps as $step) {
                $locations[] = $step->maneuver->location;
            }
        }
        return   array_map(function($el){ return ['lat' => floatval($el[1]), 'lon' => floatval($el[0])]; }, $locations);
    }
    private static function get_nearest_by_coordinates(array $routes, array $locations, int $radius){
        $array = array();
        foreach ($locations as $loc) {
            $min = $radius;
            foreach ($routes as $item) {
                $r = 6371000; // Earth Radius in meters
                $phi1 = $item['lat'] * pi()/180; // φ, λ in radians
                $phi2 = $loc['lat'] * pi()/180;
                $deltaphi = ($loc['lat']-$item['lat']) * pi()/180;
                $deltalambda = ($loc['lon']-$item['lon']) * pi()/180;
                
                $a = sin($deltaphi/2) * sin($deltaphi/2) +
                          cos($phi1) * cos($phi2) *
                          sin($deltalambda/2) * sin($deltalambda/2);
                $c = 2 * atan2(sqrt($a), sqrt(1-$a));
                
                $d = $r * $c; // distance in meters
                $min = $d < $min ? $d : $min; // Nearest distence to waypoint of our location
            }
            // check if it's as near to the road as user wants
            if($radius > $min){
                $array[] = [
                    'id' => $loc['id'],
                    'distance' => number_format($min,2),
                ];
            }
        }
        return $array;
    }
}
