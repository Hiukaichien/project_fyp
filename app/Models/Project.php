<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Jenayah;
use App\Models\Narkotik;
use App\Models\Trafik;
use App\Models\Komersil;
use App\Models\LaporanMatiMengejut; 
use App\Models\OrangHilang;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'project_date',
        'description',
    ];

    protected $casts = [
        'project_date' => 'date:Y-m-d',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Define relationships to each paper type.
     */
    public function jenayah()
    {
        return $this->hasMany(Jenayah::class, 'project_id');
    }

    public function narkotik()
    {
        return $this->hasMany(Narkotik::class, 'project_id');
    }

    public function komersil()
    {
        return $this->hasMany(Komersil::class, 'project_id');
    }
    
    public function trafik()
    {
        return $this->hasMany(Trafik::class, 'project_id');
    }

    public function orangHilang()
    {
        return $this->hasMany(OrangHilang::class, 'project_id');
    }

    public function laporanMatiMengejut()
    {
        return $this->hasMany(LaporanMatiMengejut::class, 'project_id');
    }

    /**
     * Get all associated papers grouped by type.
     */
    public function allAssociatedPapers()
    {
        $this->loadMissing([
            'jenayah', 
            'narkotik', 
            'komersil',
            'trafik',
            'orangHilang',
            'laporanMatiMengejut',
        ]);

        return [
            'jenayah' => $this->jenayah,
            'narkotik' => $this->narkotik,
            'komersil' => $this->komersil,
            'trafik' => $this->trafik,
            'orang_hilang' => $this->orangHilang,
            'laporan_mati_mengejut' => $this->laporanMatiMengejut,
        ];
    }

    /**
     * Get all associated papers merged into a single collection.
     */
    public function allPapersMerged()
    {
        $collections = [
            $this->jenayah()->get(),
            $this->narkotik()->get(),
            $this->komersil()->get(),
            $this->trafik()->get(),
            $this->orangHilang()->get(),
            $this->laporanMatiMengejut()->get(),
        ];

        $merged = collect();
        foreach ($collections as $collection) {
            $merged = $merged->merge($collection);
        }
        return $merged;
    }
}