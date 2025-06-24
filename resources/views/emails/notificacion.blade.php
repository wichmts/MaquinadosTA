@component('mail::message')

# {{ $descripcion }}

@isset($datosExtra['fecha'])
**Fecha:** {{ $datosExtra['fecha'] }}  
**Hora:** {{ $datosExtra['hora'] }}  
@endisset

@isset($datosExtra['componente'])
**Componente:** {{ $datosExtra['componente'] }}  
@endisset
@isset($datosExtra['herramental'])
**Herramental:** {{ $datosExtra['herramental'] }}  
@endisset
@isset($datosExtra['proyecto'])
**Proyecto:** {{ $datosExtra['proyecto'] }}  
@endisset
@isset($datosExtra['cliente'])
**Cliente:** {{ $datosExtra['cliente'] }}  
@endisset
@isset($datosExtra['anio'])
**Año:** {{ $datosExtra['anio'] }}  
@endisset
@component('mail::button', ['url' => $url])
Ver en el sistema.
@endcomponent

Gracias,<br>
{{ config('app.name') }}
@endcomponent
