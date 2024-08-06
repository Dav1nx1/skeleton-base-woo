function mfn_field_radio_img(field) {
	let classes = ['form-group','visual-options','positioning-options','checkboxes-list'];
	let alias = field.id;
	let value = '';

	if( _.has(field, 'alias') ){
		alias = field.alias;
	}

	if( _.has(field, 'std') ){
		value = field.std;
	}

	if( _.has(edited_item['attr'], field.old_id) || _.has(edited_item['attr'], field.id) ){
		// deprecated id with style
		if( _.has(edited_item['attr'], field.old_id) ){
			value = edited_item['attr'][field.old_id];
		}else if(_.has(edited_item['attr'], field.id)){
			if( _.has(edited_item['attr'][field.id], 'val') ){
				value = edited_item['attr'][field.id]['val'];
			}else{
				value = edited_item['attr'][field.id];
			}
		}
	}else if( (edited_item.jsclass == 'pageoption' || edited_item.jsclass == 'themeoption') && _.has(edited_item, field.id) ){
		// themeoption
		if( _.has(edited_item[field.id], 'val') && typeof edited_item[field.id]['val'] == 'string' ){
			value = edited_item[field.id]['val'];
		}else if( typeof edited_item[field.id] == 'string' ){
			value = edited_item[field.id];
		}
		//value = edited_item[field.id];
	}

	let html = `<div class="form-content"><div class="${classes.join(' ')}">
			<div class="form-control">
				<ul>
				${ _.has(field, 'options') ? _.map(field.options, function(opt, o) {

					let img = o;

					if( !img.length ) {
						img = '_default';
					}else{
						img = img.replaceAll(',', '-').replaceAll(';', '-').replaceAll('+', '-').replaceAll(' ', '-');
					}

					return `<li class="${ value == o ? 'active' : '' }">
						<input type="checkbox" ${ value == o ? 'checked' : '' } class="mfn-form-checkbox mfn-field-value" name="${field.id}" value="${o}" />
						<a href="#">
							<div class="mfn-icon" data-tooltip="${opt.replace('<span>', '').replace('</span>', '').replace('<br>', '')}">
								<img src="${mfnvbvars.themepath+'/muffin-options/svg/select/'+alias+'/'+img+'.svg'}" alt="${opt}" />
							</div>
							<span class="label">${opt}</span>
						</a>
					</li>`;

				}).join('') : '' }
				</ul>
			</div>
		</div></div>`;

	return html;
}