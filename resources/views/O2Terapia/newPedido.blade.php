@extends('crudbooster::admin_template')
@section('content')

    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Pedido de Oxigenoterapia</title>
    <style>
        .search-section, .result-section {
            margin-top: 20px;
        }
        .result-section {
            display: none;
        }

        .container {
            background-color: white;
            padding: 1rem;
            border-radius: 2rem;
        }
    </style>
</head>
<body>
<div class="container">
    <h1 class="my-4">Crear Pedido de Oxigenoterapia</h1>

    <div class="search-section">
        <form id="search-form" class="form-inline">
            <label for="dni" class="sr-only">Inserte DNI del afiliado</label>
            <input type="text" id="dni" class="form-control mb-2 mr-sm-2" placeholder="Inserte DNI del afiliado">

            <button type="button" class="btn btn-primary mb-2" onclick="searchAfiliado()" style="margin: 2rem">
                <i class="fas fa-search"></i> Buscar
            </button>
        </form>
    </div>


    <div class="result-section" id="result-section">
        <h3>Datos del Afiliado:</h3>
        <p><strong>Nombre afiliado:</strong> <span id="nombre-afiliado"></span></p>
        <p><strong>DNI afiliado:</strong> <span id="dni-afiliado"></span></p>
        <p><strong>Número afiliado:</strong> <span id="numero-afiliado"></span></p>
        <p><strong>Localidad:</strong> <span id="localidad-afiliado"></span></p>
        <p><strong>Fecha de Nacimiento:</strong> <span id="fecha-nacimiento-afiliado"></span></p>

        <button class="btn btn-success" onclick="createPedido()">Crear pedido</button>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/a076d05399.js"></script>
<script>
    let afiliadoId;

    function searchAfiliado() {
        const dni = $('#dni').val();
        // Realiza la petición POST a la API para obtener los datos del afiliado
        $.ajax({
            url: 'http://siscon.info/api/afiliado_api/',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ dni: dni }),
            success: function(response) {
                if (response.data && response.data.length > 0) {
                    const afiliado = response.data[0];

                    $('#nombre-afiliado').text(afiliado.apeynombres);
                    $('#dni-afiliado').text(afiliado.documento);
                    $('#numero-afiliado').text(afiliado.nroAfiliado);
                    $('#localidad-afiliado').text(afiliado.localidad);
                    $('#fecha-nacimiento-afiliado').text(afiliado.fecha_nacimiento);

                    afiliadoId = afiliado.id; // Guardar el ID del afiliado para la redirección

                    // Muestra la sección de resultados
                    $('#result-section').show();
                } else {
                    alert('Afiliado no encontrado');
                }
            },
            error: function() {
                alert('Error al buscar el afiliado');
            }
        });

    }

    function createPedido() {
        if (afiliadoId) {
            window.location.href = `pedidos_o2/add??id=${afiliadoId}`;
        } else {
            alert('Debe buscar un afiliado primero');
        }
    }
</script>
</body>
</html>


@endsection
