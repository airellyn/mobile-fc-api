<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Area
 * 
 * @property int $ID
 * @property string|null $NAMA
 * @property string|null $TELEPHONE
 * @property string|null $CREATED_BY
 * @property string|null $UPDATED_BY
 * @property Carbon $CREATED_AT
 * @property Carbon $UPDATED_AT
 * 
 * @property Collection|Kordinator[] $kordinators
 * @property Collection|Nasabah[] $nasabahs
 *
 * @package App\Models
 */
class Area extends Model
{
	protected $table = 'area';
	protected $primaryKey = 'ID';
	public $timestamps = false;

	protected $dates = [
		'CREATED_AT',
		'UPDATED_AT'
	];

	protected $fillable = [
		'NAMA',
		'TELEPHONE',
		'CREATED_BY',
		'UPDATED_BY',
		'CREATED_AT',
		'UPDATED_AT'
	];

	public function kordinators()
	{
		return $this->hasMany(Kordinator::class, 'ID_AREA');
	}

	public function nasabahs()
	{
		return $this->hasMany(Nasabah::class, 'ID_AREA');
	}
}
