<?php
namespace Dealense\NearToRoute;

use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'./routes/web.php');
    }
    public function register()
    {
            
    }

}