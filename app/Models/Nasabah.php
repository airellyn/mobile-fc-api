<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Nasabah
 * 
 * @property int $ID
 * @property int|null $ID_AREA
 * @property int|null $ID_MITRA
 * @property string|null $NAMA
 * @property string|null $TELEPHONE
 * @property string|null $TELEPHONE_RUMAH
 * @property string|null $TELEPHONE_KANTOR
 * @property string|null $ALAMAT_RUMAH
 * @property string|null $ALAMAT_KANTOR
 * @property float|null $TOTAL_TAGIHAN
 * @property Carbon|null $TANGGAL_CLOSE
 * @property int|null $IS_CLOSE
 * @property int|null $IS_AKTIF
 * @property string|null $CREATED_BY
 * @property string|null $UPDATED_BY
 * @property Carbon $CREATED_AT
 * @property Carbon $UPDATED_AT
 * 
 * @property Area $area
 * @property Mitra $mitra
 * @property Collection|SignNasabah[] $sign_nasabahs
 *
 * @package App\Models
 */
class Nasabah extends Model
{
	protected $table = 'nasabah';
	protected $primaryKey = 'ID';
	public $timestamps = false;

	protected $casts = [
		'ID_AREA' => 'int',
		'ID_MITRA' => 'int',
		'TOTAL_TAGIHAN' => 'float',
		'IS_CLOSE' => 'int',
		'IS_AKTIF' => 'int'
	];

	protected $dates = [
		'TANGGAL_CLOSE',
		'CREATED_AT',
		'UPDATED_AT'
	];

	protected $fillable = [
		'ID_AREA',
		'ID_MITRA',
		'NAMA',
		'TELEPHONE',
		'TELEPHONE_RUMAH',
		'TELEPHONE_KANTOR',
		'ALAMAT_RUMAH',
		'ALAMAT_KANTOR',
		'TOTAL_TAGIHAN',
		'TANGGAL_CLOSE',
		'IS_CLOSE',
		'IS_AKTIF',
		'CREATED_BY',
		'UPDATED_BY',
		'CREATED_AT',
		'UPDATED_AT'
	];

	public function area()
	{
		return $this->belongsTo(Area::class, 'ID_AREA');
	}

	public function mitra()
	{
		return $this->belongsTo(Mitra::class, 'ID_MITRA');
	}

	public function sign_nasabahs()
	{
		return $this->hasMany(SignNasabah::class, 'ID_NASABAH');
	}
}
