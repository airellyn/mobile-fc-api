<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Mitra
 * 
 * @property int $ID
 * @property string|null $NAMA
 * @property string|null $CREATED_BY
 * @property string|null $UPDATED_BY
 * @property Carbon $CREATED_AT
 * @property Carbon $UPDATED_AT
 * 
 * @property Collection|Nasabah[] $nasabahs
 *
 * @package App\Models
 */
class Mitra extends Model
{
	protected $table = 'mitra';
	protected $primaryKey = 'ID';
	public $timestamps = false;

	protected $dates = [
		'CREATED_AT',
		'UPDATED_AT'
	];

	protected $fillable = [
		'NAMA',
		'CREATED_BY',
		'UPDATED_BY',
		'CREATED_AT',
		'UPDATED_AT'
	];

	public function nasabahs()
	{
		return $this->hasMany(Nasabah::class, 'ID_MITRA');
	}
}
