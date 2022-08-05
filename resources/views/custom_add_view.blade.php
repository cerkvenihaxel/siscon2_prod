  @extends('crudbooster::admin_template')
  @section('content')

  <!DOCTYPE html>
<html lang="es">
<body>
<div class="container">
    <div class="row">
        <div class="col-md-12">

        @if(session('status'))
        <h6 class="alert alert-success">{{ session('status') }}</h6>
        @endif
            

                <div class="card-body">



                <table class="table table-borderd table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>ID</th>
                                <th>Nombre afiliado</th>
                                <th>Clínica</th>
                                <th>Edad</th>
                                <th>Teléfono afiliado</th>
                                <th>Estado paciente</th>
                                <th>Estado solicitud</th>
                                <th>Fecha cirugía</th>
                                <th>Médico solicitante</th>
                                <th>Teléfono médico</th>
                                <th>Número solicitud</th>
                             
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($visitor as $item)
                            <tr key={{$item->$id}}>
                                <td><input class="form-check-input" type="checkbox" value="" id="flexCheckDefault"></td>
                                <td>{{ $item->id}}</td>
                                <td>{{ $item->afiliados_id}}</td>
                                <td>{{ $item->clinicas_id}}</td>
                                <td>{{ $item->edad}}</td>
                                <td>{{ $item->tel_afiliado}}</td>
                                <td>{{ $item->estado_paciente_id}}</td>
                                <td>{{ $item->estado_solicitud_id}}</td>
                                <td>{{ $item->fecha_cirugia}}</td>
                                <td>{{ $item->medicos_id}}</td>
                                <td>{{ $item->tel_medico}}</td>
                                <td>{{ $item->nrosolicitud}}</td>
                                
                            </tr>
                            @endforeach
                        </tbody>
                </table>


                <!-- Modal -->
           
            </div>
                </div>
            </div>

            <div class='panel panel-default'>
              <div class='panel-heading'>Agregar Cotización</div>
              <div class='panel-body'>
                <form method='post' action='{{CRUDBooster::mainpath('add-save')}}'>
                  <div class='form-group'>
                    <label>Nombre y apellido afiliado</label>
                    <input type='text' name='label1' required class='form-control'/>

                    <label>Clínica</label>
                    <input type='text' name='label1' required class='form-control'/>

                    <label>Edad</label>
                    <input type='text' name='label1' required class='form-control'/>

                    <label>Teléfono afiliado</label>
                    <input type='text' name='label1' required class='form-control'/>

                    <label>Estado paciente</label>
                    <input type='text' name='label1' required class='form-control'/>

                    <label>Estado solicitud</label>
                    <input type='text' name='label1' required class='form-control'/>

                    <label>Fecha cirugía</label>
                    <input type='text' name='label1' required class='form-control'/>

                    <label>Médico solicitante</label>
                    <input type='text' name='label1' required class='form-control'/>

                    <label>Teléfono médico</label>
                    <input type='text' name='label1' required class='form-control'/>

                    <label>Número solicitud</label>
                    <input type='text' name='label1' required class='form-control'/>


                  </div>
                   
                  <!-- etc .... -->
                  
                </form>
              </div>
              <div class='panel-footer'>
                <input type='submit' class='btn btn-primary' value='Save changes'/>
              </div>
            </div>
            


        </div>
    </div>
</div>

 <!-- Your html goes here -->

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.5/dist/umd/popper.min.js" integrity="sha384-Xe+8cL9oJa6tN/veChSP7q+mnSPaj5Bcu9mPX5F5xIGE0DVittaqT5lorf0EI7Vk" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-kjU+l4N0Yf4ZOJErLsIcvOU2qSb74wXpOhqTvwVx3OElZRweTnQ6d31fXEoRD1Jy" crossorigin="anonymous"></script>
</body>
</html>
@endsection