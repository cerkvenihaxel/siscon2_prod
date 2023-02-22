@php
    $solEntrantes= DB::table('entrantes')->where('estado_solicitud_id', 1)->count();
    $solEntrantes = $solEntrantes-13;
    $solAuditada= DB::table('entrantes')->where('estado_solicitud_id', 8)->count();
    $solCotizada = DB::table('cotizaciones')->count();
    $solAdjudicada = DB::table('adjudicaciones')->count();
    $solNoAdjudicada = $solCotizada - $solAdjudicada;
    $solTotal = $solCotizada + $solAdjudicada + $solNoAdjudicada;

    $now = date('Y-m-d');
    $vencimiento= date('Y-m-d', strtotime("+1 days"));

    $start=date('d-m-Y');
    $end=date('d-m-Y', strtotime("+1 days"));
    
    $solVencidas= DB::table('entrantes')->where('estado_solicitud_id', 1)->where('fecha_expiracion', '<=', $now)->count();
    $solProximasVencer = DB::table('entrantes')->where('estado_solicitud_id', 1)->where('fecha_expiracion',  $vencimiento)->count();
    //Porcentajes

    $percentSolCotizada= $solCotizada / $solTotal * 100;
    $percentSolAdjudicada = $solAdjudicada / $solTotal * 100;
    $percentSolNoAdjudicada = $solNoAdjudicada / $solTotal * 100; 


    $solGlobal = DB::table('cotizaciones')->where('proveedor', 'LIKE', '%global%')->count();
    $solMedKit = DB::table('cotizaciones')->where('proveedor', 'LIKE', '%medkit%')->count();
    $solQRA = DB::table('cotizaciones')->where('proveedor', 'LIKE', '%qra%')->count();
    $solMiguelAngel = DB::table('cotizaciones')->where('proveedor', 'LIKE', '%miguel angel%')->count();

    $auditadasSol = DB::table('entrantes')->where('estado_solicitud_id', 8)->get('nrosolicitud');

    foreach  ($auditadasSol as $aS) {

        $auditadasSol = $aS->nrosolicitud;
        $pendientesSol = DB::table('cotizaciones')->where('nrosolicitud', $auditadasSol)->where('proveedor', 'NOT LIKE', "%global%")->count();
    }

    $solTotales = DB::table('entrantes')->count();

    $ultimasSolicitudes = DB::table('entrantes')->orderBy('id', 'desc')->limit(5)->get();
@endphp


<!DOCTYPE html>

<html lang="es" class="light">
    <!-- BEGIN: Head -->
    <head>
        <meta charset="utf-8">
        <link href="dist/images/logo.svg" rel="shortcut icon">
        <meta name="keywords" content="admin template, Icewall Admin Template, dashboard template, flat admin template, responsive admin template, web app">
        <title>SISCON V2</title>
        <!-- BEGIN: CSS Assets-->
        <link rel="stylesheet" href="dist/css/app.css" />
        <!-- END: CSS Assets-->
    </head>
    <!-- END: Head -->
    <body class="main">
       
        <!-- END: Mobile Menu -->

                <!-- BEGIN: Content -->
                <div class="content">
                    <div class="grid grid-cols-12 gap-6">
                        <div class="col-span-12 2xl:col-span-9">
                            <div class="grid grid-cols-12 gap-6">
                                <!-- BEGIN: General Report -->
                                
                                <div class="col-span-12 mt-8">
                                    <div class="intro-y flex items-center h-10">
                                        <h2 class="text-lg font-medium truncate mr-5">
                                            Escritorio
                                        </h2>
                                        <a href="" class="ml-auto flex items-center text-primary"> <i data-lucide="refresh-ccw" class="w-4 h-4 mr-3"></i> Recargar datos </a>
                                    </div>
                                    <div class="grid grid-cols-12 gap-6 mt-5">
                                        <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                                            <div class="report-box zoom-in">
                                                <div class="box p-5">
                                                    <div class="flex">
                                                        <i data-lucide="inbox" class="report-box__icon text-primary"></i> 
                                                        <div class="ml-auto">
                                                            <a href="/admin/entrantes?q=entrante"> 
                                                        </div>
                                                    </div>
                                                    <?php 

                                                    echo "<div class='text-3xl font-medium leading-8 mt-6'>".$solEntrantes."</div>"; ?>
                                                    <div class="text-base text-slate-500 mt-1">Solicitudes entrantes</div></a>
                                                </div>
                                            </div>
                                        </div>

                                        
                                        
                                        <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                                            <div class="report-box zoom-in">
                                                <div class="box p-5">
                                                    <div class="flex">
                                                        <i data-lucide="verified" class="report-box__icon text-pending"></i> 
                                                        <div class="ml-auto">
                                                            <a href="/admin/entrantes?q=AUDITADA+%28APROBADA%29"> 

                                                        </div>
                                                    </div>
                                                    <?php

                                                   echo "<div class='text-3xl font-medium leading-8 mt-6'>".$solAuditada."</div>"; ?>
                                                    <div class="text-base text-slate-500 mt-1">Solicitudes Auditadas Aprobadas</div></a>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                                            <div class="report-box zoom-in">
                                                <div class="box p-5">
                                                    <div class="flex">
                                                        <i data-lucide="dollar-sign" class="report-box__icon text-warning"></i> 
                                                        <div class="ml-auto">

                                                            <a href="/admin/cotizaciones19"> 

                                                        </div>
                                                    </div>
                                                    <?php
                                                    echo "<div class='text-3xl font-medium leading-8 mt-6'>".$solCotizada."</div>"; ?>
                                                    <div class="text-base text-slate-500 mt-1">Cotizaciones de solicitudes</div></a>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                                            <div class="report-box zoom-in">
                                                <div class="box p-5">
                                                    <div class="flex">
                                                        <i data-lucide="check-circle" class="report-box__icon text-success"></i> 
                                                        <div class="ml-auto">
                                                            <a href="/admin/adjudicaciones"> 

                                                        </div>
                                                    </div>
                                                    <?php
                                                    echo "<div class='text-3xl font-medium leading-8 mt-6'>".$solAdjudicada."</div>"; ?>
                                                    <div class="text-base text-slate-500 mt-1">Solicitudes Adjudicadas</div></a>
                                                </div>
                                            </div>
                                        </div>
