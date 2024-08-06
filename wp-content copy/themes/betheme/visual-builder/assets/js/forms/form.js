class MfnForm {

	constructor(fields, formrow = true) {
		this.fields = fields;
		this.formrow = formrow;
		this.field = {};
		this.html = '';
		this.rwd = 'desktop';
		this.devices = ['desktop', 'laptop', 'tablet', 'mobile'];
	}

	responsive(active) {

 		return `<ul class="responsive-switcher">
 			<li class="${ active == 'desktop' ? 'active' : '' }" data-device="desktop" data-tooltip="Desktop">
 				<span data-device="desktop" class="mfn-icon mfn-icon-desktop"></span>
 			</li>
 			<li class="${ active == 'laptop' ? 'active' : '' }" data-device="laptop" data-tooltip="Laptop">
 				<span data-device="laptop" class="mfn-icon mfn-icon-laptop"></span>
 			</li>
 			<li class="${ active == 'tablet' ? 'active' : '' }" data-device="tablet" data-tooltip="Tablet">
 				<span data-device="tablet" class="mfn-icon mfn-icon-tablet"></span>
 			</li>
 			<li class="${ active == 'mobile' ? 'active' : '' }" data-device="mobile" data-tooltip="Mobile">
 				<span data-device="mobile" class="mfn-icon mfn-icon-mobile"></span>
 			</li>
 		</ul>`;

	}

	render() {

		_.map(this.fields, (field) => {

			if( _.has(field, 'responsive') && field.responsive == true ) {

				let id = field.id;
				let oldid = ''; 
				let cssstyle = '';

				_.map(this.devices, (device) => {

					this.rwd = device;

					this.field = JSON.parse(JSON.stringify(field));
					this.field.responsive = device;

					if( _.has(field, 'old_id') ){
						oldid = field.old_id;
						cssstyle = field.css_style;
					}

					if( device != 'desktop' ) {

						if( _.has(field, 'old_id') ) {
							this.field.old_id = oldid+'_'+device;
							this.field.css_style = cssstyle+'_'+device;
						}

						this.field.id = id+'_'+device;

						if( _.has(this.field, 'std') ) delete(this.field.std);
						
					}

					if( _.has(field, 'value') && field.value == 'find' ) {
						var obj = false;
						
						if( edited_item && _.has(edited_item['attr'], 'hotspots') ){
							obj = edited_item['attr']['hotspots'].filter( o => o.hash == this.field.hash )[0];
						}

						this.field.value = _.has(obj, 'val') && _.has(obj['val'], this.field.id) ? obj['val'][this.field.id]['val'] : '';
					}


					this.display();

				});

			} else {
				this.rwd = 'desktop';
				this.field = JSON.parse(JSON.stringify(field));
				this.display();
			}

		});

		return this.html;

	}


	display() {

		let field_name = _.has(this.field, 'type') ? 'mfn_field_'+this.field.type : 'mfn_field_header';
		let classes = ['mfn-form-row', 'mfn-vb-formrow'];
		let label_classes = ['form-label'];
		let isModified = false;

		// responsive
		if( _.has(this.field, 'responsive') ){
			classes.push(`mfn_field_${this.field.responsive}`);
		}else if( _.has(this.field, 'responsive') ){

		}

		if( _.has(this.field, 'themeoptions') ){
			let to_split = this.field.themeoptions.split(':');
			if( to_split.length > 0 ){
				if( ( !_.isEmpty(mfnDbLists.themeoptions[to_split[0]]) && mfnDbLists.themeoptions[to_split[0]] != to_split[1]) || ( _.isEmpty(mfnDbLists.themeoptions[to_split[0]]) && !_.isEmpty(to_split[1]) ) ){
					return;
				}else{
					if( !_.isEmpty(mfnDbLists.themeoptions['style']) ){
						classes.push('theme-simple-style');
					}else{
						classes.push('theme-classic-style');
					}
				}
			}
		}

		// classes
		if( _.has(this.field, 'class') ){

			if( field_name != 'mfn_field_header' && this.field.class.includes('mfn-deprecated') && ( !_.has(edited_item['attr'], this.field.id) || ( _.has(this.field, 'std') && edited_item['attr'][this.field.id] == this.field.std ) ) ){
				return;
			}

			classes.push(this.field.class);
		}

		if( _.has(edited_item, 'jsclass') ){
			let element_type = edited_item.jsclass;
			if( element_type == 'button' || element_type == 'chart' || element_type == 'code' || element_type == 'sliderbar' ){
				element_type = 'widget-'+element_type;
			}
			classes.push(element_type)
		}

		if( _.has(edited_item, 'uid') && edited_item.uid == 'pageoptions' ){
			classes.push('option');
		}

		// slider input for dimensional inputs
		if( _.has(this.field, 'type') && this.field.type == 'dimensions' ){
			classes.push('mfn-slider-input');
		}

		if( field_name == 'mfn_field_html' ){

			// no form-row field
			this.html += window[field_name](this.field);

		}else if( field_name == 'mfn_field_info' ){

			// no form-row field
			this.html += `<div class="${classes.join(' ')}">${window[field_name](this.field)}</div>`;

		}else{

			let id = _.has(this.field, 'attr_id') ? `id="${this.field.attr_id.replace('rwd', this.rwd)}"` : '';
			let data_attr = [];
			let label = _.has(this.field, 'title') ? this.field.title : '';

			// responsive
			if( _.has(this.field, 'responsive') ){
				label += `${this.responsive(this.field.responsive)}`;
			}

			// label after
			if( _.has(this.field, 'label_after') ){
				label += this.field.label_after;
			}

			// desc switcher
			if( _.has(this.field, 'desc') ){
				label_classes.push('form-label-wrapper');
				label += '<a class="mfn-option-btn mfn-option-blank mfn-fr-help-icon" target="_blank" data-tooltip="Toggle description" href="#"><span class="mfn-icon mfn-icon-desc"></span></a>';
			}

			// conditions 
			if( _.has(this.field, 'condition') ){
				classes.push(`activeif activeif-${this.field.condition.id.replace('rwd', this.rwd)}`);
				data_attr.push(`data-conditionid="${this.field.condition.id.replace('rwd', this.rwd)}"`);
				data_attr.push(`data-opt="${this.field.condition.opt}"`);
				data_attr.push(`data-val="${this.field.condition.val}"`);
			}

			if( _.has(this.field, 'dynamic_data') ){
				classes.push('is_dynamic_data');
				data_attr.push(`data-dynamic="${this.field.dynamic_data}"`);
			}

			// edit text
			if( _.has(this.field, 'edit_tag') ){
				classes.push(`content-txt-edit`);
				data_attr.push(`data-edittag="${this.field['edit_tag']}"`);

				if( _.has(this.field, 'edit_tagchild') ){
					data_attr.push(`data-edittagchild="${this.field['edit_tagchild']}"`);
				}
				if( _.has(this.field, 'edit_position') ){
					data_attr.push(`data-tagposition="${this.field['edit_position']}"`);
				}
				if( _.has(this.field, 'edit_tag_var') ){
					data_attr.push(`data-edittagvar="${this.field['edit_tag_var']}"`);
				}
			}

			if( _.has(this.field, 'id') ) {
				data_attr.push(`data-id="${this.field.id}"`);
				if( !this.field.id.includes('style:') && !_.has(this.field, 'css_path') ) {
					classes.push(this.field.id);
					data_attr.push(`data-name="${this.field.id}"`);
					this.field['input_class'] = 'preview-'+this.field.id+'input';
				}
			}

			// style
			if( _.has(this.field, 'id') && ( this.field.id.includes('style:') || _.has(this.field, 'css_path') ) ) {

				let style_tag, style_name;

				if( _.has(this.field, 'css_path') ){
					style_tag = this.field.css_path;
					style_name = this.field.css_style;
					if( _.has(this.field, 'old_id') ) data_attr.push(`data-oldid="${this.field.old_id}"`);
					classes.push('object-css-input');
				}else{
					style_tag = this.field.id.split(':');
					style_name = style_tag[2].replace('_mobile', '').replace('_tablet', '').replace('_laptop', '');

					style_tag = style_tag[1];
				}

				if( _.has(this.field, 'key') ){
					data_attr.push(`data-name="${this.field.key}"`);
				}else{
					data_attr.push(`data-name="${style_name}"`);
				}

				data_attr.push(`data-csspath="${style_tag}"`);
				classes.push('inline-style-input');
				classes.push( style_name );
				this.field['input_class'] = 'preview-'+style_name+'input';
			}

			if( _.has(this.field, 'key') ) {
				classes.push(this.field.key);
			}

			if( _.has(this.field, 'data_attr') ) {
				data_attr.push(this.field['data_attr']);
			}

			if( _.has(this.field, 're_render') ){
				classes.push('re_render');
			}

			if( _.has(this.field, 're_render_if') ){
				let explode_rrf = this.field['re_render_if'].split('|');
				if( explode_rrf.length == 2 ){
					data_attr.push(`data-retype="${explode_rrf[0]}"`);
					data_attr.push(`data-reelement="${explode_rrf[1]}"`);
				}
				classes.push('re_render_if')
			}

			if( this.formrow ) this.html += `<div ${id} class="${classes.join(' ')}" ${data_attr.join(' ')}>`;

			this.html += `
				${ field_name != 'mfn_field_info' && field_name != 'mfn_field_header' && field_name != 'mfn_field_subheader' && field_name != 'mfn_field_helper' ? `<label class="${label_classes.join(' ')}">${label}</label>` : '' }
				${ _.has(this.field, 'desc') ? `<div class="desc-group"><span class="description">${this.field.desc}</span></div>` : '' }
				${ _.has(window, field_name) ? window[field_name](this.field, this.rwd) : '* '+field_name }
			`;

			if( this.formrow ) this.html += `</div>`;

		}

	}


}