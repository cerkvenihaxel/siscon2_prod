@foreach($elementos as $index => $elemento)
    <div class="modal fade" id="modal-editar-{{ $index }}" tabindex="-1" role="dialog" aria-labelledby="modal-editar-label-{{ $index }}" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('generar-pedido.editar-articulo', ['index' => $index]) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-editar-label-{{ $index }}">Editar Artículo</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="cantidad">Laboratorio</label>
                            <input type="text" name="laboratorio" class="form-control" value="{{ $elemento['laboratorio'] }}" required>
                            <label for="cantidad">Descuento:</label>
                            <input type="text" name="descuento" class="form-control" value="{{ $elemento['descuento'] }}" required>
                            <label for="cantidad">Cantidad:</label>
                            <input type="number" name="cantidad" class="form-control" value="{{ $elemento['cantidad'] }}" required>
                            <input type="hidden" name="index" value="{{ $index }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach




