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
 * @property int $ID
 * @property int|null $ID_COLLECTOR
 * @property string|null $USERNAME
 * @property string|null $PASSWORD
 * @property string|null $API_TOKEN
 * @property int|null $IS_WEB
 * @property Carbon $CREATED_AT
 * @property Carbon $UPDATED_AT
 *
 * @package App\Models
 */
class Settings extends Model
{
	protected $table = 'settings';
	protected $primaryKey = 'id';
}
