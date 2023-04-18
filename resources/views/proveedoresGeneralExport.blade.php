<table>
    <tr>
        <th>Proveedor</th>
        <th>Periodo</th>
        <th>Solicitudes entrantes del periodo</th>
        <th>Solicitudes cotizadas</th>
        <th>Solicitudes adjudicadas</th>
        <th> Solicitudes no adjudicadas </th>
        <th> Solicitudes finalizadas</th>
        <th> Total cotizado </th>
    </tr>

    @foreach($proveedores as $p)
            <?php
            $cotizaciones2 = DB::table('cotizaciones')
                ->where('proveedor', $p)
                ->whereBetween('created_at', [$start_date, $end_date])
                ->get();

            $entrantes2 = DB::table('entrantes')->whereBetween('created_at', [$start_date, $end_date])->get();

            $adjudicaciones = DB::table('adjudicaciones')
                ->where('adjudicatario', $p)
                ->whereBetween('created_at', [$start_date, $end_date])->get();

             $finalizadas = DB::table('cotizaciones')
                ->where('proveedor', $p)
                ->whereBetween('created_at', [$start_date, $end_date])
                ->where('estado_solicitud_id', 6)->get();

             $total_cotizado = DB::table('cotizaciones')
                ->where('proveedor', $p)
                ->whereBetween('created_at', [$start_date, $end_date])
                ->sum('total');
            ?>
        <tr>
            <td>{{ $p }}</td>
            <td>{{ $start_date . ' -- ' . $end_date }}</td>
            <td>{{ count($entrantes2) }}</td>
            <td>{{ count($cotizaciones2) }}</td>
            <td>{{ count($adjudicaciones) }}</td>
            <td>{{ count($cotizaciones2) - count($adjudicaciones) }}</td>
            <td>{{ count($finalizadas) }}</td>
            <td> $ {{ $total_cotizado }} </td>
        </tr>
    @endforeach
</table>
