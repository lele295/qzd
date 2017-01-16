<?php
namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AgEnterpriseServiceProvider extends ServiceProvider
{
    public function register(){}

    public function boot()
    {
        Blade::directive('versionfiles', function($expression) {
            return '<?php echo App\Util\Kits::echoFile'.$expression.';?>';
        });
    }
}