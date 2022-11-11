<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SignNasabah
 * 
 * @property int $ID
 * @property int|null $ID_COLLECTOR
 * @property int|null $ID_NASABAH
 * @property string|null $CREATED_BY
 * @property string|null $UPDATED_BY
 * @property Carbon $CREATED_AT
 * @property Carbon $UPDATED_AT
 * 
 * @property Nasabah $nasabah
 * @property Collector $collector
 * @property Collection|Penagihan[] $penagihans
 *
 * @package App\Models
 */
class SignNasabah extends Model
{
	protected $table = 'sign_nasabah';
	protected $primaryKey = 'NOMOR';
	public $timestamps = false;

	protected $casts = [
		'ID_COLLECTOR' => 'int',
		'ID_NASABAH' => 'int'
	];

	protected $dates = [
		'CREATED_AT',
		'UPDATED_AT'
	];

	protected $fillable = [
		'ID_COLLECTOR',
		'ID_NASABAH',
		'CREATED_BY',
		'UPDATED_BY',
		'CREATED_AT',
		'UPDATED_AT'
	];

	public function nasabah()
	{
		return $this->belongsTo(Nasabah::class, 'ID_NASABAH');
	}

	public function collector()
	{
		return $this->belongsTo(Collector::class, 'ID_COLLECTOR');
	}

	public function penagihans()
	{
		return $this->hasMany(Penagihan::class, 'ID_SIGN');
	}
}
