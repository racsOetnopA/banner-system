<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Web;

class Zone extends Model
{
    protected $fillable = ['name','description','width','height','web_id'];


    public function assignments(){ return $this->hasMany(Assignment::class); }

    public function web()
    {
        return $this->belongsTo(Web::class);
    }

    public function banners()
    {
        return $this->belongsToMany(Banner::class, 'banner_zone')->withPivot('principal')->withTimestamps();
    }

    /**
     * Obtener banners listos para mostrarse en la zona.
     * - Incluye sólo banners activos y dentro del rango de fechas (si aplica).
     * - Devuelve primero los banners marcados como `principal` (en orden por fecha de creación),
     *   y luego el resto en orden aleatorio.
     *
     * @param int|null $limit
     * @return \Illuminate\Support\Collection
     */
    public function bannersForDisplay(?int $limit = null)
    {
        $now = now();

        // Start from the relation so pivot data is loaded (including `principal`)
        $baseQuery = $this->banners()->where('active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
            });

        // Principals for this zone (via pivot)
        $principal = (clone $baseQuery)
            ->where('banner_zone.principal', true)
            ->orderBy('banners.created_at', 'desc')
            ->get();

        // Others (non-principal)
        $others = (clone $baseQuery)
            ->where(function ($q) { $q->whereNull('banner_zone.principal')->orWhere('banner_zone.principal', false); })
            ->inRandomOrder()
            ->get();

        $combined = $principal->concat($others);

        if ($limit) {
            return $combined->take($limit);
        }

        return $combined;
    }
}
