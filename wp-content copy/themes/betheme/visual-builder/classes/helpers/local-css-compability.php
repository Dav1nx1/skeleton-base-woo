<?php  

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*error_reporting(E_ALL);
ini_set("display_errors", 1);*/

class MfnLocalCssCompability {
	
	private $id = false;
	private $mfn_items = false;
	private $s = 0;
	private $w = 0;
	private $i = 0;
	private $nw = 0;
	private $detect_old_builder = false;
	public $devices = array('laptop', 'tablet', 'mobile');
	public $builder_storage = false;


	public function render($id) {
		$this->id = $id;
		$this->mfn_items = get_post_meta($this->id, 'mfn-page-items', true);
		$this->detect_old_builder = false;
		$this->builder_storage = mfn_opts_get('builder-storage');

		if ( !is_array( $this->mfn_items ) ) $this->mfn_items = unserialize(call_user_func('base'.'64_decode', $this->mfn_items));

		if( !empty( $this->mfn_items ) && is_array( $this->mfn_items ) ) $this->sections();
	}

	public function sections() {
		$mfn_fields = new Mfn_Builder_Fields();
		$sections_fields = $mfn_fields->get_section();

		$this->detect_old_builder = false;

		foreach( $this->mfn_items as $s=>$section ) {

			$this->s = $s;

			if( !empty($section['attr']) && is_iterable($section['attr']) ) {

				foreach ($sections_fields as $sf) {

					if( !empty($sf['old_id']) && !empty( $this->mfn_items[$s]['attr'][$sf['old_id']] ) ) {

						$this->mfn_items[$s]['attr'][$sf['id']] = array(
							'val' => $this->mfn_items[$s]['attr'][$sf['old_id']],
							'css_path' => $sf['css_path'],
							'css_style' => $sf['css_style']
						);

						unset($this->mfn_items[$s]['attr'][$sf['old_id']]);
						$this->detect_old_builder = true;

					}

					if( isset($sf['old_id']) && !empty($sf['responsive']) ) {
						foreach( $this->devices as $device ) {
							$sa_device = $sf['old_id'].'_'.$device;
							if( isset( $this->mfn_items[$s]['attr'][$sa_device] ) ) {

								$sf_device = $sf['id'].'_'.$device;

								if( !empty( $this->mfn_items[$s]['attr'][$sa_device] ) ) {

									$this->mfn_items[$s]['attr'][$sf_device] = array(
										'val' => $this->mfn_items[$s]['attr'][$sa_device],
										'css_path' => $sf['css_path'],
										'css_style' => $sf['css_style'].'_'.$device
									);

								}

								unset($this->mfn_items[$s]['attr'][$sa_device]);
								$this->detect_old_builder = true;

							}

						}
					}

				}

			}

			if( !empty($section['wraps']) && is_iterable($section['wraps']) ){
				foreach( $section['wraps'] as $w=>$wrap ) {
					$this->w = $w;
					$this->wraps($wrap);
				}
			}

		}

		if( $this->detect_old_builder ){
			$this->update();
		}

	}


	public function wraps( $wrap ) {
		$mfn_fields = new Mfn_Builder_Fields();
		$wraps_fields = $mfn_fields->get_wrap();

		if( !empty($wrap['attr']) && is_iterable($wrap['attr']) ) {

			foreach ($wraps_fields as $wf) {

				if( !empty($wf['old_id']) && !empty( $this->mfn_items[$this->s]['wraps'][$this->w]['attr'][$wf['old_id']] ) ) {

					$this->mfn_items[$this->s]['wraps'][$this->w]['attr'][$wf['id']] = array(
						'val' => $this->mfn_items[$this->s]['wraps'][$this->w]['attr'][$wf['old_id']],
						'css_path' => $wf['css_path'],
						'css_style' => $wf['css_style']
					);

					unset($this->mfn_items[$this->s]['wraps'][$this->w]['attr'][$wf['old_id']]);
					$this->detect_old_builder = true;

				}

				if( isset($wf['old_id']) && !empty($wf['responsive']) ) {
					foreach( $this->devices as $device ) {

						$wa_device = $wf['old_id'].'_'.$device;
						if( isset( $this->mfn_items[$this->s]['wraps'][$this->w]['attr'][$wa_device] ) ) {

							$wf_device = $wf['id'].'_'.$device;

							if( !empty( $this->mfn_items[$this->s]['wraps'][$this->w]['attr'][$wa_device] ) ) {

								$this->mfn_items[$this->s]['wraps'][$this->w]['attr'][$wf_device] = array(
									'val' => $this->mfn_items[$this->s]['wraps'][$this->w]['attr'][$wa_device],
									'css_path' => $wf['css_path'],
									'css_style' => $wf['css_style'].'_'.$device
								);

							}

							unset($this->mfn_items[$this->s]['wraps'][$this->w]['attr'][$wa_device]);
							$this->detect_old_builder = true;

						}

					}
				}


			}

		}

		if( !empty($wrap['items']) && is_iterable($wrap['items']) ){

			foreach( $wrap['items'] as $i=>$item ) {
				if( !empty($item['item_is_wrap']) ) {
					$this->nw = $i;
					$this->nested_wrap($item);
				}else{
					$this->i = $i;
					$this->item($item);
				}
				
			}
		}


	}

