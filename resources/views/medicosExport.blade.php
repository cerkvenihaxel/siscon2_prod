
<table>
    <tr>
        <th>Nombre del Médico</th>
        <th>Solicitudes realizadas</th>
        <th>Solicitudes adjudicadas</th>
        <th> Solicitudes no adjudicadas </th>

    </tr>
    <tr>
    <td>{{ $nombreMedico }}</td>
    <td>{{ count($data) }}</td>
    <td> {{ count($adj) }}</td>
    <td> {{ count($data) - count($adj) }}</td>
    </tr>
</table>

<table class='table table-hover table-striped table-bordered'>
<thead>
    <tr>
        <th>Fecha de pedido</th>
        <th>Numero de solicitud</th>
        <th>Nombre del afiliado</th>
        <th>Clínica</th>
        <th>Fecha de cirugía</th>
    </tr>
</thead>
<tbody>
    @foreach ($data as $item)

   
    <tr>
        <td>{{ $item->created_at }}</td>
        <td>{{ $item->nrosolicitud }}</td>
        <td>{{ $nombreAfiliado = DB::table('afiliados')->where('id', $item->afiliados_id)->value('apeynombres')}}</td>
        <td>{{ $clinicasNombre= DB::table('clinicas')->where('id', $item->clinicas_id)->value('nombre')}}</td>
        <td> {{ $item->fecha_cirugia }}</td>
        

    @endforeach

    </tr>
</tbody>

</table>

<table>
    <thead>
        <tr>
            <th>Fecha de adjudicación</th>
            <th>Número de solicitud</th>
            <th>Afiliado</th>
            <th>Proveedor Adjudicado</th>
            <th>Clínica</th>
            <th>Fecha de cirugía</th>
        </tr>
    </thead>
    <tbody>
        @foreach($adj as $a)
        <tr>
            <td>{{ $a->created_at }}</td>
            <td>{{ $a->nrosolicitud }}</td>
            <td>{{ $afiliado = DB::table('afiliados')->where('id', $a->afiliados_id )->value('apeynombres')}}</td>
            <td>{{ $a->adjudicatario }}</td>
            <td>{{ $clinica = DB::table('clinicas')->where('id', $a->clinicas_id )->value('nombre')}}</td>
            <td>{{ $a->fecha_cirugia }}</td>
        </tr>
        @endforeach
    </tbody>
</table>