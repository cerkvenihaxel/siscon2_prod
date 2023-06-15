<div class="modal-body">
    <p><strong>Nro Afiliado:</strong> {{ $afiliado->nroAfiliado }}</p>
    <p><strong>Nombre Afiliado:</strong> {{ $afiliado->nombre }}</p>
    <p><strong>Articulo:</strong> {{ $afiliado->articulo }}</p>
    <p><strong>Des Articulo:</strong> {{ $afiliado->des_articulo }}</p>
    <p><strong>Presentacion:</strong> {{ $afiliado->presentacion }}</p>
    <p><strong>Cantidad:</strong> {{ $afiliado->cantidad }}</p>
    <p><strong>Patologias:</strong> {{ DB::table('patologias')->where('id', $afiliado->patologias)->value('nombre') }}</p>
</div>
