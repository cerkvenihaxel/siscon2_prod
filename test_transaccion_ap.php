<!DOCTYPE html>
<html>
<head>
    <title>Test - Transacción AP</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>Test - Transacción AP (Consumo de prestaciones)</h3>
                </div>
                <div class="card-body">
                    <p><strong>✅ Implementación completada:</strong></p>
                    <ul>
                        <li>✅ Controlador: <code>TransaccionApController</code></li>
                        <li>✅ Vista: <code>transaccion_ap/index.blade.php</code></li>
                        <li>✅ Rutas con middleware CBBackend</li>
                        <li>✅ Métodos en UnionPersonalSoapService</li>
                        <li>✅ Modelo UpConsumo actualizado</li>
                    </ul>
                    
                    <hr>
                    
                    <h5>Funcionalidades implementadas:</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h6>1. Consulta Rápida de Elegibilidad (ELG)</h6>
                                </div>
                                <div class="card-body">
                                    <ul class="small">
                                        <li>Consulta en tiempo real con jQuery</li>
                                        <li>Validación de afiliado</li>
                                        <li>Respuesta inmediata OK/NO</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h6>2. Transacción AP</h6>
                                </div>
                                <div class="card-body">
                                    <ul class="small">
                                        <li>Envío de XML según especificación</li>
                                        <li>Manejo de respuestas OK/NO</li>
                                        <li>Popup de error si STATUS=NO</li>
                                        <li>Carga de medicamentos si STATUS=OK</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h5>Carga de Medicamentos (Lazy Loading):</h5>
                    <div class="alert alert-info">
                        <ul class="mb-0">
                            <li>Búsqueda en tabla <code>articulosZafiro</code></li>
                            <li>Filtro por <code>nro_registro_alfabeta</code> = código prestación</li>
                            <li>Select2 con búsqueda dinámica</li>
                            <li>Guardado en tabla <code>up_consumos</code></li>
                        </ul>
                    </div>
                    
                    <hr>
                    
                    <div class="text-center">
                        <a href="/admin/transaccion-ap" class="btn btn-primary btn-lg">
                            🚀 Acceder a Transacción AP
                        </a>
                    </div>
                    
                    <hr>
                    
                    <h6>Rutas implementadas:</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Método</th>
                                    <th>Ruta</th>
                                    <th>Descripción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="badge badge-success">GET</span></td>
                                    <td>/admin/transaccion-ap</td>
                                    <td>Vista principal</td>
                                </tr>
                                <tr>
                                    <td><span class="badge badge-warning">POST</span></td>
                                    <td>/transaccion-ap/elegibilidad</td>
                                    <td>Consulta ELG</td>
                                </tr>
                                <tr>
                                    <td><span class="badge badge-warning">POST</span></td>
                                    <td>/transaccion-ap/procesar</td>
                                    <td>Transacción AP</td>
                                </tr>
                                <tr>
                                    <td><span class="badge badge-success">GET</span></td>
                                    <td>/transaccion-ap/buscar-articulos</td>
                                    <td>Lazy loading medicamentos</td>
                                </tr>
                                <tr>
                                    <td><span class="badge badge-warning">POST</span></td>
                                    <td>/transaccion-ap/guardar-consumo</td>
                                    <td>Guardar en up_consumos</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
