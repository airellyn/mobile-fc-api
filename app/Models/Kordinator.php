<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Kordinator
 * 
 * @property int $ID
 * @property int|null $ID_AREA
 * @property string|null $NAMA
 * @property string|null $TELEPHONE
 * @property string|null $CREATED_BY
 * @property string|null $UPDATED_BY
 * @property Carbon $CREATED_AT
 * @property Carbon $UPDATED_AT
 * 
 * @property Area $area
 * @property Collection|Collector[] $collectors
 *
 * @package App\Models
 */
class Kordinator extends Model
{
	protected $table = 'kordinator';
	protected $primaryKey = 'ID';
	public $timestamps = false;

	protected $casts = [
		'ID_AREA' => 'int'
	];

	protected $dates = [
		'CREATED_AT',
		'UPDATED_AT'
	];

	protected $fillable = [
		'ID_AREA',
		'NAMA',
		'TELEPHONE',
		'CREATED_BY',
		'UPDATED_BY',
		'CREATED_AT',
		'UPDATED_AT'
	];

	public function area()
	{
		return $this->belongsTo(Area::class, 'ID_AREA');
	}

	public function collectors()
	{
		return $this->hasMany(Collector::class, 'ID_KORDINATOR');
	}
}
