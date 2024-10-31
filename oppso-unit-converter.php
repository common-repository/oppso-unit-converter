<?php/*
Plugin Name: Oppso Unit Converter
Plugin URI: http://www.oppso.com/oppso-unit-converter/
Description: Simple to use unit converter. It comes as a shortcode that you can use in a post / page or a text widget. Check the settings page for the shortcodes
Version: 1.1.1
Author: Dan Busuioc
Author URI: http://www.oppso.com/
*/

add_action('admin_menu','oppso_calc_add_menu');

add_filter('widget_text', 'do_shortcode');

add_shortcode('oppso_unit_converter', 'oppso_converter_shortcode');

add_action('wp_enqueue_scripts', 'oppso_scripts');

global $formulae_array ; 
$formulae_array = json_decode(file_get_contents(plugins_url("formulae.opp",__FILE__)),"true");


add_action('wp_head', 'oppso_ajax_url');

function oppso_ajax_url(){
	?>
	<script type="text/javascript">
	var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
	</script>
	<?php 
}


function oppso_scripts(){
	
wp_register_style( 'oppso-converter-style', plugins_url('css/oppso-converter.css', __FILE__) );
wp_enqueue_style( 'oppso-converter-style' );
	wp_enqueue_script(
			'oppso-converter', 
	plugins_url('/js/oppso-converter.js', __FILE__),array('jquery')
	);
}

function oppso_calc_add_menu(){
	add_options_page('Oppso Unit Converter', 'Oppso Unit Converter', 'administrator', __FILE__, 'oppso_converter_show_options');
}
function oppso_converter_show_options(){
global $formulae_array;
?>

<div>
<h1>Unit Converter Plugin</h1>
	A simple unit converter from <a href="">Oppso.com</a><br />
	This is the list of shortcodes that you can use to display the converter. Note that you can use this shortcode both inside a post / page or inside a widget.
	
</div><br />
<table cellpadding="3" cellspacing="3">
<tr>
	<td><strong>All unit converters</strong>
	</td>
	<td>[oppso_unit_converter]
	</td>
</tr>
	<?php 
foreach($formulae_array['formulae'] as $key=>$value){
	
	?>
	<tr>
	<td><strong><?php echo $value['title']; ?></strong>
	</td>
	<td>[oppso_unit_converter converter=<?php echo $key ?>]
	</td>
	</tr>
	<?php }?>
</table>
<!--  
 <form style="float:left" action="options.php" method="post">

 <p class="submit">
     <input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
 </p>
            
            
           
           
               <div style="float:left" id=""><?php oppso_converter_preview() ?></div>
             
</form>-->
	<?php 
}
function oppso_do_change(){
	global $formulae_array;
	$converter = $_POST['converter_type'];
	
	$formulae = $formulae_array;
	$formulae = $formulae['formulae'];
	
	$converter_data = ($formulae[$converter]);
	
	$convert_options_arr = $converter_data['select_box'];
	foreach($convert_options_arr as $key=>$value){
		$convert_options .= '<option value="'.$key.'">'.$value.'</option>';
	}
	
	$show .= '<div>'.$converter_data['title'].'</div>';
	$show .= '<div class="oppso-converter-description">'.$converter_data['description'].'</div>';
	$show .= '<div class="oppso-converter-form">
	
	 	<input type="hidden" name="oppso_converter_type" value="'.$converter.'" id="oppso_converter_type"/>
		<table cellspacing="3" class="oppso-form-table">
	     		<tr><td>Value</td><td>	<input  class="oppso-input"  type="text" name="oppso_value" value="" id="oppso_value" />
	     		</td></tr>
	            		<tr><td width="40%">From</td><td>
		<select class="oppso-select" id="oppso_from">'.$convert_options.'</select>
		</td></tr>';
	$show .= '<tr><td  width="40%">To</td><td>
			<select class="oppso-select" id="oppso_to">'.$convert_options.'</select></td></tr>
			
		<tr><td><input type="button" name="convert" value="Convert" id="oppso_convert"></td></tr></table>
		<div id="oppso_convert_result" class="oppso-convert-result"></div>
	
				
		';
	$show .= '';
	echo $show;
	die();
}
function oppso_do_convert(){
	
	global $formulae_array;
	$from = $_POST['from'];
	$to = $_POST['to'];
	$converter_type = $_POST['converter_type'];
	$value = $_POST['oppso_value'];
	
	
	$formulae = $formulae_array;
	$formulae = $formulae['formulae'];
	
	$converter_data = ($formulae[$converter_type]);
	$converter_base_unit = $converter_data['base_unit'];
	$converter_to = $converter_data['convert_to'];
	if($converter_type != 'temperature'){
		$converted_value = $value*$converter_to[$from]*$converter_to[$converter_base_unit]/$converter_to[$to];
	}
	else{
		$converted_value =	(($value-$converter_to[$from]['base'])/$converter_to[$from]['ratio'])*$converter_to[$to]['ratio'] + $converter_to[$to]['base']; 

	}
	echo json_encode(round($converted_value,8));
	die();
	
}
function oppso_converter_shortcode($atts){
	global $formulae_array;
	wp_enqueue_script('jquery');
	extract( shortcode_atts( array(
						'converter' => '',
	
	
	), $atts ) );
	$formulae = $formulae_array;
	$formulae = $formulae['formulae'];
	$show.='<div id="oppso-converter-box">';
	if($converter==''){
		$options = '';
		$i=0;
		foreach($formulae as $key=>$value){
			if($i==0){
				$converter = $key;
			}
			$i++;
			$options.='<option value="'.$key.'">'.ucfirst($key).'</option>';
		}
		$show.='<div id="converter-selection"><select class="oppso-select">'.$options.'</select></div>';
	}
	
	
	$converter_data = ($formulae[$converter]);
	
	$convert_options_arr = $converter_data['select_box'];
	foreach($convert_options_arr as $key=>$value){
		$convert_options .= '<option value="'.$key.'">'.$value.'</option>';
	}
	
	$show .= '<div id="oppso-converter-type"><div>'.$converter_data['title'].'</div>';
	$show .= '<div class="oppso-converter-description">'.$converter_data['description'].'</div>';
	$show .= '<div class="oppso-converter-form">

 	<input type="hidden" name="oppso_converter_type" value="'.$converter.'" id="oppso_converter_type"/>
	<table cellspacing="3" class="oppso-form-table">
     		<tr><td>Value</td><td>	<input  class="oppso-input"  type="text" name="oppso_value" value="" id="oppso_value" />
     		</td></tr>
            		<tr><td width="40%">From</td><td>
	<select class="oppso-select" id="oppso_from">'.$convert_options.'</select>
	</td></tr>';
	$show .= '<tr><td  width="40%">To</td><td>
		<select class="oppso-select" id="oppso_to">'.$convert_options.'</select></td></tr>
		
	<tr><td><input type="button" name="convert" value="Convert" id="oppso_convert"></td></tr></table>
	<div id="oppso_convert_result" class="oppso-convert-result"></div>

			
	';
	$show .= '</div><div>';
	
	return $show;
}
function oppso_converter_preview(){
	echo oppso_converter_shortcode(array("converter"=>"distance"));
}

add_action('wp_ajax_nopriv_oppso_do_convert', 'oppso_do_convert');
add_action('wp_ajax_oppso_do_convert', 'oppso_do_convert');
add_action('wp_ajax_nopriv_oppso_do_change', 'oppso_do_change');
add_action('wp_ajax_oppso_do_change', 'oppso_do_change');
?>