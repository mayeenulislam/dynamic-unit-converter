<?php

/**
 * converter Class
 * to handle various types of conversion
 */
class Converter {

	public $amount;
	public $unit;
	public $dynamic;
	public $result = array();

	public function __construct( $amount, $unit, $dynamic = FALSE ) {
		$this->amount = $amount;
		$this->unit = $unit;
		$this->dynamic = $dynamic;
	}

	public function set_weight( $amount, $unit, $dynamic = FALSE ) {
		$this->amount = $amount;
		$this->unit = $unit;
		$this->dynamic = $dynamic;
	}

	public function calculate() {

		$amount = $this->amount;
		$unit = $this->unit;
		$dynamic = $this->dynamic;

		$conv_amount = '';
		if( $dynamic != FALSE ) {
			$conv_amount = $dynamic;
		}
		function check( $conv_amount, $fixed_amount ) {
			if( empty($conv_amount) ) {
				return $fixed_amount;
			} else {
				return $conv_amount;
			}
		}

		switch ($unit) {
			case 'mg':
				$mg = $amount;
				$g = $mg / check($conv_amount, 1000);
				$kg = $g / check($conv_amount, 1000);
				break;

			case 'g':
				$mg = $amount * check($conv_amount, 1000);
				$g = $amount;
				$kg = $g / check($conv_amount, 1000);
				break;

			case 'kg':
				$mg = ( $amount * check($conv_amount, 1000) ) * check($conv_amount, 1000);
				$g = $amount * check($conv_amount, 1000);
				$kg = $amount;
				break;
			
			default:
				# code...
				break;
		}

		//Default value setting
		$mg = !empty( $mg ) ? $mg : 0;
		$g  = !empty( $g ) ? $g : 0;
		$kg = !empty( $kg ) ? $kg : 0;

		//Finalizing the result
		$this->result = array (
			'mg'	=> $mg,
			'g'		=> $g,
			'kg'	=> $kg,
		);
	}

	public function get_weight(){
		return $this->result;
	}

}
?>
<div style="font: 100% Calibri, Arial, Helvetica, sans-serif;">
	<h2>Dynamic Unit Converter (Weight)</h2>
	<p>A unit converter to intercept the default conversion unit into any custom value; i.e. 1kg = 1000gm, but we want it to be 1kg = 1200gm, simply choose the unit and pass your dynamic value to convert any amount.</p>
	<hr>
	<form action="" method="POST">
		<label for="user-amount">Amount: </label>
		<input type="text" id="user-amount" name="user_amount" placeholder="The amount" value="" autocomplete="off">

		<select id="user-unit" name="user_unit">
			<option value="">Select unit</option>
			<option value="mg">Milligram (mg)</option>
			<option value="g">Gram (g)</option>
			<option value="kg">Killogram (kg)</option>
		</select>

		<input type="text" id="dynamic-unit" name="dynamic_unit" placeholder="Dynamic amount (optional)" value="" autocomplete="off">

		<input type="submit" name="submit" value="Submit">
	</form>

<?php

/**
 * Output the data
 */
if( isset( $_POST['submit'] ) ) {
	$user_amount = $_POST['user_amount'];
	$user_unit = $_POST['user_unit'];
	$dynamic_unit = !empty( $_POST['dynamic_unit'] ) ? $_POST['dynamic_unit'] : FALSE;

	$unit_in_action = new Converter( $user_amount, $user_unit, $dynamic_unit );
	$unit_in_action->calculate();

	echo '<h3>Showing output for: <span style="color: red;">'. $user_amount . $user_unit .'</span></h3>';
	echo '<pre>';
	print_r( $unit_in_action->get_weight() );
	echo '</pre>';

	echo '<h4>Human Readable Format (using <code>printf()</code>)</h4>';
	echo '<strong>mg:</strong> '; printf( '%f', $unit_in_action->get_weight()['mg'] ); echo '<br>';
	echo '<strong>g:</strong> '; printf( '%f', $unit_in_action->get_weight()['g'] ); echo '<br>';
	echo '<strong>kg:</strong> '; printf( '%f', $unit_in_action->get_weight()['kg'] );

	/**
	 * Alternative ways
	 */
	echo '<h4>Human Readable Format (using <code>number_format()</code>)</h4>';
	echo '<strong>mg:</strong> ', number_format( $unit_in_action->get_weight()['mg'], 6 ), '<br>';
	echo '<strong>g:</strong> ', number_format( $unit_in_action->get_weight()['g'], 6 ), '<br>';
	echo '<strong>kg:</strong> ', number_format( $unit_in_action->get_weight()['kg'], 6 );
}
?>
</div>