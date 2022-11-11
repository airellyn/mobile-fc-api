<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User
 * 
 * @property int $id
 * @property int|null $id_mitra
 * @property int|null $id_collection
 * @property int|null $id_kordinator
 * @property string|null $username
 * @property string|null $password
 * @property string|null $pass
 * @property string|null $api_token
 * @property int|null $type
 * @property int|null $is_web
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class User extends Model
{
	protected $table = 'users';

	protected $casts = [
		'id_mitra' => 'int',
		'id_collection' => 'int',
		'id_kordinator' => 'int',
		'type' => 'int',
		'is_web' => 'int'
	];

	protected $hidden = [
		'password',
		'api_token'
	];

	protected $fillable = [
		'id_mitra',
		'id_collection',
		'id_kordinator',
		'username',
		'password',
		'pass',
		'api_token',
		'type',
		'is_web'
	];
}
