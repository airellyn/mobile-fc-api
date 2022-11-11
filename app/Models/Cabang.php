<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Cabang
 * 
 * @property int $ID
 * @property string|null $NAMA
 * @property string|null $ALAMAT
 * @property string|null $TELEPHONE
 * @property string|null $CREATED_BY
 * @property string|null $UPDATED_BY
 * @property Carbon $CREATED_AT
 * @property Carbon $UPDATED_AT
 * 
 * @property Collection|Collector[] $collectors
 *
 * @package App\Models
 */
class Cabang extends Model
{
	protected $table = 'cabang';
	protected $primaryKey = 'ID';

	protected $dates = [
		'CREATED_AT',
		'UPDATED_AT'
	];

	protected $fillable = [
		'NAMA',
		'ALAMAT',
		'TELEPHONE',
		'CREATED_BY',
		'UPDATED_BY',
		'CREATED_AT',
		'UPDATED_AT'
	];

	public function collectors()
	{
		return $this->hasMany(Collector::class, 'ID_CABANG');
	}
}
