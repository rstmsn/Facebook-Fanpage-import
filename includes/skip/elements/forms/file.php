<?php
/**
 * Skip Fileupload field
 * @package Skip\Forms
 * @since 1.0
 * @ignore
 */
 
namespace skip\v1_0_0;


class File extends Form_Element{
	
	var $delete;
	var $wp_browse;
	
	/**
	 * Constructor
	 * @since 1.0
	 * @param string $name Name of File Field.
	 * @param array/string $args List of Arguments.
	 */
	function __construct( $name, $label = FALSE, $args = array() ){
		
		$defaults = array(
			'delete' => FALSE,
			'save' => TRUE
		);
		
		$args = wp_parse_args( $args, $defaults );
		$this->delete = $args[ 'delete' ];
		
		$args[ 'close_tag' ] = FALSE; // No Close tag for Input type Text
		$args[ 'label' ] = $label;
		
		parent::__construct( 'input', $name, $args );

		$this->del_param( 'value' ); // Not needed here
		$this->add_param( 'type', 'file' ); // This is a text field!
		
	}
	
	/**
	 * Rendering Editor field
	 * @package Skip
	 * @since 1.0
	 * @return string $html Returns The HTML Code.
	 */	
	public function render(){
		global $skip_javascripts;
		
		$file_url = $this->value[ 'url' ];
		$file_path = $this->value[ 'file' ];
		
		$skip_javascripts[] = '	
			$( "#skip_filepreview_' . $this->params[ 'id' ] . '" ).attr( "src", $( "#skip_filename_' . $this->params[ 'id' ] . ' a" ).attr( "href" ) );
		';
		
		$html_before = '<div class="skip_file ui-state-default ui-corner-all">';
				$html_before.= '<div class="skip_filepreview">';
					$html_before.= '<img id="skip_filepreview_' . $this->params[ 'id' ] . '" class="skip_filepreview_image" />';
					if( isset( $this->value[ 'url' ] ) ) 
						$html_before.= '<div class="skip_filename" id="skip_filename_' . $this->params[ 'id' ] . '"><a href="' . $file_url . '" target="_blank">' . basename( $file_path ) . '</a></div>';
			
				$html_before.= '</div>';
			$html_before.= '<div class="skip_fileuploader">';
		
		$html_after = '</div></div>';
		
		$this->before( $html_before );
		$this->after( $html_after );
		
		$this->add_param( 'class', 'skip_file_fileinput' );
		
		return parent::render();
	}

	/**
	 * Saving Editor field
	 * @package Skip
	 * @since 1.0
	 */	
	public function save(){
		
		if( array_key_exists( 'CONTENT_LENGTH', $_SERVER ) )
			if( $_SERVER['CONTENT_LENGTH'] > max_upload() )
				$this->error_msgs[] = $this->errors[ 2 ];
			
		if( array_key_exists( $this->form_name . '_value', $_FILES ) ):
		
			if( file_exists( $_FILES[ $this->form_name . '_value' ][ 'tmp_name' ][ $this->name ] ) ):
				
				$file[ 'name' ] = $_FILES[ $this->form_name . '_value' ][ 'name' ][ $this->name ];
				$file[ 'type' ] = $_FILES[ $this->form_name . '_value' ][ 'type' ][ $this->name ];
				$file[ 'tmp_name' ] = $_FILES[ $this->form_name . '_value' ][ 'tmp_name' ][ $this->name ];
				$file[ 'error' ] = $_FILES[ $this->form_name . '_value' ][ 'error' ][ $this->name ];
				$file[ 'size' ] = $_FILES[ $this->form_name . '_value' ][ 'size' ][ $this->name ];
				
				$file_before = $this->value();
				
				if( file_exists( $file_before[ 'file' ] ) )
					unlink( $file_before[ 'file' ] );
					
				$override = array(
					'test_form' => FALSE,
					'action' => 'update'
				);
				
				$wp_file = wp_handle_upload( $file, $override );
				
				@unlink( $file[ 'tmp_name' ] );
				
				update_option( $this->option_name, $wp_file );
			endif;
					
		endif;
	}
}

/**
 * Fileupload getter Function
 * @see skip_file()
 * @ignore
 */
function get_file( $name, $label = FALSE, $args = array(), $return = 'html' ){
	$file= new File( $name, $label, $args );
	return $file->render();
}

/**
 * <pre>skip_file( $name, $args )</pre>
 * 
 * Adding a File Field.
 * 
 * <b>Default Usage</b>
 * <code>
 * skip_file( 'myfile' );
 * </code>
 * This will create an automated saved file field.
 * 
 * <b>Parameters</b>
 * 
 * <code>
 * $name // (string) (required) The name of the field.
 * $args // (array/string) (optional) Values for further settings.
 * </code>
 * 
 * <b>$args Settings</b>
 * 
 * <ul>
 * 	<li>id (string) ID if the HTML Element.</li> 
 * 	<li>label  (string) Label for Element.</li> 
 *  <li>max_bytes (int) Size for max upload in Bytes (default is post_max_size from php.ini)
 * 	<li>classes (string) Name of CSS Classes which will be inserted into HTML seperated by empty space.</li>
 * 	<li>before_element (string) Content before the element.</li>
 *	<li>after_element (string) Content after the element.</li>
 * 	<li>save (boolean) TRUE if value of field have to be saved in Database, FALSE if not (default TRUE).</li>
 * </ul>
 * 
 * <b>Example</b>
 * 
 * Creating a labeled WordPress Upload field in an automatic saved form.
 * <code>
 * skip_form_start( 'myformname' );
 * 
 * $args = array(
 * 	'id' = 'myelementid',
 * 	'label' => 'My File'
 * );
 * skip_file( 'myfile', $args );
 * 
 * skip_form_end();
 * </code>
 * 
 * Getting back the saved data.
 * <code>
 * $filename = skip_value( 'myformname', 'myfile' );
 * </code>
 * @package Skip\Forms
 * @since 1.0
 * @param string $name Name of File field.
 * @param array/string $args List of Arguments.
 */
function file( $name, $label = FALSE, $args = array() ){
	echo get_file( $name, $label, $args );
}