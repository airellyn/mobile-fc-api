<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Collector
 * 
 * @property int $ID
 * @property int|null $ID_KORDINATOR
 * @property string|null $ID_KARYAWAN
 * @property string|null $NAMA
 * @property Carbon|null $TANGGAL_MASUK
 * @property string|null $TELEPHONE
 * @property string|null $ALAMAT
 * @property string|null $FOTO
 * @property int|null $IS_AKTIF
 * @property string|null $CREATED_BY
 * @property string|null $UPDATED_BY
 * @property Carbon $CREATED_AT
 * @property Carbon $UPDATED_AT
 * 
 * @property Kordinator $kordinator
 * @property Collection|LogTracking[] $log_trackings
 * @property Collection|SignNasabah[] $sign_nasabahs
 *
 * @package App\Models
 */
class Collector extends Model
{
	protected $table = 'collector';
	protected $primaryKey = 'ID';
	public $timestamps = false;

	protected $casts = [
		'ID_KORDINATOR' => 'int',
		'IS_AKTIF' => 'int'
	];

	protected $dates = [
		'TANGGAL_MASUK',
		'CREATED_AT',
		'UPDATED_AT'
	];

	protected $fillable = [
		'ID_KORDINATOR',
		'ID_KARYAWAN',
		'NAMA',
		'TANGGAL_MASUK',
		'TELEPHONE',
		'ALAMAT',
		'FOTO',
		'IS_AKTIF',
		'CREATED_BY',
		'UPDATED_BY',
		'CREATED_AT',
		'UPDATED_AT'
	];

	public function kordinator()
	{
		return $this->belongsTo(Kordinator::class, 'ID_KORDINATOR');
	}

	public function log_trackings()
	{
		return $this->hasMany(LogTracking::class, 'ID_COLLECTOR');
	}

	public function sign_nasabahs()
	{
		return $this->hasMany(SignNasabah::class, 'ID_COLLECTOR');
	}
}
