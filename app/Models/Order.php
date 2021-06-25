<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $table='don_hang';
    protected $primaryKey='Id';
    protected $fillable=[
        'Id_KH',
        'Ngay_Lap',
        'Dia_Chi_Giao_Hang',
        'Tong_Tien',
        'Trang_Thai',
        'Ho_Ten',
        'So_Dien_Thoai'


    ];
    public function Account(){
        return $this->belongsTo('App\Models\Account', 'Id_KH', 'Id');
    }
    
    public $timestamps = false;
}
