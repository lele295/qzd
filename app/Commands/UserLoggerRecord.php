<?php
namespace App\Commands;
use App\Log\Facades\Logger;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UserLoggerRecord extends Command implements SelfHandling,ShouldQueue{
    use InteractsWithQueue,SerializesModels;

    public function __construct(){

    }

    public function handle(){

    }
}