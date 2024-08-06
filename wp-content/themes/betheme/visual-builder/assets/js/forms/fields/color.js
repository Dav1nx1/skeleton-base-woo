function mfn_field_color(field = false) {
	let placeholder = '';
	let value = '';
	let classes = ['mfn-form-control mfn-form-input color-picker-vb'];
	let name_attr = '';
	let data_attr = '';
	let html = '';

	if( _.has(field, 'std') ){
		placeholder = field.std;
	}

	if( _.has(field, 'field_class') ){
		classes.push(field.field_class);
	}

	if( _.has(field, 'id') ){
		name_attr = `name="${field.id}"`;
		html += `<div class="form-content">`;
	}

	if( _.has(field, 'on_change') ){
		classes.push('field-to-object'); // object updater only
	}else if( _.has(field, 'id') ){
		classes.push('mfn-field-value'); // all on change actions
	}

	if( _.has(field, 'default_value') ){
		value = field.default_value;
	}


	if( _.has(edited_item['attr'], field.old_id) || _.has(edited_item['attr'], field.id) ){
		// deprecated id with style
		if( _.has(edited_item['attr'], field.old_id) ){
			value = edited_item['attr'][field.old_id];
			if( _.has(field, 'key') && _.has(edited_item['attr'][field.old_id], field.key) ){
				value = edited_item['attr'][field.old_id][field.key];
			}
		}else if(_.has(edited_item['attr'], field.id)){
			if( _.has(field, 'key') && _.has(edited_item['attr'][field.id], field.key) ){
				value = edited_item['attr'][field.id][field.key];
			}else if( _.has(edited_item['attr'][field.id], 'val') ){

				if( typeof edited_item['attr'][field.id]['val'] == 'object' && _.has(field, 'key') && _.has(edited_item['attr'][field.id]['val'], field.key) ){
					value = edited_item['attr'][field.id]['val'][field.key];
				}else{
					value = edited_item['attr'][field.id]['val'];
				}
				
			}else{
				value = edited_item['attr'][field.id];
			}
		}
	}else if( (edited_item.jsclass == 'pageoption' || edited_item.jsclass == 'themeoption') && _.has(edited_item, field.id) ){
		// themeoption
		if( _.has(edited_item[field.id], 'val') ){
			if( _.has(field, 'key') && _.has(edited_item['val'], field.id) && _.has(edited_item[field.id]['val'], field.key) ){
				value = edited_item[field.id]['val'][field.key];
			}else{
				value = edited_item[field.id]['val']
			}
		}else{
			value = edited_item[field.id];
			if( _.has(field, 'key') && _.has(edited_item, field.id) && _.has(edited_item[field.id], field.key) ){
				value = edited_item[field.id][field.key];
			}
		}
		
		
	}

	if( typeof field === 'string' ){
		value = field;
	}

	if( _.has(field, 'key') ){
		data_attr = `data-key="${field.key}"`;
	}

	if( _.has(field, 'point_key') ) {
		data_attr = `data-pointobj="${field.point_key}"`;
	}

	if( value == '#fff' ) value = '#ffffff';

	html += `<div class="form-group color-picker has-addons has-addons-prepend">
		<div class="color-picker-group">
			<div class="form-addon-prepend"><a href="#" class="color-picker-open"><span ${ value != '' ? `style="background-color: ${value}; border-color: ${value};"` : '' } class="label ${value.length ? getContrastYIQ( value ) : 'light'}"><i class="icon-bucket"></i></span></a></div>
			<div class="form-control has-icon has-icon-right field">
				<input ${data_attr} class="${classes.join(' ')}" type="text" placeholder="${placeholder}" ${name_attr} value="${value}" autocomplete="off" />
				<a class="mfn-option-btn mfn-option-text color-picker-clear" href="#"><span class="text">Clear</span></a>
			</div>
		</div>
	</div>`;

	if( _.has(field, 'id') ){
		html += `</div>`;
	}

	return html;
}