<?php echo "class=\"";
ob_start();
foreach ( ['first', 'second' => $var] as $key => $value ) {
if ( true === is_int( $key ) ) { echo $e($value) . " "; }
else { if ( true === $value ) { echo $e($key) . " "; } }
}
echo trim( (string)ob_get_clean() );
echo "\""; ?>