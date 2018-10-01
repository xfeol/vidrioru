<?php

function table_header()
{
?>

<span style="font-size:medium;font-family:serif;font-style:italic;color:#740000;">Варианты исполнения</span>
<br />
<br />
<table width="500px" border="0" cellspacing="15" cellpadding="0" style="border-collapse:collapse; border-spacing: 0 10px;">
<tbody>

<tr height="20" style="border-bottom: 1px solid #EAEAEA;">

<td width="100" align="center" valign="top">Каркас<br /></td>
<td width="150" align="center" valign="top">Столешница<br /></td>
<td width="150" align="center" valign="top">Габариты, ШxГxВ<br /></td>
<td width="150" align="center" valign="top">Раскладывается<br /></td>
</tr>
<?php
}


function table_row($p0, $p1, $p2, $p3, $p4, $p5, $p6, $p7,$p8,$p9)
{
?>
<tr height="65" valign="bottom" style="border-bottom: 1px solid #EAEAEA;">
<td align = "middle">
<a href="http://vidrio.ru/images/<?php echo $p2 ?>" title="<?php echo $p3 ?>" rel="lightbox"><img src="http://vidrio.ru/images/<?php echo $p0 ?>" title="<?php echo $p1 ?>" /></a><br />
<?php echo $p1 ?>
</td>

<td align="middle">
<a href="http://vidrio.ru/images/<?php echo $p6 ?>" title="<?php echo $p7 ?>" rel="lightbox"><img src="http://vidrio.ru/images/<?php echo $p4 ?>" title="<?php echo $p5 ?>" /></a><br />
<?php echo $p5 ?>
</td>

<td align="center" valign="middle">
<?php echo $p8 ?>
</td>

<td align="center" valign="middle">
<?php echo $p9 ?>
</td>


</tr>
<?php
}


function table_bottom()
{
?>
</tbody>
</table>

<?php
}

if ($childs_info[0] == 0)
{

    if (isset($product_type['parameters']))
    {
	$param = array();
	foreach($product_type['parameters'] as $pid => $values)
	{
	    $param[] = $values['parameter_value'];
	}
	table_header();
	table_row($param[0], $param[1], $param[2], $param[3], $param[4], $param[5], $param[6], $param[7], $param[8], $param[9]);
	table_bottom();
	
    }
} else {
    table_header();
    foreach($childs_info[1] as $index => $values)
    {
	$param = array();
	foreach($values['types'] as $id => $type)
	{
	    foreach($type['parameters'] as $num => $parameter)
	    {
		$param[] = $parameter['parameter_value'];

	    }
	}
	table_row($param[0], $param[1], $param[2], $param[3], $param[4], $param[5], $param[6], $param[7], $param[8], $param[9]);
	
    }
    table_bottom();
}

