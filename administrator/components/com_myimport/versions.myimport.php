<?php
  ////////////////////////////////////////////////////////
  // Компонент импорта/экспорта товаров для Virtuemart	//
  // Разработан для Joomla 1.5.x 						//
  // 2010 (C) Ребров О.В.   (admin@webplaneta.com.ua)	//
  ////////////////////////////////////////////////////////
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<table width="100%" border="1" cellpadding="10" cellspacing="10" bordercolor="#66CCFF">
  <tr>
    <th bgcolor="#66CCFF"><p align="center"><strong>История версий MyImport</strong></p></th>
  </tr>
	<tr>
<td><p align="left"><strong>MyImport 1.5.6.2:</strong></p><hr color="#66CCFF"/>
<p align="left"><strong style="color:red">Исправлено:</strong></p>
<ul style="padding-left:15px">
	<li>Исправлено ошибка с нулевым размером прайса</li>
	<li>Исправлено ошибка с обновлением производителей у товаров</li>
</ul>
<p align="left"><strong>MyImport 1.5.6.1:</strong></p><hr color="#66CCFF"/>
<p align="left"><strong style="color:green">Добавлено:</strong></p>
<ul style="padding-left:15px">
	<li>Добавлено выбор кодировки исходного файла</li>
	<li>Добавлена история версий</li>
</ul>
<br/>
<p align="left"><strong>MyImport 1.5.6 my birsday edition для Joomla 1.5 Специальная версия в честь моего дня рождения (4 июля)!</strong></p><hr color="#66CCFF"/>
<p align="left"><strong style="color:green">Добавлено:</strong></p>
<ul style="padding-left:15px">
	<li>Переписан полностью алгоритм компонента все перенесено на функции (есть теперь возможность расширяться)</li>
	<li>Добавлена сортировка категорий и товаров</li>
	<li>Добавлена возможность сброса количества на складе</li>
	<li>При отсутствии указанной категории товарам они попадают в категорию unsorted</li>
</ul>
<p align="left"><strong style="color:red">Исправлено:</strong></p>
<ul style="padding-left:15px">
	<li>Устранена проблема с кодировкой</li>
	<li>Устранена проблема с экспортом категорий</li>
</ul>
<br/>
<p align="left"><strong>MyImport 1.5.5 </strong></p><hr color="#66CCFF"/>
<p align="left"><strong style="color:green">Добавлено:</strong></p>
<ul style="padding-left:15px">
	<li>Переписан инсталятор компонента</li>
	<li>Добавлена возможность добавления описания у категорий</li>
	<li>Добавления изображений и мини-изображений категорий</li>
</ul>
<br/>
<p align="left"><strong>MyImport 1.5.4</strong></p><hr color="#66CCFF"/>
<p align="left"><strong style="color:red">Исправлено:</strong></p>
<ul style="padding-left:15px">
	<li>Исправлена ошибка при добавлении изображений к существующим товарам(ранее они только добавлялись к новым)</li>
</ul>
<br/>
<p align="left"><strong>MyImport 1.5.3 </strong></p><hr color="#66CCFF"/>
<p align="left"><strong style="color:green">Добавлено:</strong></p>
<ul style="padding-left:15px">
	<li>Загрузка дочерних товаров (в колонке "товар родитель" напротив дочернего товара ставьте артикул товара родителя)</li>
	<li>Добавлена возможность указания шаблонов для категорий, количество товаров в строке, названия шаблона подробного описания, указание валюты и единицы  измерения веса товара (Эти возможности в параметрах компонента. Вверху справа Azn )</li>
</ul>
<p align="left"><strong style="color:red">Исправлено:</strong></p>
<ul style="padding-left:15px">
	<li>Исправлена ошибка при импорте в категории 3 порядка, а также при некоторых условиях в категории 2 уровня вложенности </li>
</ul>
<br/>
<p align="left"><strong>MyImport 1.5.2</strong></p><hr color="#66CCFF"/>
<p align="left"><strong style="color:green">Добавлено:</strong></p>
<ul style="padding-left:15px">
	<li>Загрузка остатка товара</li>
	<li>Загрузка названий изображений для товаров</li>
	<li>Загрузка производителей</li>
</ul>
<p align="left"><strong style="color:red">Исправлено:</strong></p>
<ul style="padding-left:15px">
	<li>При одинаковом названии категории не происходит конфликта </li>
</ul>
</td>
  </tr>
</table>