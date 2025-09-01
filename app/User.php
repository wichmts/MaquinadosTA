<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

use App\Proveedor;

class User extends Authenticatable implements JWTSubject 
{

    use Notifiable;
    use HasRoles;

    protected $fillable = [
        'nombre', 'ap_paterno', 'ap_materno', 'email', 'celular', 'password'
    ];
    protected $hidden = [
        'password', 'remember_token',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function proveedor(){
        return $this->belongsTo(Proveedor::class);
    }

    public function horario(){
        return $this->hasOne(Horario::class, 'horario_id', 'medico_id');
    }

    public function getNombreCompletoAttribute()
    {
        return strtoupper("{$this->nombre} {$this->ap_paterno} {$this->ap_materno}");
    }

    public function getNombreProveedorAttribute()
    {
        if($this->proveedor){
            return $this->proveedor->nombre;
        }
        return '';
    }

    public function getEstadoIdAttribute()
    {
        return $this->broker ? $this->broker->estado_id : -1;
    }
    
    public function getClaveGeneradaAttribute()
    {
        return "{$this->clave_broker}-{$this->clave_agente}";
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function routeNotificationForMail()
    {
        return $this->email; 
    }

    public function toArray(){
  		$data = parent::toArray();
        $data['roles'] = $this->roles()->pluck('name');
        $data['permisos'] = $this->permissions->pluck('name');
        $data['nombre_completo'] = strtoupper($this->nombre . ' ' . $this->ap_paterno . ' ' . $this->ap_materno);
        $data['clave_generada'] = $this->clave_generada;
        $data['maquinas'] = $this->maquinas ? json_decode($this->maquinas) : [];
  		return $data;
  	}

    public function componentesMatricero()
    {
        return $this->hasMany(Componente::class, 'matricero_id');
    }

    public function componentesProgramador()
    {        
        return $this->hasMany(Componente::class, 'programador_id');
    }

    public function fabricaciones()
    {
        return $this->hasMany(Fabricacion::class, 'usuario_id');
    }

    public function documentos()
    {
        return $this->hasMany(Documento::class, 'usuario_id');
    }

    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'usuario_id');
    }

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'usuario_id');
    }

    public function pruebasDeProceso()
    {
        return $this->hasMany(PruebaProceso::class, 'usuario_id');
    }

    public function pruebasDeDiseno()
    {
        return $this->hasMany(PruebaDiseno::class, 'usuario_id');
    }

}
