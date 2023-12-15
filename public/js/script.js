$(document).ready(function () {
	$(document).delegate('[data-toggle="tooltip"]', 'mouseover', function(){
		$(this).tooltip({ html : true });
	});
	$(document).delegate('[data-toggle="popover"]', 'mouseover', function(){
		$(this).popover({ html : true });
	});

	$("a[href='?c=base&a=manual']").attr('target', '_blank');

	$(".fecha").datetimepicker({
      maxDate: 'now',
      format: 'DD/MM/YYYY'
    })

/**
 * Opciones por defecto para todas las implementaciones de DataTable()
*/
	if (typeof($.fn.dataTable) !== 'undefined') {
		$.fn.dataTable.ext.errMode	= 'none';
		$.extend( $.fn.dataTable.defaults, {
			language: {
				url: '/cdn/datatables/1.10.12/Spanish_sym.json',
				decimal: ',',
				thousands: '.',
				search: '_INPUT_',
				searchPlaceholder: 'Ingrese búsqueda'
			},
			info: true,
			buttons: [],
			order: [[0, 'desc']],
			ordering:  true,
			searching: true,
			columnDefs: [
				{targets: 3, searchable: false, orderable: false}
			]
		});
	}
});

$('.tabulable').ready(function () {
	$('.tabulable').keypress(function (event) {
		if ( event.which == 13 ) {
			event.preventDefault();
			if (event.keyCode == 13) {
				/* FOCUS ELEMENT */
				var inputs = $(this).parents("form").eq(0).find(":input");
				var idx = inputs.index(this);

				if (idx == inputs.length - 1) {
					inputs[0].select()
				} else {
					inputs[idx + 1].focus(); //  handles submit buttons
					inputs[idx + 1].select();
				}
				return false;
			}
		}
	});
});

/**
 * Llena los elementos de una etiqueta <select> pasandole un array.
 * Se encarga de limpiar el contenido antes del llenado o mantener los ids previamente seleccionados.
 *
 * @param boolen	$not_clean	- Si esta en true, mantiene el "value" preseleecionado, ideal para articular con PHP.
 * @param string	$dom_select	- valor usado para seleccionar el elemento dom. E.j.: 'select#id_situacion_revista'
 * @param array		$options	- Opciones para el las etiquetas select con formato ['id' => '', nombre => '', borrado => '']
 * @return JQuery
*/
	function addOptions($options, $dom_select, $not_clean=false){
		$obj				= $($dom_select);
		if($obj[0].nodeName	!= 'SELECT') return $obj;

		$value_pre_selected	= false;
		if($obj.val() != '' && $not_clean){
			$value_pre_selected = $obj.val();
		}
// Limpiar etiquetas <Select> antes de llenarlas
		$obj.html('');
		$obj.append($('<option>', {
			value: '',
			text : 'Seleccione'
		}));
// Llenar etiquetas <Select>
		$.each($options, function (i, item) {
			$_options	= {
				value: item.id,
				text : item.nombre,
			};
			if(item.borrado != '0'){
				$_options.disabled	= 'disabled';
			}
			if(item.id	== $value_pre_selected){
				$_options.selected	= 'selected';
			}
			$obj.append($('<option>', $_options));
		});
		return $obj;
	}

	function isNumber(evt) {
		evt = (evt) ? evt : window.event;
		var charCode = (evt.which) ? evt.which : evt.keyCode;
		if ( (charCode > 31 && charCode < 48) || charCode > 57) {
			return false;
		}
		return true;
	}