<?php


class AdminTableSeeder extends \Illuminate\Database\Seeder{
    public function run(){
      \App\Model\Admin\AdminModel::create([
          'username'=>'admin',
          'password'=>\Illuminate\Support\Facades\Hash::make('123456'),
          'email'=>'12@qq.com',
          'real_name'=>'admin'
      ]);
    }
}