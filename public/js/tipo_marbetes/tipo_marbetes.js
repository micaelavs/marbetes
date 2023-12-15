$(document).ready(function () {
    var _table = $('.tipo_marbetes').DataTable({
        language: {
            url: $endpoint_cdn + '/datatables/1.10.12/Spanish_sym.json',
            search: '_INPUT_',
            searchPlaceholder: 'Ingrese búsqueda'
        },
        autoWidth: false,
        bFilter: true,
        info: true,
        columnDefs: [
            { targets: 0, width: '10%' },
            { targets: 1, width: '10%' },
            { targets: 2, width: '5%' }
        ],
        order: [[0, 'asc']]
    });

});