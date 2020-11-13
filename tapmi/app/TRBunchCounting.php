<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TRBunchCounting extends Model
{
    //  
    protected $connection = 'mobile_ins';
    protected $table = 'TR_BUNCH_COUNTING_VERIFICATION';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = [
        'TANGGAL_TRANSAKSI',
        'NIK_PIC',
        'NAMA_PIC',
        'ROLE_PIC',
        'BA_CODE',
        'BA_NAME',
        'AFD_CODE',
        'BLOCK_CODE',
        'BLOCK_NAME',
        'NO_TPH',
        'NO_BCC',
        'TOTAL_JJG_PIC',
        'TOTAL_JJG_AI',
        'SELISIH_JJG',
        'PERCENT_VARIANCE',
        'SCORES_AI',
        'START_PROCESS_AI',
        'END_PROCESS_AI',
        'DATA_SOURCE',
        'IMG_URL',
        'IMG_NAME',
        'KONDISI_FOTO',
        'INSERT_TIME',
        'INSERT_USER',
        'INSERT_USER_FULLNAME',
        'INSERT_USER_USERROLE',
        'AVG_SCORES_AI'
    ];
}
