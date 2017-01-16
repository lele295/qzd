<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/5/27
 * Time: 20:02
 */

namespace App\Commands;


use App\Http\Controllers\mobile\TestController;
use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Test extends Command implements SelfHandling
{

    use InteractsWithQueue, SerializesModels;

    private $id;

    public function __construct($id){
        $this->id = $id;
    }
    public function handle(){
        $test = new TestController();
        $test->insert_into_data($this->id);
    }
}