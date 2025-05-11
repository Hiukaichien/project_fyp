<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    /**
     * Get all of the Jenayah papers associated with the project.
     */
    public function jenayahPapers()
    {
        // Assuming your JenayahPaper model is named 'JenayahPaper'
        // and the foreign key in 'jenayah_papers' table is 'project_id'
        return $this->hasMany(JenayahPaper::class, 'project_id');
    }

    /**
     * Get all of the Narkotik papers associated with the project.
     */
    public function narkotikPapers()
    {
        // Assuming your NarkotikPaper model is named 'NarkotikPaper'
        return $this->hasMany(NarkotikPaper::class, 'project_id');
    }

    /**
     * Get all of the Komersil papers associated with the project.
     */
    public function komersilPapers()
    {
        // Assuming your KomersilPaper model is named 'KomersilPaper'
        return $this->hasMany(KomersilPaper::class, 'project_id');
    }

    /**
     * Get all of the Trafik Seksyen papers associated with the project.
     */
    public function trafikSeksyenPapers()
    {
        // Assuming your TrafikSeksyenPaper model is named 'TrafikSeksyenPaper'
        return $this->hasMany(TrafikSeksyenPaper::class, 'project_id');
    }

    /**
     * Get all of the Trafik Rule papers associated with the project.
     */
    public function trafikRulePapers()
    {
        // Assuming your TrafikRulePaper model is named 'TrafikRulePaper'
        return $this->hasMany(TrafikRulePaper::class, 'project_id');
    }

    /**
     * Get all of the Orang Hilang papers associated with the project.
     */
    public function orangHilangPapers()
    {
        // Assuming your OrangHilangPaper model is named 'OrangHilangPaper'
        return $this->hasMany(OrangHilangPaper::class, 'project_id');
    }

    /**
     * Get all of the Laporan Mati Mengejut papers associated with the project.
     */
    public function laporanMatiMengejutPapers()
    {
        // Assuming your LaporanMatiMengejutPaper model is named 'LaporanMatiMengejutPaper'
        return $this->hasMany(LaporanMatiMengejutPaper::class, 'project_id');
    }

    /**
     * An example of a helper method to get all papers from all types for a project.
     * This might be useful for a project overview page.
     * Note: This will return a collection of collections, or you could merge them.
     */
    public function allAssociatedPapers()
    {
        return [
            'jenayah' => $this->jenayahPapers,
            'narkotik' => $this->narkotikPapers,
            'komersil' => $this->komersilPapers,
            'trafik_seksyen' => $this->trafikSeksyenPapers,
            'trafik_rule' => $this->trafikRulePapers,
            'orang_hilang' => $this->orangHilangPapers,
            'laporan_mati_mengejut' => $this->laporanMatiMengejutPapers,
        ];
    }

    /**
     * If you want a single merged collection of all papers (might lose type distinction easily):
     * Be cautious with this if different paper types have vastly different fields you need to access.
     */
    public function allPapersMerged()
    {
        $allPapers = collect([]);
        $allPapers = $allPapers->merge($this->jenayahPapers);
        $allPapers = $allPapers->merge($this->narkotikPapers);
        $allPapers = $allPapers->merge($this->komersilPapers);
        $allPapers = $allPapers->merge($this->trafikSeksyenPapers);
        $allPapers = $allPapers->merge($this->trafikRulePapers);
        $allPapers = $allPapers->merge($this->orangHilangPapers);
        $allPapers = $allPapers->merge($this->laporanMatiMengejutPapers);

        // You might want to sort this merged collection, e.g., by a common date field
        // return $allPapers->sortBy('tarikh_ks_dibuka_variant'); // if you add such an accessor
        return $allPapers;
    }
}