<!--END  First line Cuadros -->

<!-- Segunda linea de cuadros -->
                                        <div class="col-span-12 mt-8">
                                            
                                            <div class="grid grid-cols-12 gap-6 mt-5">
                                                <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                                                    <div class="report-box zoom-in">
                                                        <div class="box p-5">
                                                            <div class="flex">
                                                                <i data-lucide="clipboard-x" class="report-box__icon text-primary"></i> 
                                                                <div class="ml-auto">
                                                                    <?php echo "<a href='/admin/entrantes?filter_column%5Bentrantes.created_at%5D%5Btype%5D=&filter_column%5Bentrantes.fecha_expiracion%5D%5Btype%5D=between&filter_column%5Bentrantes.fecha_expiracion%5D%5Bvalue%5D%5B%5D=01%2F01%2F2022&filter_column%5Bentrantes.fecha_expiracion%5D%5Bvalue%5D%5B%5D=".$start."&filter_column%5Bentrantes.fecha_expiracion%5D%5Bsorting%5D=&q=entrante&lasturl=http%3A%2F%2Flocalhost%3A8000%2Fadmin%2Fentrantes%3Fq%3Dentrante'>"; 
                                                                        ?>         
                                                             </div>
                                                            </div>
                                                            <?php 
        
                                                            echo "<div class='text-3xl font-medium leading-8 mt-6'>".$solVencidas."</div>"; ?>
                                                            <div class="text-base text-slate-500 mt-1">Solicitudes vencidas</div></a>
                                                        </div>
                                                    </div>
                                                </div>
        
                                                
                                                
                                                <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                                                    <div class="report-box zoom-in">
                                                        <div class="box p-5">
                                                            <div class="flex">
                                                                <i data-lucide="timer-reset" class="report-box__icon text-danger"></i> 
                                                                <div class="ml-auto">
                                                                    <?php echo "<a href='/admin/entrantes?filter_column%5Bentrantes.created_at%5D%5Btype%5D=&filter_column%5Bentrantes.fecha_expiracion%5D%5Btype%5D=between&filter_column%5Bentrantes.fecha_expiracion%5D%5Bvalue%5D%5B%5D=".$start."&filter_column%5Bentrantes.fecha_expiracion%5D%5Bvalue%5D%5B%5D=".$end."&filter_column%5Bentrantes.fecha_expiracion%5D%5Bsorting%5D=&q=entrante&lasturl=http%3A%2F%2Flocalhost%3A8000%2Fadmin%2Fentrantes%3Fq%3Dentrante'>"; 
                                                                        ?>
                                                                </div>
                                                            </div>
                                                            <?php
        
                                                           echo "<div class='text-3xl font-medium leading-8 mt-6'>".$solProximasVencer."</div>"; ?>
                                                            <div class="text-base text-slate-500 mt-1">Solicitudes Próximas a vencer</div></a>
                                                        </div>
                                                    </div>
                                                </div>

                                        <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                                            <div class="report-box zoom-in">
                                                <div class="box p-5">
                                                    <div class="flex">
                                                        <i data-lucide="clock-2" class="report-box__icon text-warning"></i> 
                                                        <div class="ml-auto">
                                                        </div>
                                                    </div>
                                                    <?php
                                                    echo "<div class='text-3xl font-medium leading-8 mt-6'>".$pendientesSol."</div>"; ?>
                                                    <div class="text-base text-slate-500 mt-1">Solicitudes pendientes por cotizar</div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                                            <div class="report-box zoom-in">
                                                <div class="box p-5">
                                                    <div class="flex">
                                                        <i data-lucide="equal" class="report-box__icon text-success"></i> 
                                                        <div class="ml-auto">
                                                        </div>
                                                    </div>
                                                    <?php
                                                    echo "<div class='text-3xl font-medium leading-8 mt-6'>".$solTotales."</div>"; ?>
                                                    <div class="text-base text-slate-500 mt-1">Solicitudes Totales</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Fin Segunda linea cuadros -->
                                <!-- END: General Report -->
                              
                                <!-- General Report 2 -->

                                


                                <!-- BEGIN: Weekly Top Seller -->
                                <div class="col-span-12 sm:col-span-6 lg:col-span-3 mt-8">
                                    <div class="intro-y flex items-center h-10">
                                        <h2 class="text-lg font-medium truncate mr-5">
                                            Solicitudes
                                        </h2>
                                    </div>
                                    <div class="intro-y box p-5 mt-5">
                                        <div class="mt-3">
                                            <div class="h-[213px]">
                                                <canvas id="report-pie-chart"></canvas>
                                                <input type="hidden" id="solCotizada" value="<?php echo $percentSolCotizada; ?>">
                                                <input type="hidden" id="solAdjudicada" value="<?php echo $percentSolAdjudicada; ?>">
                                                <input type="hidden" id="solNoAdjudicada" value="<?php echo $percentSolNoAdjudicada; ?>">
                                            </div>  
                                        </div>
                                        <div class="w-52 sm:w-auto mx-auto mt-8">
                                            <div class="flex items-center">
                                                <div class="w-2 h-2 bg-primary rounded-full mr-3"></div>

                                                <?php
                                               echo "<span class='truncate'>Solicitudes Cotizadas</span>";
                                               echo "<span class='font-medium ml-auto'>".sprintf("%0.2f", $percentSolCotizada)."</span>"; 
                                               ?>

                                            </div>
                                            <div class="flex items-center mt-4">

                                                <div class="w-2 h-2 bg-pending rounded-full mr-3"></div>

                                                <?php
                                               echo "<span class='truncate'>Solicitudes Adjudicadas</span>";
                                               echo "<span class='font-medium ml-auto'>".sprintf("%0.2f", $percentSolAdjudicada)."</span>"; 
                                               ?>
                                                
                                            </div>
                                            <div class="flex items-center mt-4">
                                                <div class="w-2 h-2 bg-warning rounded-full mr-3"></div>


                                                <?php
                                                echo "<span class='truncate'>Solicitudes No Adjudicadas</span>";
                                                echo "<span class='font-medium ml-auto'>".sprintf("%0.2f", $percentSolNoAdjudicada)."</span>"; 
                                                ?>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- END: Weekly Top Seller -->
                                <!-- BEGIN: Sales Report -->
                                <div class="col-span-12 sm:col-span-6 lg:col-span-3 mt-8">
                                    <div class="intro-y flex items-center h-10">
                                        <h2 class="text-lg font-medium truncate mr-5">
                                            Pedidos Médicos
                                        </h2>
                                    </div>
                                    <div class="intro-y box p-5 mt-5">
                                        <div class="mt-3">
                                            <div class="h-[213px]">
                                                <canvas id="report-donut-chart"></canvas>
                                            </div>
                                        </div>
                                        <div class="w-52 sm:w-auto mx-auto mt-8">
                                            <div class="flex items-center">
                                                <div class="w-2 h-2 bg-primary rounded-full mr-3"></div>
                                                <span class="truncate">Pedidos médicos</span> <span class="font-medium ml-auto">62%</span> 
                                            </div>
                                            <div class="flex items-center mt-4">
                                                <div class="w-2 h-2 bg-pending rounded-full mr-3"></div>
                                                <span class="truncate">Solicitudes Auditadas/Aprobadas</span> <span class="font-medium ml-auto">33%</span> 
                                            </div>
                                            <div class="flex items-center mt-4">
                                                <div class="w-2 h-2 bg-warning rounded-full mr-3"></div>
                                                <span class="truncate">Pedidos cotizados</span> <span class="font-medium ml-auto">10%</span> 
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- END: Sales Report -->
                              
                                <!-- BEGIN: Weekly Best Sellers -->
                                <div class="col-span-12 xl:col-span-4 mt-6">
                                    <div class="intro-y flex items-center h-10">
                                        <h2 class="text-lg font-medium truncate mr-5">
                                            Proveedores con mayor cotización
                                        </h2>
                                    </div>
                                    <div class="mt-5">
                                        <div class="intro-y">
                                            <div class="box px-4 py-4 mb-3 flex items-center zoom-in">
                                                <div class="w-10 h-10 flex-none image-fit rounded-md overflow-hidden">
                                                    <img alt="Midone - HTML Admin Template" src="dist/images/profile-7.jpg">
                                                </div>
                                                <div class="ml-4 mr-auto">
                                                    <div class="font-medium">Global Médica</div>
                                                </div>
                                                <div class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium">{{ $solGlobal }} Cotizaciones</div>
                                            </div>
                                        </div>
                                        <div class="intro-y">
                                            <div class="box px-4 py-4 mb-3 flex items-center zoom-in">
                                                <div class="w-10 h-10 flex-none image-fit rounded-md overflow-hidden">
                                                    <img alt="Midone - HTML Admin Template" src="dist/images/profile-4.jpg">
                                                </div>
                                                <div class="ml-4 mr-auto">
                                                    <div class="font-medium">QRA</div>
                                                </div>
                                                <div class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium">{{$solQRA}} cotizaciones</div>
                                            </div>
                                        </div>
                                        <div class="intro-y">
                                            <div class="box px-4 py-4 mb-3 flex items-center zoom-in">
                                                <div class="w-10 h-10 flex-none image-fit rounded-md overflow-hidden">
                                                    <img alt="Midone - HTML Admin Template" src="dist/images/profile-9.jpg">
                                                </div>
                                                <div class="ml-4 mr-auto">
                                                    <div class="font-medium">MedKit</div>
                                                </div>
                                                <div class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium">{{$solMedKit}} Cotizaciones</div>
                                            </div>
                                        </div>
                                        <div class="intro-y">
                                            <div class="box px-4 py-4 mb-3 flex items-center zoom-in">
                                                <div class="w-10 h-10 flex-none image-fit rounded-md overflow-hidden">
                                                    <img alt="Midone - HTML Admin Template" src="dist/images/profile-8.jpg">
                                                </div>
                                                <div class="ml-4 mr-auto">
                                                    <div class="font-medium">Miguel Angel</div>
                                                </div>
                                                <div class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium">{{$solMiguelAngel}} Cotizaciones</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- END: Weekly Best Sellers -->
                                
                               
                            </div>
                        </div>
                        <div class="col-span-12 2xl:col-span-3">
                            <div class="2xl:border-l -mb-10 pb-10">
                                <div class="2xl:pl-6 grid grid-cols-12 gap-x-6 2xl:gap-x-0 gap-y-6">
                                    
                                    
                                    <!-- BEGIN: Important Notes -->
                                    <div class="col-span-12 md:col-span-6 xl:col-span-12 xl:col-start-1 xl:row-start-1 2xl:col-start-auto 2xl:row-start-auto mt-3">
                                        <div class="intro-x flex items-center h-10">
                                            <h2 class="text-lg font-medium truncate mr-auto">
                                                Últimas solicitudes cargadas
                                            </h2>
                                            <button data-carousel="important-notes" data-target="prev" class="tiny-slider-navigator btn px-2 border-slate-300 text-slate-600 dark:text-slate-300 mr-2"> <i data-lucide="chevron-left" class="w-4 h-4"></i> </button>
                                            <button data-carousel="important-notes" data-target="next" class="tiny-slider-navigator btn px-2 border-slate-300 text-slate-600 dark:text-slate-300 mr-2"> <i data-lucide="chevron-right" class="w-4 h-4"></i> </button>
                                        </div>
                                        <div class="mt-5 intro-x">
                                            <div class="box zoom-in">
                                                <div class="tiny-slider" id="important-notes">

                                                    @foreach ($ultimasSolicitudes as $uS)
                                                    <div class="p-5">
                                                        <div class="text-base font-medium truncate">MEDICO : {{DB::table('medicos')->where('id', $uS->medicos_id)->value('nombremedico')}}</div>
                                                        <div class="text-slate-400 mt-1">{{$uS->created_at}}</div>
                                                        <div class="text-slate-500 text-justify mt-1">PACIENTE: {{DB::table('afiliados')->where('id', $uS->afiliados_id)->value('apeynombres')}}<br> Observación: {{$uS->observaciones}}</div>
                                                        <div class="font-medium flex mt-5">
                                                            <a href="/admin/entrantes/detail/{{$uS->id}}" class="btn btn-outline-secondary py-1 px-2 ml-auto ml-auto bg-success text-white">Ver solicitud</a>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                  
                                                   
                                                  
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- END: Important Notes -->
                                   
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END: Content -->


        <!-- BEGIN: JS Assets-->
        <script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key=["your-google-map-api"]&libraries=places"></script>
        <script src="dist/js/app.js"></script>
        <!-- END: JS Assets-->
    </body>
</html>