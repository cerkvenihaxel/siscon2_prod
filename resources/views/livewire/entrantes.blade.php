<div>

<h2 class="font-light text-xl mx-auto text-gray-800 leading-tight">
    <i class="ion-clipboard"></i> {{ __('Entrantes: ') }} {{ $count }}
</h2>

    <div class="d-flex justify-content-end">
        <h4>
            <button class="btn btn-success font-light" wire:click="openModal">Nuevo pedido</button>
            <button class="btn btn-warning font-light" wire:click="increment">Exportar</button>
        </h4>
    </div>


<div  class="container-fluid mx-auto my-auto table-responsive">

    <div style="background-color: white;" class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-title">
                    Buscar por nombre de afiliado, médico, especialidad, etc.
                    <input wire:model="search" class="form-control form-control-sm" placeholder="Buscar por nombre de afiliado">
                </div>
            </div>
        </div>

    <table style="background-color: white; border-radius: 12px" class="table table-bordered table-hover">
        <thead style="background-color: #DFFCFF; font-size: 12px">
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Fecha de carga</th>
            <th scope="col">Afiliado</th>
            <th scope="col">Nro Afiliado</th>
            <th scope="col">Clínica</th>
            <th scope="col">Edad</th>
            <th scope="col">Nro. Solicitud</th>
            <th scope="col">Médico</th>
            <th scope="col">Estado paciente</th>
            <th scope="col">Estado solicitud</th>
            <th scope="col">Fecha cirugía</th>
            <th scope="col">Sufrió accidente</th>
            <th scope="col">Necesidad</th>
            <th scope="col">Grupo artículos</th>
            <th scope="col">Días transcurridos</th>
            <th scope="col">Fecha expiración</th>
            <th scope="col">Obra social</th>
            <th scope="col">Acción</th>
        </tr>
        </thead>
        <tbody>
        @foreach($entrantes as $ent)
            @php
                $rowColor = '';
                if($ent->necesidad == 1){
                    $rowColor = '#FFBFB6';
                }
                if($ent->necesidad == 2){
                    $rowColor = '#FEFFE7';
                }
                if($ent->necesidad == 3){
                    $rowColor = '#C7FFC0';
                }
             @endphp
        <tr class="text-sm" style="background-color: {{$rowColor}}">
            <td>{{$ent->id}}</td>
            <td>{{$ent->created_at}}</td>
            <td>{{$ent->afiliados_id}}</td>
            <td>{{$ent->nroAfiliado}}</td>
            <td>{{$ent->clinicas->nombre}}</td>
            <td>{{ $ent->edad }}</td>
            <td>{{ $ent->nrosolicitud }}</td>
            <td>{{ $ent->medicos->nombremedico }}</td>
            <td>{{ $ent->estado_paciente->estado }}</td>
            <td>{{ $ent->estado_solicitud_id }}</td>
            <td>{{ $ent->fecha_cirugia }}</td>
            <td>{{ $ent->accidente }}</td>
            <td>{{ $ent->nec->necesidad }}</td>
            <td>{{ $ent->grupo_articulos }}</td>
            <td>3</td>
            <td>{{ $ent->fecha_expiracion }}</td>
            <td>APOS</td>
            <td>
                <!-- Botón para cotizar solicitud -->
                <button class="btn btn-xs btn-success">
                    <i class="ion ion-bag"></i> Cotizar solicitud
                </button>

                <!-- Botón para rechazar solicitud -->
                <button class="btn btn-xs btn-danger" wire:click="openModal({{$ent->id}})">
                    <i class="ion ion-android-cancel"></i> Rechazar solicitud
                </button>

                <!-- Botón para autorizar solicitud -->
                <button class="btn btn-xs btn-success" wire:click="openModal({{$ent->id}})">
                    <i class="ion ion-checkmark"></i> Autorizar solicitud
                </button>

                <!-- Botón para ver solicitud -->
                <button class="btn btn-xs btn-info">
                    <i class="ion ion-eye"></i> Ver solicitud
                </button>

                <!-- Botón para editar -->
                <button class="btn btn-xs btn-warning">
                    <i class="ion ion-edit"></i> Editar
                </button>

                <!-- Botón para eliminar -->
                <button class="btn btn-xs btn-danger">
                    <i class="ion ion-trash-b"></i> Eliminar
                </button>
            </td>

        </tr>

        @endforeach

        </tbody>
    </table>

    <nav aria-label="Páginas">
        <ul class="pagination pagination-sm">
            @foreach ($entrantes->links()->elements[0] as $page => $url)
                <li class="page-item {{ $page == $entrantes->currentPage() ? 'active' : '' }}">
                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                </li>
            @endforeach
        </ul>

    </nav>
</div>
