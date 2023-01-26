
<table>
    <tr>
        <th>Nombre del articulo</th>
    </tr>
    <tr>
    <td>{{ $nombreArticulo = DB::table('articulos')->where('id', $id)->value('des_articulo') }}</td>
    </tr>
</table>

<table class='table table-hover table-striped table-bordered'>
<thead>
    <tr>
        <th>Fecha</th>
        <th>Numero de solicitud</th>
        <th>Nombre del afiliado</th>
        <th>Nombre del medico</th>
        <th>Clínica</th>
        <th>Especialidad</th>
        <th>Cantidad</th>
    </tr>
</thead>
<tbody>
    @foreach ($data as $item)
    <tr>
        <td>{{ $item->created_at }}</td>
        <td>{{ $item->nrosolicitud }}</td>
<td>{{ $nombreAfiliado = DB::table('afiliados')->where('id',
                $item->afiliados_id)->value('apeynombres')}}</td>
        <td>{{ $nombreMedico = DB::table('medicos')->where('id', $item->medicos_id)->value('nombremedico') }}</td>
        <td>{{ $nombreClinica = DB::table('clinicas')->where('id', $item->clinicas_id)->value('nombre')}}</td>
        <td>{{ $grupoArticulos = DB::table('grupos')->where('id', $item->grupo_articulos)->value('des_grupo') }}</td>
        <td>{{ $cantidad = DB::table('entrantes_detail')->where('entrantes_id', $item->id)->where('articulos_id', $id)->value('cantidad') }}</td>
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
    </tr>
</thead>
<tbody>
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

    
    @endforeach

    </tr>
</tbody>

</table>