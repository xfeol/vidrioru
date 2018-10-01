<?php

unset ($_SESSION['products_info']);
$prods = new CProducts();
$prods->get_attributes(1407);
print_r($prods->get_product_info(1407));
