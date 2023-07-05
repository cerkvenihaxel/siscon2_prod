
<table>
    <tr>
        <th>Nombre del articulo</th>
    </tr>
    <tr>
    <td>{{ $nombreArticulo = DB::table('articulos')->where('id', $id)->value('des_articulo') }}</td>
    </tr>
</table>

<table>
    <tr>
        <th> Cantidad de solicitudes realizadas </th>
        <th> Cantidad total de pedidos del artículo </th>

    </tr>
    <tr>
        <td>{{ count($data)}}</td>
        <td>{{ $cantidadTotal = DB::table('entrantes_detail')->where('articulos_id', $id)->sum('cantidad') }}</td>
    </tr>
</table>


<table class='table table-hover table-striped table-bordered'>
<thead>
    <tr>
        <th>Fecha de pedido del médico</th>
        <th>Numero de solicitud</th>
        <th>Nombre del afiliado</th>
        <th>Nombre del medico</th>
        <th>Clínica</th>
        <th>Edad</th>
        <th>Especialidad</th>
        <th>Cantidad</th>
        <th>Estado solicitud</th>
    </tr>
</thead>
<tbody>
    @foreach ($data as $item)
    <tr>
        <td>{{ $item->created_at }}</td>
        <td>{{ $item->nrosolicitud }}</td>
        <td>{{ $nombreAfiliado = DB::table('afiliados')->where('id', $item->afiliados_id)->value('apeynombres')}}</td>
        <td>{{ $nombreMedico = DB::table('medicos')->where('id', $item->medicos_id)->value('nombremedico') }}</td>
        <td>{{ $nombreClinica = DB::table('clinicas')->where('id', $item->clinicas_id)->value('nombre')}}</td>
        <td>{{ $item->edad }}</td>
        <td>{{ $grupoArticulos = DB::table('grupos')->where('id', $item->grupo_articulos)->value('des_grupo') }}</td>
        <td>{{ $cantidad = DB::table('entrantes_detail')->where('entrantes_id', $item->id)->where('articulos_id', $id)->value('cantidad') }}</td>
        <td>{{ $estado = DB::table('estado_solicitud')->where('id', $item->estado_solicitud_id)->value('estado') }}</td>
    </tr>
    @endforeach
</tbody>

</table>

<table class='table table-hover table-striped table-bordered'>
    <h3>Datos de cotizaciones del artículo</h3>
<thead>
    <tr>
        <th>Fecha de cotización</th>
        <th>Numero de solicitud</th>
        <th>Nombre del afiliado</th>
        <th>Nombre del medico</th>
        <th>Proveedor</th>
        <th>Precio unitario</th>
        <th>Cantidad</th>
        <th>Subtotal</th>
        <th>Estado solicitud</th>
    </tr>
</thead>
<tbody>
    @if($cot>0)
    @foreach ($cot as $item2)
    <tr>
        <td>{{ $item2->created_at }}</td>
        <td>{{ $item2->nrosolicitud }}</td>
        <td>{{ $nombreAfiliado = DB::table('afiliados')->where('id', $item2->afiliados_id)->value('apeynombres')}}</td>
        <td>{{ $nombreMedico = DB::table('medicos')->where('id', $item2->medicos_id)->value('nombremedico') }}</td>
        <td>{{ $item2->proveedor }}</td>
        <td>{{ $precioUnitario = DB::table('cotizaciones_detail')->where('entrantes_id', $item2->id)->where('articulos_id', $id)->value('precio_unitario') }}</td>
        <td>{{ $cantidad = DB::table('cotizaciones_detail')->where('entrantes_id', $item2->id)->where('articulos_id', $id)->value('cantidad') }}</td>
        <td>{{ $subtotal = DB::table('cotizaciones_detail')->where('entrantes_id', $item2->id)->where('articulos_id', $id)->value('precio') }}</td>
        <td>{{ $estado = DB::table('estado_solicitud')->where('id', $item2->estado_solicitud_id)->value('estado') }}</td>

    @endforeach
    @else
    <tr>
        <td colspan="8">No hay cotizaciones para este artículo</td>
    </tr>
</tbody>
    @endif

</table>
