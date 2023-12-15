$(document).ready(function () {

	if ($('.pedidos_listado').length) { 
		var tabla = $('#tabla').DataTable({
	        language: {
	            url: $endpoint_cdn + '/datatables/1.10.12/Spanish_sym.json',
	            decimal: ',',
	            thousands: '.',
	            infoEmpty: 'No hay datos de Pedidos...'
	        },
	        processing: true,
	        serverSide: true,
	        //responsive: true,
	        searchDelay: 1200,

	        ajax: {
	            url: $base_url + '/index.php/Pedidos_marbete/ajax_pedidos',
	            contentType: "application/json",
	            data: function (d) {
               
                } 	
	        },
	        info: true, 
	        bFilter: true,
	        columnDefs: [
	        	{ targets: 0, width: '5%'}, //id
		        { targets: 1, width: '10%'}, //empresa
		        { targets: 2, width: '10%'}, //tipo_marbete
		        { targets: 3, width: '10%'}, //imprenta
		        { targets: 4, width: '10%'}, //fecha_solicitud
				{ targets: 5, width: '5%'}, //cantidad_solicitada
				{ targets: 6, width: '5%'}, //cantidad_autorizada
				{ targets: 7, width: '8%'}, //estado
				{ targets: 8, width: '15%'}, //observaciones
				{ targets: 9, width: '10%' } //acciones
	        ],
	        order: [[4,'desc']],
	        columns: [
	        	{
	                title: 'Nº pedido',
	                name:  'id',
	                data:  'id',
	                className: 'text-left'
	            },
	            {
	                title: 'Empresa',
	                name:  'empresa',
	                data:  'empresa',
	                className: 'text-left'
	            },
	            {
	                title: 'Tipo Marbete',
	                name:  'tipo_marbete',
	                data:  'tipo_marbete',
	                className: 'text-left',
	            },
	            {
	                title: 'Imprenta',
	                name:  'imprenta',
	                data:  'imprenta',
	                className: 'text-left'
	            },
	            {
	                title: 'Fecha de solicitud',
	                name: 'fecha_solicitud',
	                data: 'fecha_solicitud',
	                className: 'text-left',
	                render: function (data, type, row) {
	                	let rta= '';
						if(data == null){
						}else{
							rta = moment(data,'DD/MM/YYYY').format('DD/MM/YYYY'); 
						} 	
						return rta;
					}
	            },
	            {
	                title: 'Cantidad solicitada',
	                name: 'cantidad_solicitada',
	                data: 'cantidad_solicitada',
	                className: 'text-left'
	            },
	            {
	                title: 'Cantidad autorizada',
	                name: 'cantidad_autorizada',
	                data: 'cantidad_autorizada',
	                className: 'text-left'
	            },
	            {
	                title: 'Estado',
	                name:  'estado',
	                data:  'estado',
	                className: 'text-left',
	                render: function (data, type, row) {
	                	let rta= '';
	                	if(data!=null){
	                		if(data==$estados_pedido['solicitado']){
	                			rta = '<span class="label label-default">Solicitado</span>';
	                		}else if(data==$estados_pedido['autorizado']){
	                			rta = '<span class="label label-success">Autorizado</span>';
	                		}else if(data==$estados_pedido['firmado']){
	                			rta = '<span class="label label-primary">Firmado</span>';
	                		}else if(data==$estados_pedido['rechazado']){
	                			rta = '<span class="label label-danger">Rechazado</span>';
	                		}else if(data==$estados_pedido['anulado']){
	                			rta = '<span class="label label-warning">Anulado</span>';
	                		}else if(data==$estados_pedido['impreso_entregado']){
	                			rta = '<span class="label label-info">Entregado / Impreso</span>';
	                		}		
	                	}
	                	return rta;
					}
	                
	            },
	            {
	                title: 'Observaciones',
	                name:  'observaciones',
	                data:  'observaciones',
	                className: 'text-left'
	            },
	            {
	                title: 'Acciones',
	                data: 'acciones',
	                name: 'acciones',
	                className: 'text-center',
	                orderable: false,
	                render: function (data, type, row) {
	                	let rta = '';
	                	rta += '<div class="btn-group btn-group-sm">';
	                	if(row!=null){
	                		if(row.estado == $estados_pedido['solicitado'] && $rol_actual == $roles['autorizante']){
	                			rta += ' <a href="' + $base_url + '/index.php/Pedidos_marbete/autorizacion_alta/' + row.id + '" data-user="" data-toggle="tooltip" data-placement="top" title="Autorizar pedido" target="_self"><i class="fa fa-check"></i></a>&nbsp;';
	                		}else if(row.estado == $estados_pedido['solicitado'] && $rol_actual == $roles['carga']){
	                			rta += ' <a href="' + $base_url + '/index.php/Pedidos_marbete/autorizacion_alta/' + row.id + '" data-user="" data-toggle="tooltip" data-placement="top" title="Autorizar pedido" target="_self"><i class="fa fa-check"></i></a>&nbsp;';
	                		}else{
	                			rta += '';
	                		}

	                		if(row.estado == $estados_pedido['autorizado'] || row.estado == $estados_pedido['rechazado'] || row.estado == $estados_pedido['firmado'] || row.estado == $estados_pedido['anulado'] || row.estado == $estados_pedido['impreso_entregado'] ){
	                			rta += '';
	                		}else{
                			  	rta += ' <a href="' + $base_url + '/index.php/Pedidos_marbete/modificar/' + row.id + '" class="borrar" data-user="" data-toggle="tooltip" data-placement="top" title="Modificar Pedido" target="_self"><i class="fa fa-pencil"></i></a>&nbsp;';
                			  	rta += ' <a href="' + $base_url + '/index.php/Pedidos_marbete/baja/' + row.id + '" class="borrar" data-user="" data-toggle="tooltip" data-placement="top" title="Baja pedido" target="_self"><i class="fa fa-trash"></i></a>&nbsp;';
	                		}

	                		if(row.estado == $estados_pedido['solicitado'] && $rol_actual == $roles['autorizante']){
	                			rta += ' <a href="' + $base_url + '/index.php/Pedidos_marbete/rechazar/' + row.id + '" data-user="" data-toggle="tooltip" data-placement="top" title="Rechazar pedido" target="_self"><i class="fa fa-times"></i></a>&nbsp;';
	                		}else if(row.estado == $estados_pedido['solicitado'] && $rol_actual == $roles['carga']){
	                			rta += ' <a href="' + $base_url + '/index.php/Pedidos_marbete/rechazar/' + row.id + '" data-user="" data-toggle="tooltip" data-placement="top" title="Rechazar pedido" target="_self"><i class="fa fa-times"></i></a>&nbsp;';
	                		}else{
	                			rta += '';
	                		}

	                		if(row.estado == $estados_pedido['firmado'] && ($rol_actual == $roles['firmante'] || $rol_actual == $roles['autorizante'] || $rol_actual == $roles['carga'] )){
	                			rta += ' <a href="' + $base_url + '/index.php/Pedidos_marbete/pedido_autorizado/' + row.id + '" data-user="" data-toggle="tooltip" data-placement="top" title="Descargar pedido autorizado y firmado" target="_self"><i class="fa fa-download"></i></a>';
	                			if($rol_actual == $roles['firmante']){
									rta += ' <a href="' + $base_url + '/index.php/Pedidos_marbete/anular/' + row.id + '" data-user="" data-toggle="tooltip" data-placement="top" title="Anular pedido" target="_self"><i class="fa fa-times-circle"></i></a>';
								}
	                		}else{
	                			rta += '';
	                		}

	                		if(row.estado == $estados_pedido['firmado'] && ($rol_actual == $roles['carga'] || $rol_actual == $roles['autorizante']) ){
	                			rta += ' <a href="' + $base_url + '/index.php/Pedidos_marbete/imprimir_entregar/' + row.id + '" data-user="" data-toggle="tooltip" data-placement="top" title="Entregar pedido" target="_self"><i class="fa fa-share-square-o"></i></a>';
	                		}else{
	                			rta += '';
	                		}

	                		
	                		if(row.estado == $estados_pedido['autorizado'] && $rol_actual == $roles['firmante']){
	                			rta += ' <a href="' + $base_url + '/index.php/Pedidos_marbete/firmar/' + row.id + '" data-user="" data-toggle="tooltip" data-placement="top" title="Firmar pedido" target="_self"><i class="fa fa-mouse-pointer"></i></a>';
	                		}else{
	                			rta += '';
	                		}

	                		rta += '</div>';
	                    	

	                	}
	                	return rta;
	                }
	            },
	        ]
	    });


	$(".accion_exportador").click(function () {
	    var form = $('<form/>', {id:'form_ln' , action : $(this).val(), method : 'POST'});
	    $(this).append(form);
	    form.append($('<input/>', {name: 'search', type: 'hidden', value: $('div.dataTables_filter input').val() }))
	        .append($('<input/>', {name: 'campo_sort', type: 'hidden', value: $('#tabla').dataTable().fnSettings().aoColumns[$('#tabla').dataTable().fnSettings().aaSorting[0][0]].name }))
	        .append($('<input/>', {name: 'dir', type: 'hidden', value: $('#tabla').dataTable().fnSettings().aaSorting[0][1] }))
	        .append($('<input/>', {name: 'rows', type: 'hidden', value: $('#tabla').dataTable().fnSettings().fnRecordsDisplay() }));
	     form.submit();
		});

	}

	 $("#id_empresa").select2();

	 $(".fecha_solicitud").datetimepicker({
				format: 'DD/MM/YYYY',
				maxDate: 'now'
			});
	$("#fecha_solicitud").datetimepicker({
			format: 'DD/MM/YYYY',
			maxDate: 'now'
		});

  	$("input").keydown(function (e){
   		var keyCode= e.which;
   		if (keyCode == 13){
     	event.preventDefault();
     	return false;
   		}
   	});

	$('select#id_empresa').on('change', function($e){
			var id_empresa;
			if($('select#id_empresa').val()==""){
				id_empresa = 0;
			}else{
				id_empresa = $('select#id_empresa').val();
			}
			$.ajax({
				url: $base_url+"/Pedidos_marbete/buscarEmpresa",
				data: {
					id_empresa: id_empresa,

				},
				method: "POST"
			})
			.done(function (data) {
				if(data.data.cuit != null){
					$("#cuit").text(data.data.cuit);
					$("#codigo_cnrt").text(data.data.codigo_cnrt);
				
				}
				if(data.data.cuit == null){
					$("#cuit").text("");
					$("#codigo_cnrt").text("");
				
				}
			})
			.fail(function(data){
				$("#cuit").val("");
				$("#codigo_cnrt").val("");
			});

	});



});