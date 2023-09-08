<!-- Modal para agregar artículos -->

    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-pedido-label">Agregar Artículos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <form action="{{ route('generar-pedido.store')}}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="articulo">Buscar Artículo:</label>
                        <input type="text" id="articulo-search" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="descripcion">Laboratorio</label>
                        <input type="text" name="descripcion" class="form-control" value="NOVONDRISK">
                    </div>
                    <div class="form-group">
                        <label for="monodroga">Monodroga</label>
                        <input type="text" name="monodroga" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="cantidad">Cantidad</label>
                        <input type="number" name="cantidad" class="form-control" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Agregar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

