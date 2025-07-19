<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\Jenayah;
use App\Models\Narkotik;
use App\Models\TrafikSeksyen;
use App\Models\Komersil;
use App\Models\LaporanMatiMengejut;
use App\Models\OrangHilang;

class Project extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', // <-- ADD THIS LINE
        'name',
        'project_date',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'project_date' => 'date:Y-m-d',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the project.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

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
    
    public function TrafikSeksyen()
    {
        return $this->hasMany(TrafikSeksyen::class, 'project_id');
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
            'TrafikSeksyen',
            'orangHilang',
            'laporanMatiMengejut',
        ]);

        return [
            'jenayah' => $this->jenayah,
            'narkotik' => $this->narkotik,
            'komersil' => $this->komersil,
            'TrafikSeksyen' => $this->TrafikSeksyen,
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
            $this->TrafikSeksyen()->get(),
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