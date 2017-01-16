<?php

namespace App\Model\Backend;
use DB;
use Illuminate\Database\Eloquent\Model;

/**
 * Description of BackUser
 *
 * @author lenovo
 */
class BackendUser extends Model {

    protected $table = 'backend_user';
    protected $fillable = ['said', 'status'];

    public static function findByUsername($username) {
        //分本地用户和安硕用户登录
        if (is_numeric($username)){
            //dd(33);
            $user = \Illuminate\Support\Facades\DB::table('sync_user_info')->where('USERID',$username)->first();
            //dd($user->USERID);
        } else {
            $user = self::where('username', $username)->first()->toArray();
        }
        /*if (!empty($user)) {
            $user = $user->toArray();
        }*/
        return $user;
    }

    public static function findByPage($condition, $page_size = 10) {
        $query = self::orderBy('id','DESC');
        if (isset($condition['username'])) {
            $query->where('username', 'like', "%{$condition['username']}%");
        }
        return $query->paginate($page_size);
    }

    /**
     * 根据主键查找
     * @param type $pk
     */
    public static function findByPk($pk) {
        $user = self::where('id', $pk)->first();
        if (!empty($user)) {
            $user = $user->toArray();
        }
        return $user;
    }

    /**
     * 根据指定的字段查找
     * @param type $fieldName
     * @param type $value
     */
    public static function findByField($fieldName, $value) {
        $user = self::where($fieldName, $value)->first();
        if (!empty($user)) {
            $user = $user->toArray();
        }
        return $user;
    }

    /**
     * @param type $fieldName
     * @param type $value
     */
    public static function editCheck($fieldName, $value, $id) {
        $user = self::where($fieldName, $value)->where('id', '!=', $id)->first();
        return $user;
    }
    public  function AddUsers($data){
        $insert=DB::table('backend_user')->insert($data);
        return $insert;
    }

}
