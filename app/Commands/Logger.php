<?php
namespace App\Commands;

use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

class Logger extends Command implements SelfHandling,ShouldQueue{
    use InteractsWithQueue, SerializesModels;
    private $message;
    public function __construct($message){
        $this->message = $message;
    }

    public function handle(){

    }

    public function fail(){

    }
}