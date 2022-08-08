  @extends('crudbooster::admin_template')
  @section('content')



  <!DOCTYPE html>
  <html lang="es">


  <script type="text/javascript">
      //POR FIS REVEER ESTO PARA QUE FUNCIONE EL AUTOCOMPLETE

  </script>

  <body>
      <div class="container">
          <div class="row">
              <div class="col-md-12">

                  @if(session('status'))
                  <h6 class="alert alert-success">{{ session('status') }}</h6>
                  @endif


                  <div class="card-body">



                      <table class="table table-borderd table-striped">


                          <tbody>
                              <div class="box-body" id="parent-form-area">

                                  <div class="table-responsive">
                                      <table id="table-detail" class="table table-striped">

                                          {{-- Acá comienza el view de la solicitud pasando por referencia el ID de la Solicitud entrante --}}

                                          <tbody>
                                              @foreach($visitor as $item)
                                              <tr key={{$item->$id}}>
                                                  <td>Nombre y Apellido Afiliado</td>
                                                  <td id="afiliado_id">{{ $item->afiliados_id}}</td>
                                              </tr>



                                              <tr>
                                                  <td>Clínica</td>
                                                  <td id="clinicaName">{{ $item->clinicas_id}}</td>
                                              </tr>



                                              <tr>
                                                  <td>Edad</td>
                                                  <td id="edad">{{ $item->edad}}</td>
                                              </tr>



                                              <tr>
                                                  <td>Telefono afiliado</td>
                                                  <td id="telAfiliado">{{ $item->tel_afiliado}}</td>
                                              </tr>



                                              <tr>
                                                  <td>Estado Paciente</td>
                                                  <td id="estadoPaciente">{{ $item->estado_paciente_id}}</td>
                                              </tr>



                                              <tr>
                                                  <td>Estado Solicitud</td>
                                                  <td id="estadoSolicitud">{{ $item->estado_solicitud_id}}</td>
                                              </tr>



                                              <tr>
                                                  <td>Fecha Cirugia</td>
                                                  <td id="fechaCirugia">{{ $item->fecha_cirugia}}</td>

                                              </tr>



                                              <tr>
                                                  <td>Médico Solicitante</td>
                                                  <td id="medicoSolicitante">{{ $item->medicos_id}}</td>
                                              </tr>



                                              <tr>
                                                  <td>Teléfono médico</td>
                                                  <td id="telMedico">{{ $item->tel_medico}}</td>
                                              </tr>



                                              <tr>
                                                  <td>Número de Solicitud</td>
                                                  <td id="nroSolicitud">{{ $item->nrosolicitud}}</td>
                                              </tr>



                                              <tr>
                                                  <td>Necesidad</td>
                                                  <td id="necesidad">{{ $item->necesidad}}</td>
                                              </tr>



                                              <tr>
                                                  <td>Grupo articulos</td>
                                                  <td>1</td>
                                              </tr>

                                              {{-- Termina el form de solicitantes antes de los artículos --}}


                                              <tr>
                                                  <td colspan="2">

                                                      <div class="panel panel-default">
                                                          <div class="panel-heading">
                                                              <i class="fa fa-bars"></i> Detalles de la solicitud
                                                          </div>
                                                          <div class="panel-body">
                                                              <table id="table-entrantes_detail"
                                                                  class="table table-striped table-bordered">
                                                                  <thead>
                                                                      <tr>
                                                                          <th>Artículos solicitados</th>
                                                                          <th>Cantidad</th>

                                                                      </tr>
                                                                  </thead>
                                                                  <tbody>

                                                                      <tr>
                                                                          <td class="articulos_id">
                                                                              <span class="td-label">PLACA ANATOMICA
                                                                              </span><input type="hidden"
                                                                                  name="entrantes_detail-articulos_id[]"
                                                                                  value="915"> </td>
                                                                          <td class="cantidad">
                                                                              <span class="td-label">1</span><input
                                                                                  type="hidden"
                                                                                  name="entrantes_detail-cantidad[]"
                                                                                  value="1"> </td>

                                                                      </tr>

                                                                      <tr>
                                                                          <td class="articulos_id">
                                                                              <span class="td-label">STENT</span><input
                                                                                  type="hidden"
                                                                                  name="entrantes_detail-articulos_id[]"
                                                                                  value="913"> </td>
                                                                          <td class="cantidad">
                                                                              <span class="td-label">2</span><input
                                                                                  type="hidden"
                                                                                  name="entrantes_detail-cantidad[]"
                                                                                  value="2"> </td>

                                                                      </tr>


                                                                  </tbody>
                                                              </table>


                                                          </div>
                                                      </div>


                                                  </td>
                                              </tr>




                                              <tr>
                                                  <td>Observaciones</td>
                                                  <td>Por favor es el gobernador, debe ser de manera urgente</td>
                                              </tr>

                                              @endforeach
                                              {{-- Acá termina el view detail del formulario pasando por referencia el ID de entrantes --}}
                                          </tbody>
                                      </table>
                                  </div>
                              </div>
                              {{-- @foreach($visitor as $item)
                            <tr key={{$item->$id}}>
                              <td><input class="form-check-input" type="checkbox" value="" id="flexCheckDefault"></td>
                              <td>{{ $item->id}}</td>
                              <td id="afiliado_id">{{ $item->afiliados_id}}</td>
                              <td id="clinicaName">{{ $item->clinicas_id}}</td>
                              <td id="edad">{{ $item->edad}}</td>
                              <td id="telAfiliado">{{ $item->tel_afiliado}}</td>
                              <td id="estadoPaciente">{{ $item->estado_paciente_id}}</td>
                              <td id="estadoSolicitud">{{ $item->estado_solicitud_id}}</td>
                              <td id="fechaCirugia">{{ $item->fecha_cirugia}}</td>
                              <td id="medicoSolicitante">{{ $item->medicos_id}}</td>
                              <td id="telMedico">{{ $item->tel_medico}}</td>
                              <td id="nroSolicitud">{{ $item->nrosolicitud}}</td>


                              </tr>
                              @endforeach --}}


                          </tbody>
                      </table>

                      <div class='panel panel-default'>
                          <div class='panel-heading'>Agregar Cotización</div>
                          <div class='panel-body'>
                              <form method='post' action='{{CRUDBooster::mainpath('add-save')}}'>
                                  <div class='form-group'>
                                      <label class="control-label col-sm-2">Nombre y apellido afiliado</label>
                                  
                                      <input type='text' id='afiliado' required class='form-control' readonly="" />

                                      <label>Clínica</label>
                                      <input type='text' id="clinica" required class='form-control' readonly="" />

                                      <label>Edad</label>
                                      <input type='text' id='edadInput' required class='form-control' readonly="" />

                                      <label>Teléfono afiliado</label>
                                      <input type='text' id='telAfiliadoInput' required class='form-control'
                                          readonly="" />

                                      <label>Estado paciente</label>
                                      <input type='text' id='estadoPacienteInput' required class='form-control'
                                          readonly="" />

                                      <label>Estado solicitud</label>
                                      <input type='text' id='estadoSolicitudInput' required class='form-control'
                                          readonly="" />

                                      <label>Fecha cirugía</label>
                                      <input type='text' id='fechaCirugiaInput' required class='form-control'
                                          readonly="" />

                                      <label>Médico solicitante</label>
                                      <input type='text' id='medicoSolicitanteInput' required class='form-control'
                                          readonly="" />

                                      <label>Teléfono médico</label>
                                      <input type='text' id='telMedicoInput' required class='form-control'
                                          readonly="" />

                                      <label>Número solicitud</label>
                                      <input type='text' id='nroSolicitudInput' required class='form-control'
                                          readonly="" />


                                  </div>

                                  <!-- Modal -->

                                  <div class="panel-body">

                                      <div class="row">
                                          <div class="col-sm-10">
                                              <div class="panel panel-default">
                                                  <div class="panel-heading"><i class="fa fa-pencil-square-o"></i>
                                                      Formulario</div>
                                                  <div class="panel-body child-form-area">
                                                      <div class="form-group">
                                                          <label class="control-label col-sm-2">Artículos
                                                          </label>
                                                          <div class="col-sm-10">

                                                              <div id="detallesdelasolicitudarticulos_id"
                                                                  class="input-group">
                                                                  <input type="hidden" class="input-id">
                                                                  <input type="text" class="form-control input-label "
                                                                      readonly="">
                                                                  <span class="input-group-btn">
                                                                      <button class="btn btn-primary"
                                                                          onclick="showModaldetallesdelasolicitudarticulos_id()"
                                                                          type="button"><i class="fa fa-search"></i>
                                                                          Escoger Dato</button>
                                                                  </span>
                                                              </div><!-- /input-group -->


                                                              <div id="modal-datamodal-detallesdelasolicitudarticulos_id"
                                                                  class="modal" tabindex="-1" role="dialog">
                                                                  <div class="modal-dialog modal-lg " role="document">
                                                                      <div class="modal-content">
                                                                          <div class="modal-header">
                                                                              <button type="button" class="close"
                                                                                  data-dismiss="modal"
                                                                                  aria-label="Close"><span
                                                                                      aria-hidden="true">×</span></button>
                                                                              <h4 class="modal-title"><i
                                                                                      class="fa fa-search"></i> Escoger
                                                                                  Dato Artículos
                                                                              </h4>
                                                                          </div>
                                                                          <div class="modal-body">
                                                                              <iframe
                                                                                  id="iframe-modal-detallesdelasolicitudarticulos_id"
                                                                                  style="border:0;height:430px;width: 100%"
                                                                                  src=""></iframe>
                                                                          </div>

                                                                      </div><!-- /.modal-content -->
                                                                  </div><!-- /.modal-dialog -->
                                                              </div><!-- /.modal -->


                                                          </div>
                                                      </div>


                                                      <div class="form-group">
                                                          <label class="control-label col-sm-2">Cantidad
                                                          </label>
                                                          <div class="col-sm-10">
                                                              <input id="detallesdelasolicitudcantidad" type="number"
                                                                  name="child-cantidad" class="form-control ">

                                                          </div>
                                                      </div>


                                                      <div class="form-group">
                                                          <label class="control-label col-sm-2">Garantía (meses)
                                                          </label>
                                                          <div class="col-sm-10">
                                                              <input id="detallesdelasolicitudgarantia" type="number"
                                                                  name="child-garantia" class="form-control ">

                                                          </div>
                                                      </div>


                                                      <div class="form-group">
                                                          <label class="control-label col-sm-2">Precio
                                                          </label>
                                                          <div class="col-sm-10">
                                                              <input id="detallesdelasolicitudprecio" type="number"
                                                                  name="child-precio" class="form-control ">

                                                          </div>
                                                      </div>


                                                      <div class="form-group">
                                                          <label class="control-label col-sm-2">Procendencia
                                                          </label>
                                                          <div class="col-sm-10">


                                                              <select id="detallesdelasolicitudprocedencias_id"
                                                                  name="child-procedencias_id"
                                                                  class="form-control select2  select2-hidden-accessible"
                                                                  tabindex="-1" aria-hidden="true">
                                                                
                                                                  <option value="1">Importado</option>
                                                                  <option value="2">Nacional</option>
                                                              </select>
                                                              <span
                                                                  class="select2 select2-container select2-container--default"
                                                                  dir="ltr" style="width: 741px;">
                                                                  <span class="selection"><span
                                                                          class="select2-selection select2-selection--single"
                                                                          role="combobox" aria-haspopup="true"
                                                                          aria-expanded="false" tabindex="0"
                                                                          aria-labelledby="select2-detallesdelasolicitudprocedencias_id-container">
                                                                          <span class="select2-selection__arrow"
                                                                              role="presentation"><b
                                                                                  role="presentation"></b></span>
                                                                      </span></span>
                                                                  <span class="dropdown-wrapper"
                                                                      aria-hidden="true"></span></span>

                                                          </div>
                                                      </div>



                                                  </div>
                                                  <div class="panel-footer" align="right">
                                                      <input type="button" class="btn btn-default"
                                                          id="btn-reset-form-detallesdelasolicitud"
                                                          onclick="resetFormdetallesdelasolicitud()" value="Resetear">
                                                      <input type="button" id="btn-add-table-detallesdelasolicitud"
                                                          class="btn btn-primary"
                                                          onclick="addToTabledetallesdelasolicitud()"
                                                          value="Agregar a la Tabla">
                                                  </div>
                                              </div>
                                          </div>
                                      </div>

                                      <div class="panel panel-default">
                                          <div class="panel-heading">
                                              <i class="fa fa-table"></i> Tabla de Detalles
                                          </div>
                                          <div class="panel-body no-padding table-responsive"
                                              style="max-height: 400px;overflow: auto;">
                                              <table id="table-detallesdelasolicitud"
                                                  class="table table-striped table-bordered">
                                                  <thead>
                                                      <tr>
                                                          <th>Artículos</th>
                                                          <th>Cantidad</th>
                                                          <th>Garantía (meses)</th>
                                                          <th>Precio</th>
                                                          <th>Procendencia</th>
                                                          <th width="90px">Acción</th>
                                                      </tr>
                                                  </thead>
                                                  <tbody>


                                                      <tr class="trNull">
                                                          <td colspan="6" align="center">No tenemos datos disponibles
                                                          </td>
                                                      </tr>
                                                  </tbody>
                                              </table>
                                          </div>
                                      </div>
                                  </div>



                          </div>
                      </div>
                  </div>



                  <script type="text/javascript">
                      var nombreAfiliado = document.getElementById('afiliado_id');
                      console.log(nombreAfiliado.innerHTML);
                      var clinicaName = document.getElementById('clinicaName');
                      console.log(clinicaName.innerHTML);

                      var edad = document.getElementById('edad');
                      console.log(edad.innerHTML);
                      var telAfiliado = document.getElementById('telAfiliado');
                      console.log(telAfiliado.innerHTML);
                      var estadoPaciente = document.getElementById('estadoPaciente');
                      console.log(estadoPaciente.innerHTML);
                      var estadoSolicitud = document.getElementById('estadoSolicitud');
                      console.log(estadoSolicitud.innerHTML);
                      var fechaCirugia = document.getElementById('fechaCirugia');
                      console.log(fechaCirugia.innerHTML);
                      var medicoSolicitante = document.getElementById('medicoSolicitante');
                      console.log(medicoSolicitante.innerHTML);
                      var telMedico = document.getElementById('telMedico');
                      console.log(telMedico.innerHTML);
                      var nroSolicitud = document.getElementById('nroSolicitud');
                      console.log(nroSolicitud.innerHTML);


                      document.getElementById('afiliado').value = nombreAfiliado.innerHTML;
                      document.getElementById('clinica').value = clinicaName.innerHTML;
                      document.getElementById('edadInput').value = edad.innerHTML;
                      document.getElementById('telAfiliadoInput').value = telAfiliado.innerHTML;
                      document.getElementById('estadoPacienteInput').value = estadoPaciente.innerHTML;
                      document.getElementById('estadoSolicitudInput').value = estadoSolicitud.innerHTML;
                      document.getElementById('fechaCirugiaInput').value = fechaCirugia.innerHTML;
                      document.getElementById('medicoSolicitanteInput').value = medicoSolicitante.innerHTML;
                      document.getElementById('telMedicoInput').value = telMedico.innerHTML;
                      document.getElementById('nroSolicitudInput').value = nroSolicitud.innerHTML;

                  </script>
                  <!-- etc .... -->

                  </form>

                  {{-- Comienza el form de llenado --}}



              </div>

              <div class='panel-footer'>
                  <input type='submit' class='btn btn-primary' value='Save changes' />
              </div>
          </div>



      </div>
      </div>
      </div>

      <!-- Your html goes here -->



      <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.5/dist/umd/popper.min.js"
          integrity="sha384-Xe+8cL9oJa6tN/veChSP7q+mnSPaj5Bcu9mPX5F5xIGE0DVittaqT5lorf0EI7Vk" crossorigin="anonymous">
      </script>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.min.js"
          integrity="sha384-kjU+l4N0Yf4ZOJErLsIcvOU2qSb74wXpOhqTvwVx3OElZRweTnQ6d31fXEoRD1Jy" crossorigin="anonymous">
      </script>
  </body>

  </html>
  @endsection
