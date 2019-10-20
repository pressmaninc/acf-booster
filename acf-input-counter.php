<?php
	new acf_input_counter();

class acf_input_counter {
	public function __construct() {
		add_action( 'acf/render_field/type=text', array( $this, 'render_field' ), 20, 1 );
		add_action( 'acf/render_field/type=textarea', array( $this, 'render_field' ), 20, 1 );
		//add_action( 'acf/input/admin_enqueue_scripts', array( $this, 'scripts' ) );
	} // end public function __construct

	private function run() {
		// cannot run on field group editor or it will
		// add code to every ACF field in the editor
		$run = true;
		global $post;
		if ( $post && $post->ID && get_post_type( $post->ID ) == 'acf-field-group' && get_field( 'counter-control', 'option' ) == '1' ) {
			$run = false;
		}
		return $run;
	} // end private function run
	public function render_field( $field ) {
		if ( ! $this->run() ||
			! $field['maxlength'] ||
			! $field['show_count'] == '1' ||
			( $field['type'] != 'text' && $field['type'] != 'textarea' ) ) {
			// only run on text and text area fields when maxlength is set
			return;
		}
		$max = $field['maxlength'];

		$classes = apply_filters( 'acf-input-counter/classes', array() );
		$ids     = apply_filters( 'acf-input-counter/ids', array() );

		$insert = true;
		if ( count( $classes ) || count( $ids ) ) {
			$insert = false;

			$exist = array();
			if ( $field['wrapper']['class'] ) {
				$exist = explode( ' ', $field['wrapper']['class'] );
			}
			$insert = $this->check( $classes, $exist );

			if ( ! $insert && $field['wrapper']['id'] ) {
				$exist = array();
				if ( $field['wrapper']['id'] ) {
					$exist = explode( ' ', $field['wrapper']['id'] );
				}
				$insert = $this->check( $ids, $exist );
			}
		} // end if filter classes or ids

		if ( ! $insert ) {
			return;
		}
		$display = sprintf(
			__( 'limit: %1$s / %2$s', 'acf-counter' ),
			'%%len%%',
			'%%max%%'
		);
		$display = apply_filters( 'acf-input-counter/display', $display );
		$display = str_replace( '%%len%%', '<span class="count">0</span>', $display );
		$display = str_replace( '%%max%%', $max, $display );
		?>
				<span class="char-count">
				<?php
					echo $display;
				?>
				</span>
			<?php
	} // end public function render_field

	private function check( $allow, $exist ) {
		// if there is anything in $allow
		// see if any of those values are in $exist
		$intersect = array_intersect( $allow, $exist );
		if ( count( $intersect ) ) {
			return true;
		}
		return false;
	} // end private function check

} // end class acf_input_counter
