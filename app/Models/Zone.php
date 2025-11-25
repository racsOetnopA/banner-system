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
        return $this->belongsToMany(Banner::class, 'banner_zone')->withTimestamps();
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

        $base = function () use ($now) {
            return Banner::query()
                ->where('active', true)
                ->where(function ($q) use ($now) {
                    $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
                })
                ->where(function ($q) use ($now) {
                    $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
                });
        };

        // Banners principales asociados a esta zona
        $principal = $base()
            ->where('principal', true)
            ->whereHas('zones', function ($q) { $q->where('zones.id', $this->id); })
            ->orderBy('created_at', 'desc')
            ->get();

        // Banners no principales asociados a esta zona (aleatorio)
        $others = $base()
            ->where(function ($q) { $q->where('principal', false)->orWhereNull('principal'); })
            ->whereHas('zones', function ($q) { $q->where('zones.id', $this->id); })
            ->inRandomOrder()
            ->get();

        $combined = $principal->concat($others);

        if ($limit) {
            return $combined->take($limit);
        }

        return $combined;
    }
}
