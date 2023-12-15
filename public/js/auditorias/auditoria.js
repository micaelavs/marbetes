$(document).ready(function () {
    var tabla = $('#tabla').DataTable({
            language: {
                url: $endpoint_cdn + '/datatables/1.10.12/Spanish_sym.json',
                decimal: ',',
                thousands: '.',
                infoEmpty: 'No hay datos de Historia Clinica...'
            },
            processing: true,
            serverSide: true,
            //responsive: true,
            searchDelay: 1200,

            ajax: {
                url: $base_url + '/auditorias/ajax_auditoria',
                contentType: "application/json",
                data: function (d) {
                filtros_dataTable = $.extend({}, d, {
                    empresa             : $('#empresa').val(),
                    fecha_desde         : $('#fecha_desde').val(),
                    fecha_hasta         : $('#fecha_hasta').val(),
                    imprenta            : $('#imprenta').val(),
                    tipo_marbete        : $("#tipo_marbete").val(),
                    camara              : $("#camara").val()

                });
                return filtros_dataTable; 
                }   
            },
            info: true, 
            bFilter: true,
            columnDefs: [
                { targets: 0},
                { targets: 1},
                { targets: 2},
                { targets: 3},
                { targets: 4},
                { targets: 5},
                { targets: 6},
                { targets: 7},
                { targets: 8, orderable: false}
            ],
            order: [[1, "desc"], [5, "desc"]],
            columns: [
                {
                    title: 'Imprenta',
                    data: "imprenta",
                    name: "imprenta",
                    className: 'text-left'
                },
                {
                    title: 'Empresa',
                    data: 'empresa',
                    name: 'empresa',
                    className: 'text-left',
                },
                {
                    title: 'Camara',
                    data: 'camara',
                    name: 'camara',
                    className: 'text-left',
                },
                {
                    title: 'Tipo Marbete',
                    data: 'tipo_marbete',
                    name: 'tipo_marbete',
                    className: 'text-left',
                },
                {
                    title: 'Estado',
                    data: 'estado',
                    name: 'estado',
                    className: 'text-left',
                },
                {
                    title: 'Fecha de solicitud ',
                    data: 'fecha_solicitud',
                    name: 'fecha_solicitud',
                    className: 'text-left',
                      render: function (data, type, row) {
                        if(data == null){
                        }else{
                            rta = moment(data,'DD/MM/YYYY HH:II').format('DD/MM/YYYY'); 
                        }   
                        return rta;
                    }
                },
                {
                    title: 'Marbetes Solicitados',
                    data: 'cantidad',
                    name: 'cantidad',
                },
                {
                    title: 'Marbetes Asignados',
                    data: 'cantidad_autorizada',
                    name: 'cantidad_autorizada',
                },
                {
                    title: 'Numeracion Asignada',
                    data: 'numeracion_asignada',
                    name: 'numeracion_asignada',
                },
            ]
        });

    /** Consulta al servidor los datos y redibuja la tabla
     * @return {Void}
    */
    function update() {
        tabla.draw();
    }

    /**
     * Acciones para los filtros, actualizar vista
    */
    $('#empresa').on('change', update);

    $('#fecha_desde,.fecha_desde').datetimepicker({
            format: 'DD/MM/YYYY'
        }).on("dp.change", function (e) {
            update();
            $('#fecha_desde').keyup(function() { 
                if(this.value == ''){
                    update();
                }
            });
            $('#fecha_hasta').data("DateTimePicker").minDate(e.date);
        });

    $('#fecha_hasta,.fecha_hasta').datetimepicker({
            format: 'DD/MM/YYYY'
        }).on("dp.change", function (e) {
            update();
            $('#fecha_hasta').keyup(function() { 
                if(this.value == ''){
                    update();
                }
            });
            $('#fecha_desde').data("DateTimePicker").maxDate(e.date);
        });

    $('#imprenta').on('change', update);

    $('#tipo_marbete').on('change', update);

    $('#camara').on('change', update);

    /****/
    
    $("#empresa").select2();
    
    $("#imprenta").select2();
   
    $("#tipo_marbete").select2();
    
    $("#camara").select2();
    
    $("#fecha_desde").datetimepicker({
        format: 'DD/MM/YYYY'
    });

    $("#fecha_hasta").datetimepicker({
        format: 'DD/MM/YYYY'
    });

    $(".accion_exportador").click(function () {
        var form = $('<form/>', { id: 'form_ln', action: $(this).val(), method: 'POST' });
        $(this).append(form);
        form.append($('<input/>', {name: 'search', type: 'hidden', value: $('div.dataTables_filter input').val() }))
            .append($('<input/>', {name: 'campo_sort', type: 'hidden', value: $('#tabla').dataTable().fnSettings().aoColumns[$('#tabla').dataTable().fnSettings().aaSorting[0][0]].name }))
            .append($('<input/>', {name: 'dir', type: 'hidden', value: $('#tabla').dataTable().fnSettings().aaSorting[0][1] }))
            .append($('<input/>', {name: 'rows', type: 'hidden', value: $('#tabla').dataTable().fnSettings().fnRecordsDisplay() }))
            .append($('<input/>', {name: 'empresa', type: 'hidden', value:$('#empresa').val() }))
            .append($('<input/>', {name: 'fecha_desde', type: 'hidden', value:$('#fecha_desde').val() }))
            .append($('<input/>', {name: 'fecha_hasta', type: 'hidden', value:$('#fecha_hasta').val() }))
            .append($('<input/>', {name: 'imprenta', type: 'hidden', value:$('#imprenta').val() }))
            .append($('<input/>', {name: 'tipo_marbete', type: 'hidden', value:$('#tipo_marbete').val() }))
            .append($('<input/>', {name: 'camara', type: 'hidden', value:$('#camara').val() }));
        form.submit();
    });

});
