<?php
namespace App\Console\Commands;

use App\Model\Yitu\YituModel;
use Illuminate\Console\Command;

/**
 * Author: CHQ
 * Time: 2016/7/7 17:53
 * Usage:
 * Update:
 */
class CleanYituCache extends Command{
	protected $name = 'clean:yitucache';
	protected $description = '删除非当天的依图接口缓存记录';

	public function fire(){
		YituModel::cleanExpiredRecords();
	}
}