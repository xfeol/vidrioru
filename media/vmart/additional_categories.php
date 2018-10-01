<?php

if (in_array($category_id, array(51,58,59,60,61,62,103,104))) {
?>	
	<span class="addinfo">
	Всвязи с нестабильностью курса евро и доллара по отношению к рублю просим Вас уточнять цены у менеджеров перед заказом товаров.
	</span>
<?php
}

if (in_array($category_id, array(110))) {
?>

	<span style="font-size:1.3em;padding-top:15px;display:block;">
		<i class="fa fa-cc-mastercard" style="color:red;"></i>
		<i class="fa fa-cc-visa"></i>
	</span>
	<span class="addinfo" >
		Оплати сейчас и получи бесплатную доставку!<br />Подробности по телефону у наших менеджеров.
	</span>
<?php
}