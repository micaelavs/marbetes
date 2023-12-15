$(document).ready(function () {

    if($('.listado_imprenta').length){

        var _table = $('.imprentas').DataTable({
            language: {
                url: $endpoint_cdn + '/datatables/1.10.12/Spanish_sym.json',
                search: '_INPUT_',
                searchPlaceholder: 'Ingrese búsqueda'
            },
            autoWidth: false,
            bFilter: true,
            info: true,
            columnDefs: [
                { targets: 0, width: '15%' },
                { targets: 1, width: '30%' },
                { targets: 2, width: '50%' },
                { targets: 3, width: '5%' }
            ],
            order: [[0, 'asc']]
        });
    }
    
    if($('.alta_imprenta').length){

        $('#inscripcion_afip').on('change', function($e){
        
        let inscripcion_afip = $tipo_check['inscripcion_afip'];
    
         if($(this).prop('checked')){
              $(this).val(inscripcion_afip);
             }else{
                  $(this).val('');    
             }
        });

        $('#modelo_marbete').on('change', function($e){
        
        let modelo_marbete = $tipo_check['modelo_marbete'];
      
         if($(this).prop('checked')){
              $(this).val(modelo_marbete);
             }else{
                  $(this).val('');    
             }
        });

        $(".fecha_ultima_revision").datetimepicker({
                    format: 'DD/MM/YYYY',
                    maxDate: 'now'
                });
        $("#fecha_ultima_revision").datetimepicker({
                format: 'DD/MM/YYYY',
                maxDate: 'now'
            });

    }    

});