	public function nested_wrap($wrap) {
		$mfn_fields = new Mfn_Builder_Fields();
		$wraps_fields = $mfn_fields->get_wrap();

		if( !empty($wrap['attr']) && is_iterable($wrap['attr']) ) {

			foreach ($wraps_fields as $wf) {

				if( !empty($wf['old_id']) && !empty( $this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->nw]['attr'][$wf['old_id']] ) ) {

					$this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->nw]['attr'][$wf['id']] = array(
						'val' => $this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->nw]['attr'][$wf['old_id']],
						'css_path' => $wf['css_path'],
						'css_style' => $wf['css_style']
					);

					unset($this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->nw]['attr'][$wf['old_id']]);
					$this->detect_old_builder = true;

				}

				if( isset($wf['old_id']) && !empty($wf['responsive']) ) {
					foreach( $this->devices as $device ) {

						$wa_device = $wf['old_id'].'_'.$device;
						if( isset( $this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->nw]['attr'][$wa_device] ) ) {

							$wf_device = $wf['id'].'_'.$device;

							if( !empty( $this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->nw]['attr'][$wa_device] ) ) {

								$this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->nw]['attr'][$wf_device] = array(
									'val' => $this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->nw]['attr'][$wa_device],
									'css_path' => $wf['css_path'],
									'css_style' => $wf['css_style'].'_'.$device
								);

							}

							unset($this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->nw]['attr'][$wa_device]);
							$this->detect_old_builder = true;

						}

					}
				}


			}

		}

		if( !empty($wrap['items']) && is_iterable($wrap['items']) ){

			foreach( $wrap['items'] as $i=>$item ) {
				$this->i = $i;
				$this->nested_item($item);
			}
		}
	}

	public function nested_item($item) {
		$mfn_fields = new Mfn_Builder_Fields();
		$items_fields = $mfn_fields->get_items();
		$items_advanced = $mfn_fields->get_advanced(true);


		if( !empty($item['type']) && !empty($items_fields[$item['type']]['attr']) && is_iterable($items_fields[$item['type']]['attr']) ) {

			foreach ($items_fields[$item['type']]['attr'] as $it) {

				if( isset($it['old_id']) && !empty( $this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->nw]['items'][$this->i]['attr'][$it['old_id']] ) ) {

					if( !empty($it['old_id']) && !empty( $this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->nw]['items'][$this->i]['attr'][$it['old_id']] ) ) {

						$this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->nw]['items'][$this->i]['attr'][$it['id']] = array(
							'val' => $this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->nw]['items'][$this->i]['attr'][$it['old_id']],
							'css_path' => $it['css_path'],
							'css_style' => $it['css_style']
						);
					}

					unset($this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->nw]['items'][$this->i]['attr'][$it['old_id']]);
					$this->detect_old_builder = true;

				}

				if( isset($it['old_id']) && !empty($it['responsive']) ) {
					foreach( $this->devices as $device ) {
						$ia_device = $it['old_id'].'_'.$device;
						if( isset( $this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->nw]['items'][$this->i]['attr'][$ia_device] ) ) {

							$if_device = $it['id'].'_'.$device;

							if( !empty( $this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->nw]['items'][$this->i]['attr'][$ia_device] ) ) {

								$this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->nw]['items'][$this->i]['attr'][$if_device] = array(
									'val' => $this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->nw]['items'][$this->i]['attr'][$ia_device],
									'css_path' => $it['css_path'],
									'css_style' => $it['css_style'].'_'.$device
								);

							}

							unset($this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->nw]['items'][$this->i]['attr'][$ia_device]);
							$this->detect_old_builder = true;

						}

					}
				}

			}

			/* Advanced */

			foreach ($items_advanced as $it) {

				if( isset($it['old_id']) && !empty( $this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->nw]['items'][$this->i]['attr'][$it['old_id']] ) ) {

					if( !empty($it['old_id']) && !empty( $this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->nw]['items'][$this->i]['attr'][$it['old_id']] ) ) {
						$this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->nw]['items'][$this->i]['attr'][$it['id']] = array(
							'val' => $this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->nw]['items'][$this->i]['attr'][$it['old_id']],
							'css_path' => $it['css_path'],
							'css_style' => $it['css_style']
						);
					}

					unset($this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->nw]['items'][$this->i]['attr'][$it['old_id']]);
					$this->detect_old_builder = true;

				}

				if( isset($it['old_id']) && !empty($it['responsive']) ) {
					foreach( $this->devices as $device ) {
						$ia_device = $it['old_id'].'_'.$device;
						if( isset( $this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->nw]['items'][$this->i]['attr'][$ia_device] ) ) {

							$if_device = $it['id'].'_'.$device;

							if( !empty( $this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->nw]['items'][$this->i]['attr'][$ia_device] ) ) {

								$this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->nw]['items'][$this->i]['attr'][$if_device] = array(
									'val' => $this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->nw]['items'][$this->i]['attr'][$ia_device],
									'css_path' => $it['css_path'],
									'css_style' => $it['css_style'].'_'.$device
								);

							}

							unset($this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->nw]['items'][$this->i]['attr'][$ia_device]);
							$this->detect_old_builder = true;

						}

					}
				}

			}


		}

	}


	public function item($item) {
		$mfn_fields = new Mfn_Builder_Fields();
		$items_fields = $mfn_fields->get_items();
		$items_advanced = $mfn_fields->get_advanced(true);


		if( !empty($item['type']) && !empty($items_fields[$item['type']]['attr']) && is_iterable($items_fields[$item['type']]['attr']) ) {

			foreach ($items_fields[$item['type']]['attr'] as $it) {

				if( isset($it['old_id']) && !empty( $this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->i]['attr'][$it['old_id']] ) ) {

					if( !empty($it['old_id']) && !empty( $this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->i]['attr'][$it['old_id']] ) ) {
						$this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->i]['attr'][$it['id']] = array(
							'val' => $this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->i]['attr'][$it['old_id']],
							'css_path' => $it['css_path'],
							'css_style' => $it['css_style']
						);
					}

					unset($this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->i]['attr'][$it['old_id']]);
					$this->detect_old_builder = true;

				}

				if( isset($it['old_id']) && !empty($it['responsive']) ) {
					foreach( $this->devices as $device ) {
						$ia_device = $it['old_id'].'_'.$device;
						if( isset( $this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->i]['attr'][$ia_device] ) ) {

							$if_device = $it['id'].'_'.$device;

							if( !empty( $this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->i]['attr'][$ia_device] ) ) {

								$this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->i]['attr'][$if_device] = array(
									'val' => $this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->i]['attr'][$ia_device],
									'css_path' => $it['css_path'],
									'css_style' => $it['css_style'].'_'.$device
								);

							}

							unset($this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->i]['attr'][$ia_device]);
							$this->detect_old_builder = true;

						}

					}
				}

			}

			/* Advanced */

			foreach ($items_advanced as $it) {

				if( isset($it['old_id']) && !empty( $this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->i]['attr'][$it['old_id']] ) ) {

					if( !empty($it['old_id']) && !empty( $this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->i]['attr'][$it['old_id']] ) ) {
						$this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->i]['attr'][$it['id']] = array(
							'val' => $this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->i]['attr'][$it['old_id']],
							'css_path' => $it['css_path'],
							'css_style' => $it['css_style']
						);
					}

					unset($this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->i]['attr'][$it['old_id']]);
					$this->detect_old_builder = true;

				}

				if( isset($it['old_id']) && !empty($it['responsive']) ) {
					foreach( $this->devices as $device ) {
						$ia_device = $it['old_id'].'_'.$device;
						if( isset( $this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->i]['attr'][$ia_device] ) ) {

							$if_device = $it['id'].'_'.$device;

							if( !empty( $this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->i]['attr'][$ia_device] ) ) {

								$this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->i]['attr'][$if_device] = array(
									'val' => $this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->i]['attr'][$ia_device],
									'css_path' => $it['css_path'],
									'css_style' => $it['css_style'].'_'.$device
								);

							}

							unset($this->mfn_items[$this->s]['wraps'][$this->w]['items'][$this->i]['attr'][$ia_device]);
							$this->detect_old_builder = true;

						}

					}
				}

			}


		}

	}


	public function update() {

		if ( 'encode' == $this->builder_storage ) {
			$new = call_user_func('base'.'64_encode', serialize($this->mfn_items));
		}else{
			$new = $this->mfn_items;
		}

		update_post_meta($this->id, 'mfn-page-items', $new);

	}


}