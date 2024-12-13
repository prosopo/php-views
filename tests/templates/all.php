

<div><?php echo $e( $var ); ?></div>

<div><?php echo( $html ); ?></div>

<?php for( $i = 0; $i < 3; $i++ ): ?>item<?php endfor; ?>

<?php for( $i = 0; $i < 3; $i++ ): ?>
- item
<?php endfor; ?>

<?php foreach( $items as $item ): ?>item<?php endforeach; ?>

<?php foreach( $items as $item ): ?>
- item
<?php endforeach; ?>

<?php if( $var ): ?>item<?php endif; ?>

<?php if( $var ): ?>
-item
<?php endif; ?>

<?php if( $var ): ?>
-first
<?php elseif( $var2 ): ?>
-second
<?php else: ?>
-third
<?php endif; ?>

<?php
$a = 1;
?>

<?php use my\package; ?>

<?php use my\package; ?>

<?php if ( $var ) echo "selected=\"\""; ?>

<?php if ( $var ) echo "checked=\"\""; ?>

<?php echo "class=\"";
ob_start();
foreach ( ['first',
'second' => $var] as $key => $value ) {
if ( true === is_int( $key ) ) { echo $e($value) . " "; }
else { if ( true === $value ) { echo $e($key) . " "; } }
}
echo trim( (string)ob_get_clean() );
echo "\""; ?>

<?php switch($var): ?><?php case( 1 ): ?>
- first
<?php break; ?>
<?php case( 2 ): ?>
- second
<?php break; ?>
<?php default: ?>
- default
<?php endswitch; ?>