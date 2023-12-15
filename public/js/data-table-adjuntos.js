$(document).ready(function () {

	var $btn = $('#volver_legajo');
	var f = $('<form/>', { id: 'form_ln', action: '', method: 'POST' });
	var input = $('<input />', { id: 'id_bloque', name: 'id_bloque', type: 'hidden', value: '' })
	f.append(input);
	$btn.after(f);

	$('#form_l').submit();

	$('#nuevo_documento').click(function () {
		var f = $('#form_ln').attr('action', $(this).data('ref'));
		$('#id_bloque').val($(this).data('bloque'));
		f.submit();
	});

	$('#volver_legajo').click(function () {
		var f = $('#form_ln').attr('action', $(this).data('ref'));
		$('#id_bloque').val($(this).data('bloque'));
		f.submit();
	});

	var _table = $('#tabla').DataTable({
		language: {
			search: '_INPUT_',
			searchPlaceholder: 'Ingrese búsqueda'
		},
		autoWidth: false,
		lengthChange: false,
		info: false,
		bFilter: true,
		columnDefs: [
			{ targets: 0, width: '15%' },
			{ targets: 1, width: '20%' },
			{ targets: 2, width: '20%' },
			{ targets: 3, width: '25%' },
			{ targets: 3, width: '15%' },
		],
		order: [[2, 'desc']]
	});

	$.fn.dataTable.moment('DD/MM/YYYY');

});