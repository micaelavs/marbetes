$(document).ready(function () {
	if ($('.empresas').length) { //para que actue Datatable sólo donde haya una tabla
		var tabla = $('#tabla').DataTable({
	        language: {
	            url: $endpoint_cdn + '/datatables/1.10.12/Spanish_sym.json',
	            decimal: ',',
	            thousands: '.',
	            infoEmpty: 'No hay datos de Empresas...'
	        },
	        processing: true,
	        serverSide: true,
	        //responsive: true,
	        searchDelay: 1200,

	        ajax: {
	            url: $url_base + '/index.php/empresas/ajax_empresas',
	            contentType: "application/json",
	        },
	        info: true,
	        bFilter: true,
	        columnDefs: [
		        { targets: 0, width: '10%'},
		        { targets: 1, width: '10%'},
		        { targets: 2, width: '10%'},
		        { targets: 3, width: '10%'},
				{ targets: 4, width: '10%' },
				{ targets: 5, width: '10%' },
				{ targets: 6, width: '10%' },
		        { targets: 7, width: '5%'}
	        ],
	        order: [[0,'asc']],
	        columns: [
	            {
	                title: 'Cuit',
	                name:  'cuit',
	                data:  'cuit',
	                className: 'text-left'
	            },
	            {
	                title: 'Razón Social',
	                name:  'razon_social',
	                data:  'razon_social',
	                className: 'text-left'
	            },
	            {
	                title: 'Código CNRT',
	                name:  'codigo_cnrt',
	                data:  'codigo_cnrt',
	                className: 'text-left'
	            },
	            {
	                title: 'Cámara',
	                name: 'camara',
	                data: 'camara',
	                className: 'text-left'
	            },
	            {
	                title: 'Dirección',
	                name: 'direccion',
	                data: 'direccion',
	                className: 'text-left'
	            },
	            {
	                title: 'Nombre Apoderado',
	                name: 'nombre_apoderado',
	                data: 'nombre_apoderado',
	                className: 'text-left'
	            },
	            {
	                title: 'Dni Apoderado',
	                name: 'dni_apoderado',
	                data: 'dni_apoderado',
	                className: 'text-left'
	            },
	            {
	                title: 'Acciones',
	                data: 'acciones',
	                name: 'acciones',
	                className: 'text-center',
	                orderable: false,
	                render: function (data, type, row) {
	                    var $html = '';
	                    $html += '<div class="btn-group btn-group-sm">';
	                    $html += ' <a href="' + $url_base + '/index.php/empresas/modificar/' + row.id + '" data-user="" data-toggle="tooltip" data-placement="top" title="Modificar Empresa" target="_self"><i class="fa fa-pencil"></i></a>&nbsp;';
	                    $html += ' <a href="' + $url_base + '/index.php/empreas/baja/' + row.id + '" class="borrar" data-user="" data-toggle="tooltip" data-placement="top" title="Baja Empresa" target="_self"><i class="fa fa-trash"></i></a>';
	                    $html += '</div>';
	                    return $html;
	                }
	            },
	        ]
	    });

	}

	if($('#cuit').length){ //para quue solo se active el typeahead, si existe este campo en la vista
		$('#cuit').typeahead({
	        onSelect: function (item) {

	        },
	        ajax: {
	            url: $base_url+"/Empresas/buscarEmpresa",
	            //timeout: 300,
	            displayField: 'full_name',
	            valueField: 'dep',
	            triggerLength: 11,
	            method: "post",
	            loadingClass: "loading-circle",
	            preDispatch: function (query) {
	                return {
	                    cuit: query,
	                }
	            },
	            preProcess: function (data) {
	                if (data.success === false) {
	                	$("#aviso").text('El servicio para consultar CUIT, no se encuentra disponible, intente en unos minutos.');
	                    return false;
	                }
	                $("#aviso").text('');
	                var razonSocial ='';
	                var codigoCNRT = '';
					var direccion = '';
					var nombreApoderado = '';
					var dniApoderado = '';
	                if(data.data.results.length == 0){
	                	 $('#aviso').text("Debe ingresar un CUIT de una Empresa válida");
	                     razonSocial = '';
	                     codigoCNRT = '';
						 direccion = '';
						 nombreApoderado = '';
						 dniApoderado = '';
	                     $("#razon_social").text(razonSocial);
	                  	 $("input:hidden[name=razon_social]").val(razonSocial);
	                     $("#codigo_cnrt").text(codigoCNRT);
	                     $("input:hidden[name=codigo_cnrt]").val(codigoCNRT);
						$("#direccion").text(direccion);
						$("input:hidden[name=direccion]").val(direccion);
						$("#nombre_apoderado").text(nombreApoderado);
						$("input:hidden[name=nombre_apoderado]").val(nombreApoderado);
						$("#dni_apoderado").text(dniApoderado);
						$("input:hidden[name=dniapoderado]").val(dniApoderado);

	                }else{
	                	razonSocial = data.data.results[0].razonSocial;
	                	codigoCNRT 	= data.data.results[0].id;
	                	$("#aviso").text('');
	                	$("#razon_social").text(razonSocial);
	                	$("input:hidden[name=razon_social]").val(razonSocial);
	                	$("#codigo_cnrt").text(codigoCNRT);
	                	$("input:hidden[name=codigo_cnrt]").val(codigoCNRT);
						$("#direccion").text(direccion);
						$("input:hidden[name=direccion]").val(direccion);
						$("#nombre_apoderado").text(nombreApoderado);
						$("input:hidden[name=nombre_apoderado]").val(nombreApoderado);
						$("#dni_apoderado").text(dniApoderado);
						$("input:hidden[name=dniapoderado]").val(dniApoderado);
	                }

	            }
	        }
	    });
	}


});


