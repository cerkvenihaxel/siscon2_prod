
                    <form action="{{ route('afiliadosarticulos.update', $afiliadoArticulo->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="afiliado">Afiliado</label>
                            <input type="text" class="form-control form-s" id="afiliado" name="afiliado" value="{{ $afiliadoArticulo->nroAfiliado }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="articulos">Medicamento</label>
                            <input type="text" class="form-control form-s" id="articulos" name="afiliado" value="{{ $afiliadoArticulo->des_articulo }} | {{ $afiliadoArticulo->presentacion }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="cantidad">Cantidad</label>
                            <input type="number" class="form-control" id="cantidad" name="cantidad" value="{{ $afiliadoArticulo->cantidad }}">
                        </div>

                        <div class="form-group">
                            <label for="patologias">Patologías</label>
                            <input type="text" class="form-control form-s" id="afiliado" name="afiliado" value="{{ DB::table('patologias')->where('id', $afiliadoArticulo->patologias)->value('nombre') }}" readonly>

                        </div>

                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <a href="{{ route('afiliados_articulos.index') }}" class="btn btn-secondary">Cancelar</a>
                    </form>

