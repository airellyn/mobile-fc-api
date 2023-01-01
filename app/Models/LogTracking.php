<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class LogTracking
 * 
 * @property int $ID
 * @property int|null $ID_COLLECTOR
 * @property string|null $LAT
 * @property string|null $LANG
 * @property Carbon $CREATED_AT
 * @property Carbon $UPDATED_AT
 * 
 * @property Collector $collector
 *
 * @package App\Models
 */
class LogTracking extends Model
{
	protected $table = 'log_tracking';
	protected $primaryKey = 'ID';
	public $timestamps = false;

	protected $casts = [
		'ID_COLLECTOR' => 'int'
	];

	protected $dates = [
		'CREATED_AT',
		'UPDATED_AT'
	];

	protected $fillable = [
		'ID_COLLECTOR',
		'LAT',
		'LANG',
		'CREATED_AT',
		'UPDATED_AT'
	];

	public function collector()
	{
		return $this->belongsTo(Collector::class, 'ID_COLLECTOR');
	}
}
