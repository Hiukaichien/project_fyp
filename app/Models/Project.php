<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\JenayahPaper;
use App\Models\NarkotikPaper;
use App\Models\TrafikSeksyenPaper;
use App\Models\TrafikRulePaper;
use App\Models\KomersilPaper;
use App\Models\LaporanMatiMengejutPaper; 
use App\Models\OrangHilangPaper;

class Project extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'project_date',
        'description',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'project_date' => 'date:Y-m-d', // Ensures it's treated as a Carbon date object
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Define relationships to each paper type.
     */

    public function jenayahPapers()
    {
        return $this->hasMany(JenayahPaper::class, 'project_id');
    }

    public function narkotikPapers()
    {
        return $this->hasMany(NarkotikPaper::class, 'project_id');
    }

    public function trafikSeksyenPapers()
    {
        return $this->hasMany(TrafikSeksyenPaper::class, 'project_id');
    }

    public function trafikRulePapers()
    {
        return $this->hasMany(TrafikRulePaper::class, 'project_id');
    }

    public function komersilPapers()
    {
        return $this->hasMany(KomersilPaper::class, 'project_id');
    }

    public function laporanMatiMengejutPapers() // Renamed method
    {
        return $this->hasMany(LaporanMatiMengejutPaper::class, 'project_id'); // Changed model class
    }

    public function orangHilangPapers()
    {
        return $this->hasMany(OrangHilangPaper::class, 'project_id');
    }

    /**
     * Get all associated papers grouped by type.
     * The keys in the returned array should match what the view expects
     */
    public function allAssociatedPapers()
    {
        // Eager load the relationships to avoid N+1 issues if this method is called multiple times
        // or if the project object is used elsewhere where these relations are looped.
        $this->loadMissing([
            'jenayahPapers', 
            'narkotikPapers', 
            'trafikSeksyenPapers',
            'trafikRulePapers',
            'komersilPapers',
            'laporanMatiMengejutPapers', // Renamed relationship
            'orangHilangPapers'
        ]);

        return [
            'jenayah_papers' => $this->jenayahPapers,
            'narkotik_papers' => $this->narkotikPapers,
            'trafik_seksyen_papers' => $this->trafikSeksyenPapers,
            'trafik_rule_papers' => $this->trafikRulePapers,
            'komersil_papers' => $this->komersilPapers,
            'laporan_mati_mengejut_papers' => $this->laporanMatiMengejutPapers, // Renamed key and property
            'orang_hilang_papers' => $this->orangHilangPapers,
        ];
    }

    /**
     * Get all associated papers merged into a single collection.
     * (Alternative to allAssociatedPapers if you don't need grouping by type in some contexts)
     */
    public function allPapersMerged()
    {
        $collections = [
            $this->jenayahPapers()->get(),
            $this->narkotikPapers()->get(),
            $this->trafikSeksyenPapers()->get(),
            $this->trafikRulePapers()->get(),
            $this->komersilPapers()->get(),
            $this->laporanMatiMengejutPapers()->get(), // Renamed method call
            $this->orangHilangPapers()->get(),
        ];

        $merged = collect();
        foreach ($collections as $collection) {
            $merged = $merged->merge($collection);
        }
        return $merged;
    }
}
