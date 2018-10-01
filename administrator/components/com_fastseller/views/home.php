<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class fsViewHOME{

	static function printStats($d){
?>
<div class="hm-stats">
<h2>Stats</h2>
You have <b><?php echo $d['total_products'] ?></b> products total. 
<?php 
	if( $d['total_products'] ) {
?>
Out of them:
<ul>
<li><b><?php echo $d['parents'] ?></b> Parent (<?php echo $d['parent_published'] ?> published and <?php echo ($d['parents']-$d['parent_published']) ?> unpublished)</li>
<li><b><?php echo $d['children'] ?></b> Children (<?php echo $d['children_published'] ?> published and <?php echo ($d['children']-$d['children_published']) ?> unpublished)</li>
</ul>
<?php
	}
?>
<br/>
You have <b><?php echo $d['pt_count'] ?></b> Product Type<?php if($d['pt_count']!=1) echo 's' ?>.
<?php if($d['pt_count']>0) { ?>
<table class="hm-pt">
<tr class="hm-ptttl"><td>No.</td><td>Id</td><td>Name</td><td>Products Assigned<br/><span class="small">(to the Product Type)</span></td><td>Products that have Filters assigned</td></tr>
<?php
$t1=0;
$t2=0;
for($i=0; $i<$d['pt_count']; $i++){
	$t1+=$d[$i]['products_assigned'];
	$t2+=$d[$i]['values_assigned'];
?>
	<tr><td><?php echo ($i+1) ?></td><td><?php echo $d[$i]['id'] ?></td><td><?php echo $d[$i]['name'] ?></td><td><?php echo $d[$i]['products_assigned'] ?></td><td><?php echo $d[$i]['values_assigned'] ?></td></tr>
<?php }
if($d['pt_count']>1){
?>
<tr class="hm-tptot"><td> </td><td> </td><td>Totals</td><td><?php echo $t1 ?></td><td><?php echo $t2 ?></td></tr>
<?php } ?>
</table>
<?php } else {?>
<div style="color:#F700A3">Since you do not have filters yet and in order to have what to assign to products you need to create these filters first.
<br/>So please start from <b>Create Filters</b> tab.</div>
<?php } ?>
</div>
<?php
	}

}

?>