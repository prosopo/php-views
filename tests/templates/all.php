

<div><?php echo $e( $var ); ?></div>

<div><?php echo $e( $output() ); ?></div>

<div><?php echo( $html ); ?></div>

<?php for( $i = 0; $i < 3; $i++ ): ?>item<?php endfor; ?>

<?php for( $i = 0; $i < 3; $i++ ): ?><?php echo $e( $i ); ?><?php endfor; ?>

<?php for( $i = $get(); $i < 3; $i++ ): ?>item<?php endfor; ?>

<?php for( $i = 0; $i < 3; $i++ ): ?>
- item
<?php endfor; ?>

<?php for( $i = 0; $i < 3; $i++ ): ?>
    <?php echo $e( $i ); ?>
<?php endfor; ?>

<?php foreach( $items as $item ): ?>item<?php endforeach; ?>

<?php foreach( $items as $item ): ?><?php echo $e( $item ); ?><?php endforeach; ?>

<?php foreach( $getItems() as $item ): ?>item<?php endforeach; ?>

<?php foreach( $items as $item ): ?>
- item
<?php endforeach; ?>

<?php foreach( $items as $item ): ?>
    <?php echo $e( $item ); ?>
<?php endforeach; ?>

<?php if( $var ): ?>item<?php endif; ?>

<?php if( $var ): ?><?php echo $e( $output ); ?><?php endif; ?>

<?php if( $right() ): ?>item<?php endif; ?>

<?php if( $var ): ?>
-item
<?php endif; ?>

<?php if( $var ): ?>
<?php echo $e( $output ); ?>
<?php endif; ?>

<?php if( $var ): ?>
-first
<?php elseif( $var2 ): ?>
-second
<?php else: ?>
-third
<?php endif; ?>

<?php if( $var ): ?>
<?php echo $e( $output ); ?>
<?php elseif( $var2 ): ?>
<?php echo $e( $output ); ?>
<?php else: ?>
<?php echo $e( $output ); ?>
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

<?php switch($var): ?><?php case( 1 ): ?>
<?php echo $e( $output1 ); ?>
<?php break; ?>
<?php case( 2 ): ?>
<?php echo $e( $output2 ); ?>
<?php break; ?>
<?php default: ?>
<?php echo $e( $output3 ); ?>
<?php endswitch; ?>