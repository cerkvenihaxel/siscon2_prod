<div class="modal-body">
    <p><strong>Nro Afiliado:</strong> {{ $afiliado->nro_afiliado }}</p>
    <p><strong>Nombre Afiliado:</strong> {{ $afiliado->nombre }}</p>
    <p><strong>Medicación:</strong> {{ $afiliado->des_articulo }} | {{ $afiliado->presentacion }}</p>
    <p><strong>Monodroga:</strong> {{ $monodroga }}</p>
    <p><strong>Cantidad:</strong> {{ $afiliado->cantidad }}</p>
    <p><strong>Patologias:</strong> {{ DB::table('patologias')->where('id', $afiliado->patologias)->value('nombre') }}</p>
</div>
