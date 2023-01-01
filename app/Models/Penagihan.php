<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Penagihan
 * 
 * @property int $ID
 * @property int|null $ID_SIGN
 * @property int|null $ID_NASABAH
 * @property int|null $ID_COLLECTOR
 * @property Carbon $TANGGAL
 * @property string|null $KETERANGAN
 * @property string|null $FOTO_RUMAH
 * @property string|null $BUKTI_PEMBAYARAN
 * @property float|null $AMCOLL
 * @property string|null $LAT
 * @property string|null $LANG
 * @property int|null $IS_CLOSE
 * @property float|null $NILAI_PTP
 * @property Carbon|null $TANGGAL_PTP
 * @property string|null $CREATED_BY
 * @property string|null $UPDATED_BY
 * @property Carbon $CREATED_AT
 * @property Carbon $UPDATED_AT
 * 
 * @property SignNasabah $sign_nasabah
 *
 * @package App\Models
 */
class Penagihan extends Model
{
	protected $table = 'penagihan';
	protected $primaryKey = 'ID';
	public $timestamps = false;

	protected $casts = [
		'ID_SIGN' => 'int',
		'ID_NASABAH' => 'int',
		'ID_COLLECTOR' => 'int',
		'AMCOLL' => 'float',
		'IS_CLOSE' => 'int',
		'NILAI_PTP' => 'float'
	];

	protected $dates = [
		'TANGGAL',
		'TANGGAL_PTP',
		'CREATED_AT',
		'UPDATED_AT'
	];

	protected $fillable = [
		'ID_SIGN',
		'ID_NASABAH',
		'ID_COLLECTOR',
		'TANGGAL',
		'KETERANGAN',
		'FOTO_RUMAH',
		'BUKTI_PEMBAYARAN',
		'AMCOLL',
		'LAT',
		'LANG',
		'IS_CLOSE',
		'NILAI_PTP',
		'TANGGAL_PTP',
		'CREATED_BY',
		'UPDATED_BY',
		'CREATED_AT',
		'UPDATED_AT'
	];

	public function sign_nasabah()
	{
		return $this->belongsTo(SignNasabah::class, 'ID_SIGN');
	}
}
