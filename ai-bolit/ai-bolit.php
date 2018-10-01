<?php
///////////////////////////////////////////////////////////////////////////
// Created and developed by Greg Zemskov, Revisium Company
// Email: audit@revisium.com, http://revisium.com/ai/

// Commercial usage is not allowed without a license purchase or written permission of the author
// Source code and signatures usage is not allowed

// Certificated in Federal Institute of Industrial Property in 2012
// http://revisium.com/ai/i/mini_aibolit.jpg

////////////////////////////////////////////////////////////////////////////
// Запрещено использование скрипта в коммерческих целях без приобретения лицензии.
// Запрещено использование исходного кода скрипта и сигнатур.
//
// По вопросам приобретения лицензии обращайтесь в компанию "Ревизиум": http://www.revisium.com
// audit@revisium.com
// На скрипт получено авторское свидетельство в Роспатенте
// http://revisium.com/ai/i/mini_aibolit.jpg
///////////////////////////////////////////////////////////////////////////
ini_set('memory_limit', '1G');
ini_set('xdebug.max_nesting_level', 500);

$int_enc = @ini_get('mbstring.internal_encoding');
        
define('SHORT_PHP_TAG', strtolower(ini_get('short_open_tag')) == 'on' || strtolower(ini_get('short_open_tag')) == 1 ? true : false);

// Put any strong password to open the script from web
// Впишите вместо put_any_strong_password_here сложный пароль	 

define('PASS', 'geak0KIL'); 

//////////////////////////////////////////////////////////////////////////

if (isCli()) {
	if (strpos('--eng', $argv[$argc - 1]) !== false) {
		  define('LANG', 'EN');  
	}
} else {
   define('NEED_REPORT', true);
}
	
if (!defined('LANG')) {
   define('LANG', 'RU');  
}	

// put 1 for expert mode, 0 for basic check and 2 for paranoic mode
// установите 1 для режима "Эксперта", 0 для быстрой проверки и 2 для параноидальной проверки (для лечения сайта) 
define('AI_EXPERT_MODE', 1); 

define('REPORT_MASK_PHPSIGN', 1);
define('REPORT_MASK_SPAMLINKS', 2);
define('REPORT_MASK_DOORWAYS', 4);
define('REPORT_MASK_SUSP', 8);
define('REPORT_MASK_CANDI', 16);
define('REPORT_MASK_WRIT', 32);
define('REPORT_MASK_FULL', REPORT_MASK_PHPSIGN | REPORT_MASK_DOORWAYS | REPORT_MASK_SUSP
/* <-- remove this line to enable "recommendations"  

| REPORT_MASK_SPAMLINKS 

 remove this line to enable "recommendations" --> */
);

define('AI_HOSTER', 0); 

define('AI_EXTRA_WARN', 0);

$defaults = array(
	'path' => dirname('/home/vidrioru/public_html'),
	'scan_all_files' => (AI_EXPERT_MODE == 2), // full scan (rather than just a .js, .php, .html, .htaccess)
	'scan_delay' => 0, // delay in file scanning to reduce system load
	'max_size_to_scan' => '600K',
	'site_url' => 'http://vidrio.ru', // website url
	'no_rw_dir' => 0,
    	'skip_ext' => '',
        'skip_cache' => false,
	'report_mask' => REPORT_MASK_FULL
);

define('DEBUG_MODE', 0);
define('DEBUG_PERFORMANCE', 0);

define('AIBOLIT_START_TIME', time());
define('START_TIME', microtime(true));

define('DIR_SEPARATOR', '/');

define('AIBOLIT_MAX_NUMBER', 200);

define('DOUBLECHECK_FILE', 'AI-BOLIT-DOUBLECHECK.php');

if ((isset($_SERVER['OS']) && stripos('Win', $_SERVER['OS']) !== false)/* && stripos('CygWin', $_SERVER['OS']) === false)*/) {
   define('DIR_SEPARATOR', '\\');
}

$g_SuspiciousFiles = array('cgi', 'pl', 'o', 'so', 'py', 'sh', 'phtml', 'php3', 'php4', 'php5', 'php6', 'php7', 'pht', 'shtml');
$g_SensitiveFiles = array_merge(array('php', 'js', 'htaccess', 'html', 'htm', 'tpl', 'inc', 'css', 'txt', 'sql', 'ico', '', 'susp', 'suspected', 'zip', 'tar'), $g_SuspiciousFiles);
$g_CriticalFiles = array('php', 'htaccess', 'cgi', 'pl', 'o', 'so', 'py', 'sh', 'phtml', 'php3', 'php4', 'php5', 'php6', 'php7', 'pht', 'shtml', 'susp', 'suspected', 'infected', 'vir', 'ico', '');
$g_CriticalEntries = '^\s*<\?php|^\s*<\?=|^#!/usr|^#!/bin|\beval|assert|base64_decode|\bsystem|create_function|\bexec|\bpopen|\bfwrite|\bfputs|file_get_|call_user_func|file_put_|\$_REQUEST|ob_start|\$_GET|\$_POST|\$_SERVER|\$_FILES|\bmove|\bcopy|\barray_|reg_replace|\bmysql_|\bchr|fsockopen|\$GLOBALS|sqliteCreateFunction';
$g_VirusFiles = array('js', 'html', 'htm', 'suspicious');
$g_VirusEntries = '<\s*script|<\s*iframe|<\s*object|<\s*embed|fromCharCode|setTimeout|setInterval|location\.|document\.|window\.|navigator\.|\$(this)\.';
$g_PhishFiles = array('js', 'html', 'htm', 'suspected', 'php', 'pht', 'php7');
$g_PhishEntries = '<\s*title|<\s*html|<\s*form|<\s*body|bank|account';
$g_ShortListExt = array('php', 'php3', 'php4', 'php5', 'php6', 'php7', 'pht', 'html', 'htm', 'phtml', 'shtml', 'khtml', '', 'ico', 'txt');

if (LANG == 'RU') {
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// RUSSIAN INTERFACE
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$msg1 = "\"Отображать по _MENU_ записей\"";
$msg2 = "\"Ничего не найдено\"";
$msg3 = "\"Отображается c _START_ по _END_ из _TOTAL_ файлов\"";
$msg4 = "\"Нет файлов\"";
$msg5 = "\"(всего записей _MAX_)\"";
$msg6 = "\"Поиск:\"";
$msg7 = "\"Первая\"";
$msg8 = "\"Предыдущая\"";
$msg9 = "\"Следующая\"";
$msg10 = "\"Последняя\"";
$msg11 = "\": активировать для сортировки столбца по возрастанию\"";
$msg12 = "\": активировать для сортировки столбцов по убыванию\"";

define('AI_STR_001', 'Отчет сканера <a href="https://revisium.com/ai/">AI-Bolit</a> v@@VERSION@@:');
define('AI_STR_002', 'Обращаем внимание на то, что большинство CMS <b>без дополнительной защиты</b> рано или поздно <b>взламывают</b>.<p> Компания <a href="https://revisium.com/">"Ревизиум"</a> предлагает услугу превентивной защиты сайта от взлома с использованием уникальной <b>процедуры "цементирования сайта"</b>. Подробно на <a href="https://revisium.com/ru/client_protect/">странице услуги</a>. <p>Лучшее лечение &mdash; это профилактика.');
define('AI_STR_003', 'Не оставляйте файл отчета на сервере, и не давайте на него прямых ссылок с других сайтов. Информация из отчета может быть использована злоумышленниками для взлома сайта, так как содержит информацию о настройках сервера, файлах и каталогах.');
define('AI_STR_004', 'Путь');
define('AI_STR_005', 'Изменение свойств');
define('AI_STR_006', 'Изменение содержимого');
define('AI_STR_007', 'Размер');
define('AI_STR_008', 'Конфигурация PHP');
define('AI_STR_009', "Вы установили слабый пароль на скрипт AI-BOLIT. Укажите пароль не менее 8 символов, содержащий латинские буквы в верхнем и нижнем регистре, а также цифры. Например, такой <b>%s</b>");
define('AI_STR_010', "Сканер AI-Bolit запускается с паролем. Если это первый запуск сканера, вам нужно придумать сложный пароль и вписать его в файле ai-bolit.php в строке №34. <p>Например, <b>define('PASS', '%s');</b><p>
После этого откройте сканер в браузере, указав пароль в параметре \"p\". <p>Например, так <b>http://mysite.ru/ai-bolit.php?p=%s</b>. ");
define('AI_STR_011', 'Текущая директория не доступна для чтения скрипту. Пожалуйста, укажите права на доступ <b>rwxr-xr-x</b> или с помощью командной строки <b>chmod +r имя_директории</b>');
define('AI_STR_012', "Затрачено времени: <b>%s</b>. Сканирование начато %s, сканирование завершено %s");
define('AI_STR_013', 'Всего проверено %s директорий и %s файлов.');
define('AI_STR_014', '<div class="rep" style="color: #0000A0">Внимание, скрипт выполнил быструю проверку сайта. Проверяются только наиболее критические файлы, но часть вредоносных скриптов может быть не обнаружена. Пожалуйста, запустите скрипт из командной строки для выполнения полного тестирования. Подробнее смотрите в <a href="https://revisium.com/ai/faq.php">FAQ вопрос №10</a>.</div>');
define('AI_STR_015', '<div class="title">Критические замечания</div>');
define('AI_STR_016', 'Эти файлы могут быть вредоносными или хакерскими скриптами');
define('AI_STR_017', 'Вирусы и вредоносные скрипты не обнаружены.');
define('AI_STR_018', 'Эти файлы могут быть javascript вирусами');
define('AI_STR_019', 'Обнаружены сигнатуры исполняемых файлов unix и нехарактерных скриптов. Они могут быть вредоносными файлами');
define('AI_STR_020', 'Двойное расширение, зашифрованный контент или подозрение на вредоносный скрипт. Требуется дополнительный анализ');
define('AI_STR_021', 'Подозрение на вредоносный скрипт');
define('AI_STR_022', 'Символические ссылки (symlinks)');
define('AI_STR_023', 'Скрытые файлы');
define('AI_STR_024', 'Возможно, каталог с дорвеем');
define('AI_STR_025', 'Не найдено директорий c дорвеями');
define('AI_STR_026', 'Предупреждения');
define('AI_STR_027', 'Подозрение на мобильный редирект, подмену расширений или автовнедрение кода');
define('AI_STR_028', 'В не .php файле содержится стартовая сигнатура PHP кода. Возможно, там вредоносный код');
define('AI_STR_029', 'Дорвеи, реклама, спам-ссылки, редиректы');
define('AI_STR_030', 'Непроверенные файлы - ошибка чтения');
define('AI_STR_031', 'Невидимые ссылки. Подозрение на ссылочный спам');
define('AI_STR_032', 'Невидимые ссылки');
define('AI_STR_033', 'Отображены только первые ');
define('AI_STR_034', 'Подозрение на дорвей');
define('AI_STR_035', 'Скрипт использует код, который часто встречается во вредоносных скриптах');
define('AI_STR_036', 'Директории из файла .adirignore были пропущены при сканировании');
define('AI_STR_037', 'Версии найденных CMS');
define('AI_STR_038', 'Большие файлы (больше чем %s). Пропущено');
define('AI_STR_039', 'Не найдено файлов больше чем %s');
define('AI_STR_040', 'Временные файлы или файлы(каталоги) - кандидаты на удаление по ряду причин');
define('AI_STR_041', 'Потенциально небезопасно! Директории, доступные скрипту на запись');
define('AI_STR_042', 'Не найдено директорий, доступных на запись скриптом');
define('AI_STR_043', 'Использовано памяти при сканировании: ');
define('AI_STR_044', 'Просканированы только файлы, перечисленные в ' . DOUBLECHECK_FILE . '. Для полного сканирования удалите файл ' . DOUBLECHECK_FILE . ' и запустите сканер повторно.');
define('AI_STR_045', '<div class="rep">Внимание! Выполнена экспресс-проверка сайта. Просканированы только файлы с расширением .php, .js, .html, .htaccess. В этом режиме могут быть пропущены вирусы и хакерские скрипты в файлах с другими расширениями. Чтобы выполнить более тщательное сканирование, поменяйте значение настройки на <b>\'scan_all_files\' => 1</b> в строке 50 или откройте сканер в браузере с параметром full: <b><a href="ai-bolit.php?p=' . PASS . '&full">ai-bolit.php?p=' . PASS . '&full</a></b>. <p>Не забудьте перед повторным запуском удалить файл ' . DOUBLECHECK_FILE . '</div>');
define('AI_STR_050', 'Замечания и предложения по работе скрипта и не обнаруженные вредоносные скрипты присылайте на <a href="mailto:ai@revisium.com">ai@revisium.com</a>.<p>Также будем чрезвычайно благодарны за любые упоминания скрипта AI-Bolit на вашем сайте, в блоге, среди друзей, знакомых и клиентов. Ссылочку можно поставить на <a href="https://revisium.com/ai/">https://revisium.com/ai/</a>. <p>Если будут вопросы - пишите <a href="mailto:ai@revisium.com">ai@revisium.com</a>. ');
define('AI_STR_051', 'Отчет по ');
define('AI_STR_052', 'Эвристический анализ обнаружил подозрительные файлы. Проверьте их на наличие вредоносного кода.');
define('AI_STR_053', 'Много косвенных вызовов функции');
define('AI_STR_054', 'Подозрение на обфусцированные переменные');
define('AI_STR_055', 'Подозрительное использование массива глобальных переменных');
define('AI_STR_056', 'Дробление строки на символы');
define('AI_STR_057', 'Сканирование выполнено в экспресс-режиме. Многие вредоносные скрипты могут быть не обнаружены.<br> Рекомендуем проверить сайт в режиме "Эксперт" или "Параноидальный". Подробно описано в <a href="https://revisium.com/ai/faq.php">FAQ</a> и инструкции к скрипту.');
define('AI_STR_058', 'Обнаружены фишинговые страницы');

define('AI_STR_059', 'Мобильных редиректов');
define('AI_STR_060', 'Вредоносных скриптов');
define('AI_STR_061', 'JS Вирусов');
define('AI_STR_062', 'Фишинговых страниц');
define('AI_STR_063', 'Исполняемых файлов');
define('AI_STR_064', 'IFRAME вставок');
define('AI_STR_065', 'Пропущенных больших файлов');
define('AI_STR_066', 'Ошибок чтения файлов');
define('AI_STR_067', 'Зашифрованных файлов');
define('AI_STR_068', 'Подозрительных (эвристика)');
define('AI_STR_069', 'Символических ссылок');
define('AI_STR_070', 'Скрытых файлов');
define('AI_STR_072', 'Рекламных ссылок и кодов');
define('AI_STR_073', 'Пустых ссылок');
define('AI_STR_074', 'Сводный отчет');
define('AI_STR_075', 'Сканер бесплатный только для личного некоммерческого использования. Информация по <a href="https://revisium.com/ai/faq.php#faq11" target=_blank>коммерческой лицензии</a> (пункт №11). <a href="https://revisium.com/images/mini_aibolit.jpg">Авторское свидетельство</a> о гос. регистрации в РосПатенте №2012619254 от 12 октября 2012 г.');

$tmp_str = <<<HTML_FOOTER
   <div class="disclaimer"><span class="vir">[!]</span> Отказ от гарантий: невозможно гарантировать обнаружение всех вредоносных скриптов. Поэтому разработчик сканера не несет ответственности за возможные последствия работы сканера AI-Bolit или неоправданные ожидания пользователей относительно функциональности и возможностей.
   </div>
   <div class="thanx">
      Замечания и предложения по работе скрипта, а также не обнаруженные вредоносные скрипты вы можете присылать на <a href="mailto:ai@revisium.com">ai@revisium.com</a>.<br/>
      Также будем чрезвычайно благодарны за любые упоминания сканера AI-Bolit на вашем сайте, в блоге, среди друзей, знакомых и клиентов. <br/>Ссылку можно поставить на страницу <a href="https://revisium.com/ai/">https://revisium.com/ai/</a>.<br/> 
     <p>Получить консультацию или задать вопросы можно по email <a href="mailto:ai@revisium.com">ai@revisium.com</a>.</p> 
	</div>
HTML_FOOTER;

define('AI_STR_076', $tmp_str);
define('AI_STR_077', "Подозрительные параметры времени изменения файла");
define('AI_STR_078', "Подозрительные атрибуты файла");
define('AI_STR_079', "Подозрительное местоположение файла");
define('AI_STR_080', "Обращаем внимание, что обнаруженные файлы не всегда являются вирусами и хакерскими скриптами. Сканер минимизирует число ложных обнаружений, но это не всегда возможно, так как найденный фрагмент может встречаться как во вредоносных скриптах, так и в обычных.<p>Для диагностического сканирования без ложных срабатываний мы разработали специальную версию <u><a href=\"https://revisium.com/ru/blog/ai-bolit-4-ISP.html\" target=_blank style=\"background: none; color: #303030\">сканера для хостинг-компаний</a></u>.");
define('AI_STR_081', "Уязвимости в скриптах");
define('AI_STR_082', "Добавленные файлы");
define('AI_STR_083', "Измененные файлы");
define('AI_STR_084', "Удаленные файлы");
define('AI_STR_085', "Добавленные каталоги");
define('AI_STR_086', "Удаленные каталоги");
define('AI_STR_087', "Изменения в файловой структуре");

$l_Offer =<<<OFFER
    <div>
	 <div class="crit" style="font-size: 17px; margin-bottom: 20px"><b>Внимание! Наш сканер обнаружил подозрительный или вредоносный код</b>.</div> 
	 <p>Возможно, ваш сайт был взломан. Рекомендуем срочно <a href="https://revisium.com/ru/order/" target=_blank>проконсультироваться со специалистами</a> по данному отчету.</p>
	 <p>Отправьте отчет в запароленном архиве .zip в компанию "Ревизиум" на <b><a href="mailto:ai@revisium.com">ai@revisium.com</a></b> для бесплатной консультации.</p>
	 <p>Компания "<a href="https://revisium.com/">Ревизиум</a>" более 7 лет специализируется на лечении и защите сайтов от взлома.</p>
	   <p><hr size=1></p>
	   <p>Дополнительную проверку вирусов на страницах необходимо выполнить бесплатным <b><a href="http://rescan.pro/?utm=aibolit" target=_blank>веб-сканером ReScan.Pro</a></b>.</p>
	   <p><hr size=1></p>
           <div class="caution">@@CAUTION@@</div>
    </div>
OFFER;

$l_Offer2 =<<<OFFER2
	   <b>Наши новые продукты:</b><br/>
              <ul>
               <li style="margin-top: 10px">облачный <a href="https://cloudscan.pro/ru/" target=_blank>антивирус CloudScan.Pro</a> для веб-специалистов</li>
               <li style="margin-top: 10px"><a href="https://revisium.com/ru/blog/ai-bolit-4-ISP.html" target=_blank>антивирус для хостинг-компаний</a></li>
              </ul>  
	</div>
OFFER2;

} else {
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// ENGLISH INTERFACE
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$msg1 = "\"Display _MENU_ records\"";
$msg2 = "\"Not found\"";
$msg3 = "\"Display from _START_ to _END_ of _TOTAL_ files\"";
$msg4 = "\"No files\"";
$msg5 = "\"(total _MAX_)\"";
$msg6 = "\"Filter/Search:\"";
$msg7 = "\"First\"";
$msg8 = "\"Previous\"";
$msg9 = "\"Next\"";
$msg10 = "\"Last\"";
$msg11 = "\": activate to sort row ascending order\"";
$msg12 = "\": activate to sort row descending order\"";

define('AI_STR_001', 'AI-Bolit v@@VERSION@@ Scan Report:');
define('AI_STR_002', '');
define('AI_STR_003', 'Caution! Do not leave either ai-bolit.php or report file on server and do not provide direct links to the report file. Report file contains sensitive information about your website which could be used by hackers. So keep it in safe place and don\'t leave on website!');
define('AI_STR_004', 'Path');
define('AI_STR_005', 'iNode Changed');
define('AI_STR_006', 'Modified');
define('AI_STR_007', 'Size');
define('AI_STR_008', 'PHP Info');
define('AI_STR_009', "Your password for AI-BOLIT is too weak. Password must be more than 8 character length, contain both latin letters in upper and lower case, and digits. E.g. <b>%s</b>");
define('AI_STR_010', "Open AI-BOLIT with password specified in the beggining of file in PASS variable. <br/>E.g. http://you_website.com/ai-bolit.php?p=<b>%s</b>");
define('AI_STR_011', 'Current folder is not readable. Please change permission for <b>rwxr-xr-x</b> or using command line <b>chmod +r folder_name</b>');
define('AI_STR_012', "<div class=\"rep\">%s malicious signatures known, %s virus signatures and other malicious code. Elapsed: <b>%s</b
>.<br/>Started: %s. Stopped: %s</div> ");
define('AI_STR_013', 'Scanned %s folders and %s files.');
define('AI_STR_014', '<div class="rep" style="color: #0000A0">Attention! Script has performed quick scan. It scans only .html/.js/.php files  in quick scan mode so some of malicious scripts might not be detected. <br>Please launch script from a command line thru SSH to perform full scan.');
define('AI_STR_015', '<div class="title">Critical</div>');
define('AI_STR_016', 'Shell script signatures detected. Might be a malicious or hacker\'s scripts');
define('AI_STR_017', 'Shell scripts signatures not detected.');
define('AI_STR_018', 'Javascript virus signatures detected:');
define('AI_STR_019', 'Unix executables signatures and odd scripts detected. They might be a malicious binaries or rootkits:');
define('AI_STR_020', 'Suspicious encoded strings, extra .php extention or external includes detected in PHP files. Might be a malicious or hacker\'s script:');
define('AI_STR_021', 'Might be a malicious or hacker\'s script:');
define('AI_STR_022', 'Symlinks:');
define('AI_STR_023', 'Hidden files:');
define('AI_STR_024', 'Files might be a part of doorway:');
define('AI_STR_025', 'Doorway folders not detected');
define('AI_STR_026', 'Warnings');
define('AI_STR_027', 'Malicious code in .htaccess (redirect to external server, extention handler replacement or malicious code auto-append):');
define('AI_STR_028', 'Non-PHP file has PHP signature. Check for malicious code:');
define('AI_STR_029', 'This script has black-SEO links or linkfarm. Check if it was installed by yourself:');
define('AI_STR_030', 'Reading error. Skipped.');
define('AI_STR_031', 'These files have invisible links, might be black-seo stuff:');
define('AI_STR_032', 'List of invisible links:');
define('AI_STR_033', 'Displayed first ');
define('AI_STR_034', 'Folders contained too many .php or .html files. Might be a doorway:');
define('AI_STR_035', 'Suspicious code detected. It\'s usually used in malicious scrips:');
define('AI_STR_036', 'The following list of files specified in .adirignore has been skipped:');
define('AI_STR_037', 'CMS found:');
define('AI_STR_038', 'Large files (greater than %s! Skipped:');
define('AI_STR_039', 'Files greater than %s not found');
define('AI_STR_040', 'Files recommended to be remove due to security reason:');
define('AI_STR_041', 'Potentially unsafe! Folders which are writable for scripts:');
define('AI_STR_042', 'Writable folders not found');
define('AI_STR_043', 'Memory used: ');
define('AI_STR_044', 'Quick scan through the files from ' . DOUBLECHECK_FILE . '. For full scan remove ' . DOUBLECHECK_FILE . ' and launch scanner once again.');
define('AI_STR_045', '<div class="notice"><span class="vir">[!]</span> Ai-BOLIT is working in quick scan mode, only .php, .html, .htaccess files will be checked. Change the following setting \'scan_all_files\' => 1 to perform full scanning.</b>. </div>');
define('AI_STR_050', "I'm sincerely appreciate reports for any bugs you may found in the script. Please email me: <a href=\"mailto:audit@revisium.com\">audit@revisium.com</a>.<p> Also I appriciate any reference to the script in your blog or forum posts. Thank you for the link to download page: <a href=\"https://revisium.com/aibo/\">https://revisium.com/aibo/</a>");
define('AI_STR_051', 'Report for ');
define('AI_STR_052', 'Heuristic Analyzer has detected suspicious files. Check if they are malware.');
define('AI_STR_053', 'Function called by reference');
define('AI_STR_054', 'Suspected for obfuscated variables');
define('AI_STR_055', 'Suspected for $GLOBAL array usage');
define('AI_STR_056', 'Abnormal split of string');
define('AI_STR_057', 'Scanning has been done in simple mode. It is strongly recommended to perform scanning in "Expert" mode. See readme.txt for details.');
define('AI_STR_058', 'Phishing pages detected:');

define('AI_STR_059', 'Mobile redirects');
define('AI_STR_060', 'Malware');
define('AI_STR_061', 'JS viruses');
define('AI_STR_062', 'Phishing pages');
define('AI_STR_063', 'Unix executables');
define('AI_STR_064', 'IFRAME injections');
define('AI_STR_065', 'Skipped big files');
define('AI_STR_066', 'Reading errors');
define('AI_STR_067', 'Encrypted files');
define('AI_STR_068', 'Suspicious (heuristics)');
define('AI_STR_069', 'Symbolic links');
define('AI_STR_070', 'Hidden files');
define('AI_STR_072', 'Adware and spam links');
define('AI_STR_073', 'Empty links');
define('AI_STR_074', 'Summary');
define('AI_STR_075', 'For non-commercial use only. In order to purchase the commercial license of the scanner contact us at ai@revisium.com');

$tmp_str =<<<HTML_FOOTER
		   <div class="disclaimer"><span class="vir">[!]</span> Disclaimer: We're not liable to you for any damages, including general, special, incidental or consequential damages arising out of the use or inability to use the script (including but not limited to loss of data or report being rendered inaccurate or failure of the script). There's no warranty for the program. Use at your own risk. 
		   </div>
		   <div class="thanx">
		      We're greatly appreciate for any references in the social medias, forums or blogs to our scanner AI-BOLIT <a href="https://revisium.com/aibo/">https://revisium.com/aibo/</a>.<br/> 
		     <p>Contact us via email if you have any questions regarding the scanner or need report analysis: <a href="mailto:ai@revisium.com">ai@revisium.com</a>.</p> 
			</div>
HTML_FOOTER;
define('AI_STR_076', $tmp_str);
define('AI_STR_077', "Suspicious file mtime and ctime");
define('AI_STR_078', "Suspicious file permissions");
define('AI_STR_079', "Suspicious file location");
define('AI_STR_081', "Vulnerable Scripts");
define('AI_STR_082', "Added files");
define('AI_STR_083', "Modified files");
define('AI_STR_084', "Deleted files");
define('AI_STR_085', "Added directories");
define('AI_STR_086', "Deleted directories");
define('AI_STR_087', "Integrity Check Report");

$l_Offer =<<<HTML_OFFER_EN
<div>
 <div class="crit" style="font-size: 17px;"><b>Attention! The scanner has detected suspicious or malicious files.</b></div> 
 <br/>Most likely the website has been compromised. Please, <a href="https://revisium.com/en/contacts/" target=_blank>contact website security experts</a> from Revisium to check the report or clean the malware.
 <p><hr size=1></p>
 Also check your website for viruses in our free <b><a href="http://rescan.pro/?en&utm=aibo" target=_blank>online scanner ReScan.Pro</a></b>.
</div>
<br/>
<div>
   Revisium contacts: <a href="mailto:ai@revisium.com">ai@revisium.com</a>, <a href="https://revisium.com/en/contacts/">https://revisium.com/en/home/</a>
</div>
<div class="caution">@@CAUTION@@</div>
HTML_OFFER_EN;

$l_Offer2 = 'Professional virus/malware clean up and website protection service with 6 month support for only $99 (one-time payment): <a href="https://revisium.com/en/home/#order_form">https://revisium.com/en/home/</a>.';

define('AI_STR_080', "Notice! Some of detected files may not contain malicious code. Scanner tries to minimize a number of false positives, but sometimes it's impossible, because same piece of code may be used either in malware or in normal scripts.");
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$l_Template =<<<MAIN_PAGE
<html>
<head>
<!-- revisium.com/ai/ -->
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" >
<META NAME="ROBOTS" CONTENT="NOINDEX,NOFOLLOW">
<title>@@HEAD_TITLE@@</title>
<style type="text/css" title="currentStyle">
	@import "https://cdn.revisium.com/ai/media/css/demo_page2.css";
	@import "https://cdn.revisium.com/ai/media/css/jquery.dataTables2.css";
</style>

<script type="text/javascript" language="javascript" src="https://cdn.revisium.com/ai/jquery.js"></script>
<script type="text/javascript" language="javascript" src="https://cdn.revisium.com/ai/datatables.min.js"></script>

<style type="text/css">
 body 
 {
   font-family: Tahoma;
   color: #5a5a5a;
   background: #FFFFFF;
   font-size: 14px;
   margin: 20px;
   padding: 0;
 }

.header
 {
   font-size: 34px;
   margin: 0 0 10px 0;
 }

 .hidd
 {
    display: none;
 }
 
 .ok
 {
    color: green;
 }
 
 .line_no
 {
   -webkit-border-radius: 4px;
   -moz-border-radius: 4px;
   border-radius: 4px;

   background: #DAF2C1;
   padding: 2px 5px 2px 5px;
   margin: 0 5px 0 5px;
 }
 
 .credits_header 
 {
  -webkit-border-radius: 4px;
   -moz-border-radius: 4px;
   border-radius: 4px;

   background: #F2F2F2;
   padding: 10px;
   font-size: 11px;
    margin: 0 0 10px 0;
 }
 
 .marker
 {
    color: #FF0090;
	font-weight: 100;
	background: #FF0090;
	padding: 2px 0px 2px 0px;
	width: 2px;
 }
 
 .title
 {
   font-size: 24px;
   margin: 20px 0 10px 0;
   color: #9CA9D1;
}

.summary 
{
  float: left;
  width: 500px;
}

.summary TD
{
  font-size: 12px;
  border-bottom: 1px solid #F0F0F0;
  font-weight: 700;
  padding: 10px 0 10px 0;
}
 
.crit, .vir
{
  color: #D84B55;
}

.intitem
{
  color:#4a6975;
}

.spacer
{
   margin: 0 0 50px 0;
   clear:both;
}

.warn
{
  color: #F6B700;
}

.clear
{
   clear: both;
}

.offer
{
  -webkit-border-radius: 4px;
   -moz-border-radius: 4px;
   border-radius: 4px;

   width: 500px;
   background: #F2F2F2;
   color: #747474;
   font-family: Helvetica, Arial;
   padding: 30px;
   margin: 20px 0 0 550px;
   font-size: 14px;
}

.offer2
{
  -webkit-border-radius: 4px;
   -moz-border-radius: 4px;
   border-radius: 4px;

   width: 500px;
   background: #f6f5e0;
   color: #747474;
   font-family: Helvetica, Arial;
   padding: 30px;
   margin: 20px 0 0 550px;
   font-size: 14px;
}


HR {
  margin-top: 15px;
  margin-bottom: 15px;
  opacity: .2;
}
 
.flist
{
   font-family: Henvetica, Arial;
}

.flist TD
{
   font-size: 11px;
   padding: 5px;
}

.flist TH
{
   font-size: 12px;
   height: 30px;
   padding: 5px;
   background: #CEE9EF;
}


.it
{
   font-size: 14px;
   font-weight: 100;
   margin-top: 10px;
}

.crit .it A {
   color: #E50931; 
   line-height: 25px;
   text-decoration: none;
}

.warn .it A {
   color: #F2C900; 
   line-height: 25px;
   text-decoration: none;
}



.details
{
   font-family: Calibri;
   font-size: 12px;
   margin: 10px 10px 10px 0px;
}

.crit .details
{
   color: #A08080;
}

.warn .details
{
   color: #808080;
}

.details A
{
  color: #FFF;
  font-weight: 700;
  text-decoration: none;
  padding: 2px;
  background: #E5CEDE;
  -webkit-border-radius: 7px;
   -moz-border-radius: 7px;
   border-radius: 7px;
}

.details A:hover
{
   background: #A0909B;
}

.ctd
{
   margin: 10px 0px 10px 0;
   align:center;
}

.ctd A 
{
   color: #0D9922;
}

.disclaimer
{
   color: darkgreen;
   margin: 10px 10px 10px 0;
}

.note_vir
{
   margin: 10px 0 10px 0;
   //padding: 10px;
   color: #FF4F4F;
   font-size: 15px;
   font-weight: 700;
   clear:both;
  
}

.note_warn
{
   margin: 10px 0 10px 0;
   color: #F6B700;
   font-size: 15px;
   font-weight: 700;
   clear:both;
}

.note_int
{
   margin: 10px 0 10px 0;
   color: #60b5d6;
   font-size: 15px;
   font-weight: 700;
   clear:both;
}

.updateinfo
{
  color: #FFF;
  text-decoration: none;
  background: #E5CEDE;
  -webkit-border-radius: 7px;
   -moz-border-radius: 7px;
   border-radius: 7px;

  margin: 10px 0 10px 0px;   
  padding: 10px;
}


.caution
{
  color: #EF7B75;
  text-decoration: none;
  margin: 20px 0 0px 0px;   
  font-size: 12px;
}

.footer
{
  color: #303030;
  text-decoration: none;
  background: #F4F4F4;
  -webkit-border-radius: 7px;
   -moz-border-radius: 7px;
   border-radius: 7px;

  margin: 80px 0 10px 0px;   
  padding: 10px;
}

.rep
{
  color: #303030;
  text-decoration: none;
  background: #94DDDB;
  -webkit-border-radius: 7px;
   -moz-border-radius: 7px;
   border-radius: 7px;

  margin: 10px 0 10px 0px;   
  padding: 10px;
  font-size: 12px;
}

</style>

</head>
<body>

<div class="header">@@MAIN_TITLE@@ @@PATH_URL@@ (@@MODE@@)</div>
<div class="credits_header">@@CREDITS@@</div>
<div class="details_header">
   @@STAT@@<br/>
   @@SCANNED@@ @@MEMORY@@.
 </div>

 @@WARN_QUICK@@
 
 <div class="summary">
@@SUMMARY@@
 </div>
 
 <div class="offer">
@@OFFER@@
 </div>

 <div class="offer2">
@@OFFER2@@
 </div> 
 
 <div class="clear"></div>
 
 @@MAIN_CONTENT@@
 
	<div class="footer">
	@@FOOTER@@
	</div>
	
<script language="javascript">

function hsig(id) {
  var divs = document.getElementsByTagName("tr");
  for(var i = 0; i < divs.length; i++){
     
     if (divs[i].getAttribute('o') == id) {
        divs[i].innerHTML = '';
     }
  }

  return false;
}


$(document).ready(function(){
    $('#table_crit').dataTable({
       "aLengthMenu": [[100 , 500, -1], [100, 500, "All"]],
       "aoColumns": [
                                     {"iDataSort": 7, "width":"70%"},
                                     {"iDataSort": 5},
                                     {"iDataSort": 6},
                                     {"bSortable": true},
                                     {"bVisible": false},
                                     {"bVisible": false},
                                     {"bVisible": false},
                                     {"bVisible": false}
                     ],
		"paging": true,
       "iDisplayLength": 500,
		"oLanguage": {
			"sLengthMenu": $msg1,
			"sZeroRecords": $msg2,
			"sInfo": $msg3,
			"sInfoEmpty": $msg4,
			"sInfoFiltered": $msg5,
			"sSearch":       $msg6,
			"sUrl":          "",
			"oPaginate": {
				"sFirst": $msg7,
				"sPrevious": $msg8,
				"sNext": $msg9,
				"sLast": $msg10
			},
			"oAria": {
				"sSortAscending": $msg11,
				"sSortDescending": $msg12	
			}
		}

     } );

});

$(document).ready(function(){
    $('#table_vir').dataTable({
       "aLengthMenu": [[100 , 500, -1], [100, 500, "All"]],
		"paging": true,
       "aoColumns": [
                                     {"iDataSort": 7, "width":"70%"},
                                     {"iDataSort": 5},
                                     {"iDataSort": 6},
                                     {"bSortable": true},
                                     {"bVisible": false},
                                     {"bVisible": false},
                                     {"bVisible": false},
                                     {"bVisible": false}
                     ],
       "iDisplayLength": 500,
		"oLanguage": {
			"sLengthMenu": $msg1,
			"sZeroRecords": $msg2,
			"sInfo": $msg3,
			"sInfoEmpty": $msg4,
			"sInfoFiltered": $msg5,
			"sSearch":       $msg6,
			"sUrl":          "",
			"oPaginate": {
				"sFirst": $msg7,
				"sPrevious": $msg8,
				"sNext": $msg9,
				"sLast": $msg10
			},
			"oAria": {
				"sSortAscending":  $msg11,
				"sSortDescending": $msg12	
			}
		},

     } );

});

if ($('#table_warn0')) {
    $('#table_warn0').dataTable({
       "aLengthMenu": [[100 , 500, -1], [100, 500, "All"]],
		"paging": true,
       "aoColumns": [
                                     {"iDataSort": 7, "width":"70%"},
                                     {"iDataSort": 5},
                                     {"iDataSort": 6},
                                     {"bSortable": true},
                                     {"bVisible": false},
                                     {"bVisible": false},
                                     {"bVisible": false},
                                     {"bVisible": false}
                     ],
			         "iDisplayLength": 500,
			  		"oLanguage": {
			  			"sLengthMenu": $msg1,
			  			"sZeroRecords": $msg2,
			  			"sInfo": $msg3,
			  			"sInfoEmpty": $msg4,
			  			"sInfoFiltered": $msg5,
			  			"sSearch":       $msg6,
			  			"sUrl":          "",
			  			"oPaginate": {
			  				"sFirst": $msg7,
			  				"sPrevious": $msg8,
			  				"sNext": $msg9,
			  				"sLast": $msg10
			  			},
			  			"oAria": {
			  				"sSortAscending":  $msg11,
			  				"sSortDescending": $msg12	
			  			}
		}

     } );
}

if ($('#table_warn1')) {
    $('#table_warn1').dataTable({
       "aLengthMenu": [[100 , 500, -1], [100, 500, "All"]],
		"paging": true,
       "aoColumns": [
                                     {"iDataSort": 7, "width":"70%"},
                                     {"iDataSort": 5},
                                     {"iDataSort": 6},
                                     {"bSortable": true},
                                     {"bVisible": false},
                                     {"bVisible": false},
                                     {"bVisible": false},
                                     {"bVisible": false}
                     ],
			         "iDisplayLength": 500,
			  		"oLanguage": {
			  			"sLengthMenu": $msg1,
			  			"sZeroRecords": $msg2,
			  			"sInfo": $msg3,
			  			"sInfoEmpty": $msg4,
			  			"sInfoFiltered": $msg5,
			  			"sSearch":       $msg6,
			  			"sUrl":          "",
			  			"oPaginate": {
			  				"sFirst": $msg7,
			  				"sPrevious": $msg8,
			  				"sNext": $msg9,
			  				"sLast": $msg10
			  			},
			  			"oAria": {
			  				"sSortAscending":  $msg11,
			  				"sSortDescending": $msg12	
			  			}
		}

     } );
}


</script>
<!-- @@SERVICE_INFO@@  -->
 </body>
</html>
MAIN_PAGE;

$g_AiBolitAbsolutePath = dirname(__FILE__);

if (file_exists($g_AiBolitAbsolutePath . '/ai-design.html')) {
  $l_Template = file_get_contents($g_AiBolitAbsolutePath . '/ai-design.html');
}

$l_Template = str_replace('@@MAIN_TITLE@@', AI_STR_001, $l_Template);

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//BEGIN_SIG 30/01/2018 08:19:46
$g_DBShe = unserialize(gzinflate(/*1517332786*/base64_decode("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")));
$gX_DBShe = unserialize(gzinflate(/*1517332786*/base64_decode("bVVrb+I4FP0rniyzUKmUPKHQabUUOu1unyrM7IyqKjKJCVacx9gOLYx2f/va14ZWo+ULyfH18b3nXN/gUd8f/aQj90SMBiMnJUucEO6c0JGnED8cOd+rBiW4RHUjEUZFGiEhOS0zHeOrmOORU69qsSKMaShQkKJ0PqV0jRKGhTh1FqxKcrSQm5p4ztn7lVRW9S/IQhbOmWYKDXkyHO7JIwPxaLCH+ibxu/kUp+lGIwO7j672Qccm6PMrkGlkqBBPpfk3WaCZDkML2OxpITxPK7GmbDvbMXggRzRyrnCSk1RFozFb4gWR1VfKZYNNlBXkLpD7s73AMs5kxYvBG2No8ZsqacQ7PLKp7ao8UvLCgi51OHJwKekb+8CAvKreHXlsqYsNYOSVJIAPTXqm4PscLISClc86qOPwAnU56i5hSdfs6XIIalLMKoG2OOPNlkpY1sVG6pRWQYTAGUGniHCSxZzUTHVRx/kYTT76vnOIHPO3Czw4gf0gjMq9Vu0kkTOrcUHS9tmnBQf/fS1Q6I4cQWRSVTklHaQr+sHiF7KIcVrQMm4E4aXa5yBLqtULlNeECUKXnWVTJpJWZUxeqZCi44AgMShycAA7tKyRSoMuUYeKOMGM4QVT6ZsghMsUfVBHYc7xxqKHrZQKHaX5LY92IggNT6dVE14I9DtyXyeu6x6g09P9408IB49UdSnlqHd/iXrfANYWBX3FUiasSUmnFc8uHr9ePD61r+bzh/iLeovHlxd38/YzHBvYm7vg7osI4OYG2rdQVdReSVmnR0lVLtuH7fWqUgrs3pJlphtLPymAmhfY7ds6ejWvkp7YiF6uNCast8EF7tWSK29jkVQ1gXDtoh8owdeYdZaUkTgjMlakkpRacB0T2kZS8r5wKo28vTXmPViObAu2Lm/uz8c3s6d2rH6wBOZEsHNvTFtb0P7VGIOqJnvnjAAObYxSNHeVFHAPAq29r6CswJR1RSHrLi2P2FFWVRkjSqICwoZGWtVt3HWh58PdeJjq8QCzACQPPRM6fZufoW9Dn9DDSkn5ukHPgAfmEj4lVUr41mBaoEBPzQ/d7m+6EJQU6anTAs/Hk8nFw9xB3a6Zi7sB8fzEq6ZMO+7BM8gc9neThhZjKd+S0wJ4SsQp5vmEk5fPnJIyBXFCOxpzn7EgSAGyZV/n114QDGDuupbhajy5vpii8+9oNr9/vIU1z7p3q66Y+lAYUYA88u22W340W6mOXuHymzfsw1pgBteU0BRz/C90fxTaS/EXLWs156piC3BkYr1yECTSc0sA+/bca/UxmuW8qdXFrsy5g/+nOTY0E1JmdHuFDc3QDtBgHZRZWAJB3zXg+Y2q98vdn5Bc37Pzl0ua5Iz84Zz88x8=")));
$g_FlexDBShe = unserialize(gzinflate(/*1517332786*/base64_decode("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")));
$gX_FlexDBShe = unserialize(gzinflate(/*1517332786*/base64_decode("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")));
$gXX_FlexDBShe = unserialize(gzinflate(/*1517332786*/base64_decode("7X2JdttGsuiv2IrsaOMKcJNELZGVRHNtS1dSkntHcPBAEhQRkwQDgJZly+/bX23daICgFjszJ3POm0kUAr2iu7r2qva2G+3G9udgu7oTbzfq2ysTLxg78Yaz5qy6az8dX96dnV5c3l0cn/96fH53dHr6XyfHd+fH//3LMbz98eT18cW6c7WyE2zXoH3N3l754fTy7fGlE2+eHb49fo0ldSxpb690u9Dv1fcr7+ymU67XO04ZS618aacGpbbFpTbOanvlH5d+NMHnBj+fTv2khc9NeO5srzirMy+O3fkM37W4zscjf/AJn9s4Qq25vRIM+cPijeF82k+CcOr6H4M4idVrHP5zdav2xemtDZMZlPr9u/g2TvzJXTzyx2N+g2Mlo2h+Nwtn/vRuFoV9F3+tp+2hw3X6gxPowATqLZzl6elpeXMfv3QejQd+Pxz4NPY+rSBugQVLESeR703cOOy/9xO3Pw78aWJOMenPtisVakKrDh87608Tnh1VpDJcd6sGKwuvvCjybt2JN+Ph4C8MEvkf4AfVxV2w6jS0G0T+bOz1fV0Vx9ytjHxvsEeVcVPq1vZKP5zdmvMaJYmeGG5UrVMFeAo/+LAx49Ab+AN3GIzTfgHCCIKcK3PtscrUm/iZxXyXrZNMZm5BnXhj65u6XSynb0Egq8O++P1RmFmUt6fPcCT8SRVbshv4ouKUnY2Kr8sQCltV1ckmLHQwi8cegJUGv6eeuI4MV5nHUSXuBdMK7sCAzlxVhutSvz+9Pv3h8HW6Io8cB6vqZagrWHNWvXkycvEUQGGXyhDW6h39dXoRr8NwoB+oJkJaHSDN/+CN1YdfA5iHMzyQVAXhqw1z/+2HC/fVyTnWKmd235/MKpl9KtNXenCmP/g/CohlmjhlABnqHAGzbS2Fywz8YPMrr/TpsPTPaqnjvqPvQqChU2jAIMMe9Y/A0q4JHkUkiqvMLWgmPhak83onEJurFc97f/j9xKxHvSOEtVoZLO3L7y3ze7vO/i+XP5bazv4Pzr6xCj0v9pu2608J9UDrYRQSYq0jfDYJlT4WCGF4gidzgfhQEf6pE3h21GkYbFYcrArFGz4hfoXu/nHux7NwGvvb27Gf/BAONFKZRf61K9iImiAINgF4DvZxw9zZHNBjOE0AP8ZPIFjUFUJsJ90nQojpiYTOkpBQ+FZBkezO0vKJH8feNU8Z4b1p4bouLBQD26Ne04paCvEGsXsTBYnXG/td4zfVaciqHhQAOPc8j/2Ilk+OhEX4DegjEMME6qxvL/ygaoTdYPTSXjD9EL73NaWxCHaQwPXWgml/PB/4d/JfN5z2/bvI/3MOVEX9l16uZzBp5YMXVWA+RDwsgpwWI6+FUps4FYRU/pzYnw405lc7o55lJ/QzEjE/iqmfmmCCryT2zhpObXwzKw3CmykuMyA+gwTaxPdUa0xUFU2lRmVBrOrIms9fW5mGJDIOO1nxBhMgB3A2hsH1PPIQtTrl2WhWGYfXAf+kBghRLZyiP+ZlyzRxgwGMspl9lwTJ2F98Deh8zpMgHg3Q93w28BI/3yfSPj+5pzlCo9UypjT2ptdz2MRYpoNIl8cfcIuWsK59pww0/joZOes7XyI/mUdT3HSHFoz+7nwBHnDtOdCbozB8H/gMvzbCr90gBnGtELEE04H/ESheMmIw0kN3ZMGZ89r8TNCKlDge4c8vQgtLgHdHxL9WZXF2g2EE34GrEfW7inuKAXYGYT92ykA4r+HElvvhpDIMo0lMMNWoCfXcIp7M2Yd9dJwpfWDZWaUqCHYtqCKl8ebTT6U6dqNw4vO4imp/jOYTP3LjGfCn42D6HmaafEyoCoKSBcfpKJwMg2gCA19G3jT2+rLrZ3CQbsJosE21G0LIhDH1o2siR/5HdXapgE/Ajd+LE4+IIX23d4OPsX5GfiCMvOiWeiaRoP6Nx3pyG/85HswnuH6lEfwZh31vPApjhNzSnAZqCVv4JkxGfoStgB/afOMFAx+/9y1sL38rgpcFCAv74JOoD7gA5yqIA3CQB7ANfV5NBVj90SQcOGtQgmDvrLnEm7ius46LVG3UaoydmwhY7cY3fjZxCX2YDH7laRafNWvC4wHZW986+/nMBQJ7cXL6dmsyaCj6QiDYJMnDRpr359yPgJxvbslBQHbjRb36RwjCWiyIuGkJund6xBSuCYKTYW0REPOlDkkuTRI0qnYRge0WEteFd1tLyLAM9DlXRqM2RbZ67gOLIlV/d9bmAO9D3428G1ikfVkNhBOL+GbkvtxBCCzHlKbHnK/xluq3WaaN/Y/8D73sCOl3NudTBKFNQY+b1S38P8m9VcGcgLkA0Jy16sd6reVvzeTgbVU/Wt4WLjuCErWoyUZdR+F85t7XjqrjvjZwo2D1YYWeiFdkx1q43bCfXs97H1YOT+kdbrLdZgzMdG0WxkLgiTSkFN2L32fF6xaDAByW+RRxkmLimQXMSnivTo9+eXP89tI9Pz29NLjrBYGhAh/vJ3Gl7/VHfoUkDhQsLvwkCabXBLetppLX/eQymPjhPNGos+CoUxOEBdsiloA/cDWYGLR9l5l1/bGBs1mjdggT9Spx58LLsrAw9sa8NQo+qlqm+jQHJn8CLDSIaTR4uyoC3PWnYDocI21ew/9TGUKCjVhz9b1/K8AJgwE/bUglfxortkPN6qLagO8B0isrj3TyMnwVRnqL2sQF28i5IvGXDWLmnDYIUMh92/bz5eWZ+zPw9dSZLYMe9EeAF1U7lqCkFdUjUQ8GrRD5JqbH2Wc5swtLoyj8yw+Bf9P1oiToj/2XwaCryOkM0X369cHA+HwaAAGgQScb9x5BiJctATL2WTYw3iAJ9Xd6hLXl93/ge+qjxaf9tfXPUT/oWCS9t4kh4WVNwnF44/NXAncLbww23gW2aKooJmOBNkKCbWvKwcjNiV845QOn5GwiO19xbt5t7qtmdtUmLVX1qQ1bjQY1VLBzMKfDuUZlrRb85yX8+3+pI6QPMz+aAACImN+pC8gqLcMd4oiUd6I6RBrgZO+kZfCrFFAhIY2a1uvllXrqLLJuDJio99SqIdKOEi5QfhARuoRLWRpStaZSrqDcAf+WYLk/+BGVtcwyp3xydFwCrEyIutNWmhDkCDUnlX6WPhMdrRYsO+VK7g/swHzsx6wWrApnlYTz/kidb80RwG/FFHB12g6YwUGmPjCt80jUHIxckmCSfeDmSl1bcjZKwrzjqsATl1sCJsg2qM7f/C9OwEBk8J89+DfxIq15ASKNPB9RamDOn3WfwaijIC7tuYNeaY+lhVMW39bSsqQ33pKnrcxbFw4T99sQRcMg8PWKT8NnyMdwBTqnMHDm/ESidUiR7XQYagTCgqIbB59kWWjPqymJei7qWQSfJFSyKL3TPcIXeamkliJIea/1B9V0DjP9E1jahIdua5lhOkM8s5nczvwufMwkwCeij91fZiyCblRw4XdJaGB1bRUBrQEzByJPojLPQuFqnDGpIDfgT7PhMKmONwASbJlMnXXNVSH/B34UhSR4whSBGvKJpw/Cc5qq2Z8zuteDcTfEdKAmdEyLjwhRNKvEIYp6jOsSxwFHnL6YTit9tYAYfzk+8Nc/Q/hW4McdWMIT8X5q8Hgt9Hmb5oVcQzqmLbydMand3h6PsBvHexfzfh+IKlduCEHHs0gzEY1LxNPYFHJivuWGpHSBUUonpL4l+aLS8wgXTbkKCRh2hq3R7L/CIjVSLDeahiLLWQXZSXSUYaqdzJagooTLuBdSuSA2QvBWVDrHbBSpQtcJXpioLfLfRJgf6gNhRimadthyURVV03ROggssXpDwgpMW2m6SeFsw6BKVGi8VaakBTySTD158O72O+C3RmDq+Hnmz3qe+Hw2HXGLLNAAFRk3b/TOa9f6URg0xdxGuGQ5BCtBbWxej1PDad/3edc3it6S6Beo678PoPX7XNnoZjofX0ae0l45MC99EwfRWl5DiFClM4o2H0+DTNb+tSX3Yu2g2CfvTuSqpy2f4g7DvD1y76cceT4C0k2oCCbKKXjqMLWIZvrFqCZ54Frq5uGEYO0KYB3BXaWFz+YexHrHww9qKkXjstjK4kL4QpYeZF4EoRTKnizpZQU3O6vHbXz/LUQZYPP9f9+Ly/OTtT2zNqqrzblgkJj3kDoHeJ6wrh664ck3OpAZ7JVGbKI5Vf7A2xLceHh0dn126hxfHXGjJyVf8CrQ7AlJ1mCSAi7mKknkPWILXaEv13xDWiVAUFo6SCR9+UptwpaYgBlZXERp6UYv7UTBDlEwYldUPyK5W/vAALLmQXr5gsCXlGmp+RB8jE3lRwxFVnbYQ4N584L0nZgFPNJ1artARZfvXKicMpVQODDIsVa0hR+Onw58OX+9WekwESGuGghpjGULo/Kl7cCLmE2Shy6nExm3qQjiIqXhcE6V9BeTpIj/ljoNJwNJOME1IeSFI+PoDt1A6M2VZXAVx7QaEXRA+xuG1kkFuWDNaI6UZng8ATaVAQAXNOOg5ZQS1I3jvM0zhSPMo4HZNmVhKmRctTsT1A9fOLVrCJDOW1dMTiWnH2ZeVbYuIRRosR0lAtCn7mX3qqo1iUx0LllK1cENZwKyRIoz4LrI2Z+1zjiEiLnTytbbjMxEza6RSq+ExXmpKefJE7pkFT5NFRmV4JsCFDR9MYxdVRZHfDyMlm2lFksCfVrvd490AnUw+RkzDSO+GuGwaj8Pw/XzmlOEY4vEl3PDmf7iWLeh66t8gp4+oireGVG82bT4pzYWrVQCwq1GNqeHmlgiQNrCAQ+BnSQsynCnJ4eRsO+WEEZG8OrxkxNlUCtdUQHXRPkzckEiyVa7Zlv6PAPG9CZBbdcoDfxhM/Tcnb45TRwpEfJMg6nOrjvByrA5aV9DK5oMpwLxGNurPZ2pIijeyf61ef5ppxqd3/ekYNwAf8BQdXH8imQlti5MZLpn65qiXRWOkl2s10p1EtjOeAOZUVuUkdL7C5FUjDV6Ttix//llPn1WJaZ8chD0P0DSp/DW9Jk0e4lXSZBpKpXhD7Mzi4mKQxpYS2tUmEuyU9mCEMy8ZiUirBSOFkEjHx2bU/MSTWT2nyMPWHkk6GXWTPtikuAOxaEEqOGL0VroE+EcoDCawkJU/ZszcaQEnglYaCSxFYOsGX5vyKqQBJK8cFNOA0RnWDaW+2rNhjSsTerVS3beTN7cXM0ZLNNrreloKf5Pa0GqZlCGI3X7k3Yz9qCYTzwI7aRGR2XJWkSy4NaRS9Kuuf1n6l61/Nbh1TZThhDEQrAwzW5F4QxpGXIKDrzZs4DrPUfo2YDEFBlJNdkiJp71klgk7V4+qM9hEFwgWttfwCWUkcQIi1WXD5P02WZi2qzbCvawxQXv7G4w5ApgzX1QgpKpEf6Bv7vE2GbH+rkaqy5algSeVGrXW+H6J0RCww1T+bitzWeSPUZOchF4vxh9q60zWoT9jZpe1nrCwYrAqb+7jv6Lz9bqwDS+BCJnTeRmT61KN9J6oYzT25CYOjz9yaU00EMMQjn1fTSIJkax6sTzoTzKE8CTkDtgNoP7tyEaRUP48p/zHjGuuLwrQHSXTAv/WBbZtDPsqJXZOmcSCAJ3HFaTVwSd4guXCB1JqrDDjs8IYg9SntRqZIoTSxLAb7zTVK2KJlMfJfWzZEt+8DD/UUQr37ORJyEw1P6kqxhjWQcUYd6L8WF4BTo1QgYasMxcRvm2RAPp5AdeiJl9jfNNKs7xqgemwRnpfMlAvuOU9yD8WofurgnfssEeK4waKF/fq6xTvroi9YfOtV1mrUF0wr3az0IhPO4sT6Q4Vx5N5TVb7KAVeg6DmPoThuU5KaWSIslLo7vNSyZQrUisS2h79OHGVVIKDeIKPadMGm9yxUoDAif+Zz2csXk110lc3lVMjy4CoVX028QEHsl4PxTOZAdAXDY2T+TgJZgBdpIYtIWfMXSp1YTH5K+KaXG7YFMZtGVJ8iuNonZTZyB8vsJM4edlODd6iRq+zHrpmMgvPl9lcgLTQ56Hh1B0EEXeg/Lp2yYcIGaBfvXP4hxdH+z+jYli07MDe/jnGZTW5UXgVJD63qYnqgeXLBQ6nThpkFHLEMzW/xKtcyxKs6HyXhb/vYDF2KyL5I9b9rpKrwO3tnOgFI7mu3mT07lAbzfUbYpEAVk9jziHyCms57CJ83yIeqZMG2VKetGtaOBd6kNJWrt0Su9TRL+evT88uXfiPsecoXKgzz67KuE3k8g01lOoDft4E00F445STcGYqQEaRP+wK8GCDStrC8Byvk5q5tYhNZA0UtijgqomSG+uCP2+oz7qi3wdoso9SGzyXkpdfexF/PeHMSH+DMNVNIL1mnwcepS5LJlCWG2xn8l4Zqu8RFxY8z7hrSwuX6DyT0tknzl47+rJWu8NS28fUgI7YhCs0xEADgxRIG5njVWdRansFLX3xZulT/4jft4RtT93dU69GmZFzhU5EF8evf3RMiMcTKmJg6gyA5ao2D6BE/GUYaFh0ZkipbtOn9eeRC9DrBgO1pCz9GAIjLDC1Yk9lZC4VlGvfOr3f2J1eQdLFo14P+/XjPpEhMuAD3tIPGkZJQY++W+gkoLreuMdiZdI8+Z0lxQv7pBX9KeoNPkw/cZktsnXh8PmxHxqIRBcgEoT30MUrVUvrb4lCzZSRGIy/yWMVdjDykpDJBRkR0DlV6NPXwX0Sut5gEInBrs7WB4B+YLdnLn8kGX4VKSAjBKLyVOImbeaeueh/hMFUuAQyPiBw9Hz0rI03J+FgG50fRx66R8abQ/SD2URGgWeg7Q2qw98LGLh4gxkfsjc0G4Xu4+XCdvd3V1fs3JPNfPHGy5eaxcq1uI9v5baadvM0lHjilG9ubiqV7VkiJ8dWsPiiab1o1V80Oy9a1Rct+0X9+EWL31gvrFcv6q0XzTa+x3+qL6zDF/Uf8R+o0/zxRbPGnZGZBFDnzazEbj8xC4TsnHCMXDFXpKCNTs5T2/BABdSc+NEFILnUC5XfHU8HzHnkfFadjYquKY5vdbKloLalgPsz/TpXJ17Sl/VoCy5mX477HD/qZGVBw5Jz1wsT5y6eBXiC725gpApZAupkH2nWC1zSdyu9cHC7x3POEt58sRyUhnIeJEvPQPrZQZ3qjtSLgFO4qoo0QhaVTnV5xMVSnVVBiMVyK2+dfZWropa8b7C8JvFeCF4yiX9nH/x5RL4bVu7znjCTjKXjX9GA56ks5K+D3v9MxvWT+If59fUtl4mFfOJ5wP5u3jL/T0YnS1j1Z8X+uKZsSsYnZqIfTRSI0sTxfMramXpDWdn/T3/k99/Hc1SDUQlbfwCKbkYBelFtxvOZHw19dTLJNoMap2gCJ5KMGMhLGVS5YlIM4BBGwZhj88hQg3Y8fXKwgpMeH/EKV+sOx4jbKWcWhUwBl7niHbvpzYHSzeBM+1M2UXETW6hTRsGrAoYEcgAHnynWhY06qA3TvPgS0kOfCJiVCDe3bYqtWMdeUB0M3kE0z3VIDqky9dtxVp2bz7Utu/FlRxOHJjtFoIkATQ7k5ohMnpvlIYqYaAMHS63yhinLAiL0px8KCvXpLHjN/A0HL5KJqFZb4m0eb/RHEat8FR0m7W/5L3lNE2AfbzTMKuuajh+poMM5Q797ctZVGg6yJaH318G+aKr0QZWlN3VMSm2lylK02lJAq/rJVLqnnaW2E1XPpFZeIH93C6LPHUZ43JGNiArv4tsJGp8yrgCCDLLH7AkxgPUW+1k0i7bzs6w8flv5b1zMH9IQGTjxruOKs9qEf234t8WlTWEiCqWw8ijxtENbXXuom5oMUkwsobmfhR1gF/WaidQMjMZVGNlaOUfMQuvVIPtLaSqLDp0ZJXpPl+hLHQnTTkasZlOFHw3HwSwVU3Q0EukMr9OlOszpH/6pcRaZtcQ8mfNfX4g4cOKNbLxBJbcFbRW4jy6pqNFwJU+Atqxz7BhXtsSeysoPV/G5OOfDC5jLpfvb4fnbk7c/ycSr3Iy5iO2VN/M4ORgewC5foBmICxuyOobZ8TEeWUs03nUyPhU5JhR7aNfbSj25wKWmK5UhkNyqbQjdB/tZe7RkO1gzqneEWF3raWCAR/zGm+ptJdNQvV0wEUC23nQQpTXJL2xZGFK84AZ5L/+0HNRT4mZ2aCL1+6wH9x8jOQok1ZgLxQas+qIKjWC4u9jfZziyRlT+X1fG00GARy/yNIRmvGUqKVgce97tDj10ML4DQShX88JPShz/yXW5XzwRHYxIpNBZLbxl9BK517Xccz33bOWe7UoKMKb7ZJ731HYu02GLPd+DxJ+U9kYBufXssdZ7sKmV35+NfaNT1MrJZxItusDQz6MxnymZXlsxOqQomAZIr7kHYMrCG1JpEzXXQ5OIX3vAHbjI7pE3+fAEmFbUtb89+gipTtDNXhmW5jz/kTLEvNt67k9mya1RhmYvQ48hjsOLhfEGKrjabBkTW7xFRjTUDYi+V2MLNOpskwcR4IVgusOWBY2THi2S7PAwNSGf+WE+jYPeNlepL6mSBs6TOYv164OwL8v1jx+9PsDu7fY2LPwr5b24pnABVCzteYPBBTOUeTHLIjMYq4MH/tCbj0mv53p/eB9lgCSa+/IVDVEHD/z3fU+H8VhkvjIJP6mRNFJe5UrKMNs1jDmcI4hMT7WqlTkRxaS+K9heTnwRJ5BT6GkHly6BMKENHrWjRO2cJrqQpi8R+5bLTn9BPYr4sthwVlUym8Eh97yBO0YXNBWlbdV0dNB+wVk82M/Zxw72daIibl4XBxaQATIz+WJ6OVlkT2tW0/2OZ1EwTYYMXS9ChYJUVFisHMEothgKSrYknSDLWqNAAfooQ5nqRLHHN3F44ffPPGCfaJm4tHmvpG+R6azZMjxQjSNCnhiwuDnWcIntSqbTFrYCk1ckfaBEvHTyEGi7pEX2MrvDUldvDb/xLrNDd3yG7kweOPPgohDd8/rv7/RGYhv4505zSHeyO3dmzOgdM5R3xPqESc26gzN7x0zCjTd+f2fyCyyW/f85/jVzpK0nsyaJzXjuizwDgqn4fZqYkuyd6D21RmoE4A2dGxDLf3c2nZKzUcEjoXDJ+md76ws3UrI9s84mfY3FX135tTGIt6vczhLtUhq46E+vg6lfQeJYGfQ4x4dON2JxaidUmvn9OcZqGE7dcdTXxHPAXrNEoXJ2sHEmcmug3S8U6uHIHDitP3mI1a7RAhVvHkX+DRdzXGfjyVpDxUfn8bSpKF3LPD/NVJWXmqy6koKGqK4r8I3JhI3rFVbhYAf7/cgHoHS1IF/gu1RABLgX9rq3yRea+cZVNyNyPdUM95BwQsOyobWT1VZmDbK6w5mE5FiWiidELyI345fLHvJpjhKLLK1WOyPZ0jwKSTAfDjadSuSnzIpWzRBilzrbCjOcCaku2MQq6fhIz+iw7wYPTVJ6zeCIBMjJylpAFbtpWkHOHSRW2vUd5KIJn2nbkkX21fZSZwzoJ7qdJe7F6T0TL9TEcu8cMNvI2SmKEFnen00QjVbKPrYNj0vEtZmz3yqN9Ca+5XoM4pIiL58X7UkgXqyOfmeiLxqSLL+1KvoXokQVK4mK3fPGIblzbr395fVrbpqtBeUu1eTEN1WuY6jpTPjniAyV58MiM7JFTiQJp9hRsUMs12V5Gm5CYr9dmLlMeOylFkQTOEykkfftu4+Bz3eO8WZ7GrrYgoxq7QWPLYcikNe0EJgb9AEBlQZj3brp+sXWCuVWteDfIwe32DvMYqN2k8jkotqmOPpJ1gxFK/kJ8p/wj7bOeUM2hxyaFxSFobNqHx4VS6kJ7qJaT4ZVYfRLBHkFQfE+wBAeyHLmD/dBubpaGJnD3s+JeD97s9k4YMpe+TAdOGVUr4UB/MAIde/aL3lRfxR8EHhW+VDi8TyaOXeTeEom78QHjM810qSr2st2iqAvQVj4myqSSbzWAqKzg5aN/Kf/rZlF5hQbKjGT4pQWvLYGShAiG3yt0yjw2f9mZOfkPET+ig672m9ksYZ8kSVeBayKZNvZAnTv7fGxYFvChvFQKkJM/DGOmN54HJUVoLSHRlYvEsR08fPpb4AQXh1eHv5weHF8wZVVfC4y0rEb38aIm8lHk5L5ciXSiaBL1gtNqs7ZbdkpnyT+hGtRfphOLvBpSRT9Q16VD4Rm8IDKsQ5A13Tyjvve1L4N50556ieVyJ+EwJNqxp7s6CjwZ22291l4FqgBs1tkd8fjuGDWIWUIBnb4GaLylKSfZLSnVFHLlJFZF+h7SNR99nHtoKrpFZn8Gx1Dm5kHOQmJKdZiaAu0JcnSOjpznjdLTXl/Z2SVLgUbXVsqN/G3Iop7tqggleOy7SqGhaV0kr9E+ToW5qLoGoBkyLHxRs6nkrtqil4NQxqc7ySdZH+inVNZH0TeE+2amSctSBOlLQNDIaWmvvN5qupMY/ssTj2nkSnxQoPgA/nv3o45tGEQxAA2t9vTcOqT00bGMfIePeZuBbra43G0G0U3K0ktnIr9A6wwoVRFi8XbeT7soDB756Js3VJK/YL5LkjjzI8tC70pqKoBhHwvOovuBWQRItZFK7rNDYkxF9adk0uDV9CDjzyRPlgc3FvAsD/WfissIHemkjJS7BjmtTziWBqGFPKcIDVDRkYR4CiQU/I5g7gXOkApVlwUq0lBjGEueYnaSDSuAvO5S+XroCW+8yN7e/t4SsIsQoO35az2uC45nNUzvg6AAjAc7My71umSOfzF0g4OuFESX0RViOfgKh0Ry/GLc/bUwtOZk2ypE/JNqKO5vICW/keg+aeSZo5KstqcW6X1gOPXf8rnq1k/dRUoGIBCsp85a8/3c6YnyijzC7xxD386fnupLZenlCZ/TTvWZFudH785vTx2D1+9OudBLGFHCsLJnsRQkR8JRSpzUjMard/HnE3uBFDwSE+QXHD07BYq3wIu4x4bOypEI39i1pyrMrqXVb8479bhB4nccmia4vxRcGYO9ufTkf+Rflc/cnUVDYU66+1tQuTAykbhR8o9/w5elUWhzrkY7dRdv6zFDJIPyspByaR13LIj6MWEw/JGln/VtTs6ge8Cpv72F7xZ7KQCi/SF2czeJzwIKkWosBjk7oGKFSMt2Sz2Kt6MMqHqWyYw2ufVj+enby/PABLp6efDX4/di4vX3I8lkn6AiZRgsCyqy3DZ92DDjkqRs9uLmNNAmcOd60QR7J4OhVxduy6t7yxkkbrSnpXrRNDvL+b+yFxQa/xV3otP0izyDEgA/OsmYIh0My9O/B6mg+9zbJjFbh+YGWNnMV7ufhjbKhBaHgRLI3LIIo+P9qKxXdbE/eX8RGkGtpZoBNAbUQfmOWuwRv2bgdJf2ezJgbG+M+RYXLrFJDW4EtIIBjypQehK4nLOQD5zKXF+hrpRA86nz0yTXVW+tkwTBsFw6M4xn+7TsKpNnh4IxSp+eiFq9Uk0xeYoZ9jVEgoWzyiW5FnJ60+elSKuYCueY5mPV4q6c7teyPNvMm7k0gdTZiyexIV4djJ9LBmr+3Wj8Ic3xAayNMUTpWXADylIwW6i/JybBvfeFHuWswqL113ZHdX2YDOe/RhGvWAw8Ke7FXiDIh/syXt/yo1awhQf7JtptZ04l/d1cRrfV75XPzNeGza7z9TobBFGR1LfVd7wKxdH5ydnl+7bwzfHK3RsgRkIx/PEL6yGo6ZVozBMKPCxm0KI2ZzHZ2cuIm7JTOUaoN/UltMjFfqG6f5Rd67rr6soSCVcE7OU+vlBRe3oc3UENApYJdZv2eQsg2dBws5SM6QcYvKQ6bSWe3I+LEhdPe4dD8d2FgpJBeh7Pw3pHgAuU7l2T8WrF8H9GI7w0U8n8BvAqO9fcnB8/zoosa2CWyr5zCkzBsHPBJSR4zyEECwq6/kGk5qKBJZU7kDlVyjMhdiAGWk3QeziupTTq5lzN8zFX6Q+uQpauC0JYs00H1icDSXQdm59b0ktva7IqNfNNsy7CJlFmUfusiNLtpMp6xquw9SXETWxyPTZ7LaBARqo7XC+U0H537H6A6pKAaXSqqv33LYmfkGcau0IA3ucM15lxzmczTQpI4cNvncEeseM2i5jLv7mk3B6NO/5/I5BibOcqrvJgDBWKL3/gAttsxDjdfozb+qPudBMdUot9S1jNnlTqGgeKkPcNECGYjrkGsqpr93Ba/3wT02u9uPspzAqMBzw2mpjhboqJIVy41vuFCLMzfem2ORZ0PrGDJTkDMcmYu609o2drtHh6CMvXZEs2bal0qLTPvyJhnPOHi5pyrkSMde1BRxFSbUL/OQIwZl3E1nmjpPBA7CfwoDkXcAZahY1mumbCpD5rtBXwza6wKss5/lsckFoLo5U2nMZmcWEN7N3GRRLCralwgeXG3ZR7NEnlbPNLrVvY73MMGlDlWtqecOcTVY3tVX41vKmrG5IW6QXMyxrYYqXaTt2Fi4Y6op5M62qLFLb/ifU4c9ktFagLiiiwXGaVerfNE+epL0U0pZZGZhvt9Osh49tWMiOcGdkvi9UrNzXWQpRlO7tye2XmFC2uM+UjC8d3CDgxdaxjFHGZsP/8jP2kB/xY5Xny1w3HuH4cr+t5PGv13Ofzq4M96O0PEo/KFiN1PqZK3y+gMzzlyMt9GfMrqaMPveemoJtf7D+Q8dQJpAmP12c5MFTZWpyPLALQPdBbRw3t5dQq0VhmOs3lMayCH7LZcL8fKgaKtdjXgH28mVBYvUHtTjcZytVEDzV9bPolKiN+zvr8vnDiUmlEOG/81zvRVP8IYwXLQ7g/mbvnL+7Z5T+bsnjsMgrL2o0DdyXPwcPazf/RQpS/gZCneh9dLBvuH2YEf0ZsTTe+EIejmZ8fsYohS0dNqQua1z0Xjsg2+TPguIZzeFgP1/7i86DSxWWFq9vc3eWNn4U1V2YC2nzHllTzdgW9XLxhLr69RPuxv6CLoNyg5rdVNeNHewDwAcgBUZuPJoneDus4YRvLNgTRuIRSMXSMpUeHDO7ln+xvk07bKo3yHUE8zbvyHeKwvS+JV5Xif5t8gxp2fm8UeoGt6JlZ2U85j3p4k1MbjANMtGF3G82zHN1xpnLxTNIO6vNdKfKd9f/SBGwRlD0CgaHT8YrCOb8W/1EpZFkRcwnQq0q8CCPkI6tHSP1fU10ORANvCdcRToJHZF6sJ8JvYOPRmOZk/ME02PVtNEUOyenoGKvZ3mSvEvY8Qxj1/gnt+Ue6wJ7OkrXzI1Rvn+nyIKG2UOUxivXiEegyNLUcVQCdzJXpef6LXiX1cJxxzrL6kKSWhUTwZn7+bvODi8ufjs9f5XjtrNdqstlAUgir5+Y5vD1AtcMyfS1vqBzNF6orSPeilLcLD3iBARPsveQI0qLRJbPWe2l0p+up+rN5TXWMoXcs8rZanwK8mWGAVsLEcKS5TpgVy0CVlLzPxgKKwC1wDTsOE7spMY57o7AbPGYc91BwGHFNuecNxx1nh2RguU1X4uFGmnMNsaHBU6R3Ilrt9ndk/OsFkSP43DzAWnAV0dpJt/0/jX+BZA5DfVP6D+fl4RxF4wgwyoTOpWZNhzy2culxnXWcoaki+OLC+6Hzlw7vV1FCXGRP/QjP9rmg5q5JLLAW+T8+Mfj8+PzLDkkxw1Oex+kV1YscxrhtqwYT6dibp24F0qdxIuu6eK3Lg9GpBH279ynWLbzOV1DiDfmOlcAJAAqACclmB1lOqLLjFZNWOQ07hpBOas1LL16LTYd8v5AFq/Q/Q/XmHGcQmzq7g2MhpnM0A6Rz5mLuLV3y9EzdlsFLeA+p0YmumfqAqCB1HYEl11M8Am8GZ9InYNCwEIltj/mKKN4s3ebXrH8282NUz77+ewfgfcmcMpHbJG3yYmkgHt9osqlQINA3XfUnX7Gt/PV4yX8lNIHPwqGQRpxyWWaQEiGVJs9SzizISLFD16UTkaAiiuqdMxn4/l1MH1Gl2RDrd/OjsLIv7iNuZYlvFpp73CejMIo+OQb/T1FdilmBvnq0EbhAFpfLnk1dIwi7yJ5mHAoFbphmR0sSYFlkxMJhqQdoWkPj8WRF6Fn766qe8D1WnJMvtKopdDRQd7Qpb5aJcwrcJIqChY1QohmcjWZTV4ajaUZzLGeXsWfnQ1jopgzFbtokB8GsrFcmWrWrKqIXambtM5ua9D2BrlY0CWiX3urPaASOG50gQAfU+5XZXBeNBKmctJTRANHaXEb7H3Bii93YWPd7HA5Z7JvLecJqJv80ihSgx1tcFL6tk6gXZRD6r9/OTm+dI9/PVRJw2u6dVOY2YX8QZlkRY/nhhpy1Wozg02e1M0S4RcKL4/P3aPD169/ODz6L8cQiXngtthu00BZvPUIOKHK7snwDV3Ji4wJpvgFRpXDsst9btsRs50kWKZsWoDdf5mlRtgG+R4Q8OauoR4G13wLNao7wileSq1/UZ0JIA7vJXJML5PJbJzW435VCLQMfjMKgTkNo1vy7MLsw3gxosyhLorEya2+PRChQU1YbvugqpaciWyoyYNx5QbXvJVL2iiibKOmTCJJFFxfwx4T9y+s824825PNOWZf1+Pz89NzbqikbFh7SsnkYuTgbUodlrPwpgakqLpgAnJkyAbYPJ30yPfu3qic9Y58N3nzoZ9qfhGXovVcWqzHvOahFLb3BoO8M9swDBN1RW0qo87Y3foPYmy4j44IZ2m0U+EdFU843eQgUWBcdpa718g877EIiG/Y2CR3DU6F0TS8V8QCknNgIeGEW5AvTquA2ypIv/bXaME1zd5/TLxAkRGIZ85JCupKQeOGcpE2sV8XFyenb4Eaua5kcMK0DlcryAGsmKGbzx9Vv7QXMH5gtxERW0Wn5nyUUN9GXeUo0Nlgs6cNU5uKxgOOwuKJ5E7I/9Zqfq0fSE6kIV5uQWQ1ro5TPMdV4b33eOUST6v1t5rWdTDkabX/VtOaTWW1Ooqgf70zT+EQlQcGtqr/5oEVfFi1f/PACgL4rud/48BBP+SBdZaUr2LMi5LaZxhZkt9vZiWRNioGm8L3V7S+5ZMzOaobfIfFV8kYusNROPGlN8591/r7WxC/IiHSAlXT6e7528ls3Kim3y5zUmi++mUNevv93Tt4qFUZraxDH/aXTJXPV79/UVW+6Coaev7WC8sLoZzNkI0ARksS+yJFXeolww07OyrJPTRDdRX5zyqzQzZIniAPI+SBDDcbmtm2VbbjJ+3xAq8XS7BVw9Z+jzAnzDuvFIgwG5hiNHH1WaZZv4/9WN0OvKVeTpVTu3BrtsosmL1yGuTZTfijDFENcvhqV41A+icBrVJTOsaVJLioJ4xKbHvHuFgdxGfUcjxL/Dh5Fr4HKZErkQaozbcEZrbtyzILboOcr9C1V1DGs2V+1A1ys6oVX/nyL+JBmQH9Gv5TfV1bOG0VQVVWdjIjWk695RYd7Uz2ucAKXxDbYxpAFlLaUJ/k/oRBjU9H2xhqQSluuaOa6EsK9giLVR5W5Fo3p+ENBr/hPRY1pxyPuApTw9YDqjwtkbgZNaUOnzWsT4sts8oktRXkV2RXs+GKZsRcnqhyKwXSOI6+lLvIA19nmmuwm1GT0maOtfb0IOy5eANFf+x7U2O4TCo/7KlvJgCiNLofJ2P5hpZok6+DxCmHcX8UTD3OOzL5BP/ADOpcUd3VtCQbEZ9snaWk0Ujjs/PbUqS43MorkKkTcijB2SlTloLJtE+WXchrA9W+FxMvSm6t7W2K7CCxTGQlrlcXcNO3iikLPRer4K18sW6vLnR7c/L61dvjS1JRXRwdvn3LSvcG3+nBOWRYQZi1g6XqDVEfXn2P1rDvRWfBbgdf35wQGt8YK5etPRpxxRsad6X5K/APnwfM1sr3bqQOBgriGJTYb6H+VJywqBsyfaka5LVAmCY9kPec83LG+Mp+Lzy9FntsNvIqtm8zcjw5/L7IUMLzo9sTmwWpbynJ173+rxkMnWbAlc3D+4plDVQsS3qeCD9gTnQ3iF1M7KWaSwt1md2bYOpHzz74UawSqTRaKrCBMrbPJ85m0ldRDw2+JgNzC4Xh2ClPoPnHSURKUmndFNR+w+bL0in8IeFApdhutNQFNtwSs0qEJe5vyBrWlk5WnQWhslL4Awc4UgGGu5g5ak9jSJ0Jaj7NYKS5k1V/pZRBg1JHtPiKdSG94MbiH/VpfzqcAZBak4W9pqJO8GP/8LwPXFQT2pBb0YI1bMH/uE1dRLKuc7XyPboECPMOHwFPaNyHk+A4v7907lC1RG+zda52oLxcESTSVrdgAmvr4Gl0bshWDA8V6u5AQQ8+rEDnv2MnmyvsScCtnBrV5w5tpTCDd6s8I+dztjX84vdf5L8458IK8k3dfMmOKl9d2fny/wA=")));
$g_ExceptFlex = unserialize(gzinflate(/*1517332786*/base64_decode("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")));
$g_AdwareSig = unserialize(gzinflate(/*1517332786*/base64_decode("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")));
$g_PhishingSig = unserialize(gzinflate(/*1517332786*/base64_decode("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")));
$g_JSVirSig = unserialize(gzinflate(/*1517332786*/base64_decode("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")));
$gX_JSVirSig = unserialize(gzinflate(/*1517332786*/base64_decode("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")));
$g_SusDB = unserialize(gzinflate(/*1517332786*/base64_decode("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")));
$g_SusDBPrio = unserialize(gzinflate(/*1517332786*/base64_decode("RdPLccMwDEXRloQvAaWaLFNDxr1nZOMiCw8wFHX4BFnft6jcvz/39fX5yVSdalN9akzNqWdqzb09VRZEFEjBFFBBFVjBlaJpkiHrZkVWZEVWZEVWZEVWMhsDMGTbMSAbsiEbsiEbspHZyezIjuw7YWRHdmRHdmRHDuRADuRAjn15yJHzpIEcyNHz9pNpJHIiJ3I6m/d/QeZEzmIPcz5kPshHuYR8nJWgyXHOYeWRnxsOmQu5hBUyF3KRuchcZK79NzONInMzjRYaMjdyI3fQkLkPDdPo/y/l2k7mgeTS7Wyv+q7Fdhwg134xV+3VPUP2DNkzZM94f5GfzndfbJfbPWe8d0jtWnPvfJivPw==")));
$g_DeMapper = unserialize(base64_decode("YTo1OntzOjEwOiJ3aXphcmQucGhwIjtzOjM3OiJjbGFzcyBXZWxjb21lU3RlcCBleHRlbmRzIENXaXphcmRTdGVwIjtzOjE3OiJ1cGRhdGVfY2xpZW50LnBocCI7czozNzoieyBDVXBkYXRlQ2xpZW50OjpBZGRNZXNzYWdlMkxvZygiZXhlYyI7czoxMToiaW5jbHVkZS5waHAiO3M6NDg6IkdMT0JBTFNbIlVTRVIiXS0+SXNBdXRob3JpemVkKCkgJiYgJGFyQXV0aFJlc3VsdCI7czo5OiJzdGFydC5waHAiO3M6NjA6IkJYX1JPT1QuJy9tb2R1bGVzL21haW4vY2xhc3Nlcy9nZW5lcmFsL3VwZGF0ZV9kYl91cGRhdGVyLnBocCI7czoxMDoiaGVscGVyLnBocCI7czo1ODoiSlBsdWdpbkhlbHBlcjo6Z2V0UGx1Z2luKCJzeXN0ZW0iLCJvbmVjbGlja2NoZWNrb3V0X3ZtMyIpOyI7fQ=="));

//END_SIG
////////////////////////////////////////////////////////////////////////////
if (!isCli() && !isset($_SERVER['HTTP_USER_AGENT'])) {
  echo "#####################################################\n";
  echo "# Error: cannot run on php-cgi. Requires php as cli #\n";
  echo "#                                                   #\n";
  echo "# See FAQ: http://revisium.com/ai/faq.php           #\n";
  echo "#####################################################\n";
  exit;
}


if (version_compare(phpversion(), '5.3.1', '<')) {
  echo "#####################################################\n";
  echo "# Warning: PHP Version < 5.3.1                      #\n";
  echo "# Some function might not work properly             #\n";
  echo "# See FAQ: http://revisium.com/ai/faq.php           #\n";
  echo "#####################################################\n";
  exit;
}

if (!(function_exists("file_put_contents") && is_callable("file_put_contents"))) {
    echo "#####################################################\n";
	echo "file_put_contents() is disabled. Cannot proceed.\n";
    echo "#####################################################\n";	
    exit;
}
                              
define('AI_VERSION', '20180130');

////////////////////////////////////////////////////////////////////////////

$l_Res = '';

$g_Structure = array();
$g_Counter = 0;

$g_SpecificExt = false;

$g_UpdatedJsonLog = 0;
$g_NotRead = array();
$g_FileInfo = array();
$g_Iframer = array();
$g_PHPCodeInside = array();
$g_CriticalJS = array();
$g_Phishing = array();
$g_Base64 = array();
$g_HeuristicDetected = array();
$g_HeuristicType = array();
$g_UnixExec = array();
$g_SkippedFolders = array();
$g_UnsafeFilesFound = array();
$g_CMS = array();
$g_SymLinks = array();
$g_HiddenFiles = array();
$g_Vulnerable = array();

$g_RegExpStat = array();

$g_TotalFolder = 0;
$g_TotalFiles = 0;

$g_FoundTotalDirs = 0;
$g_FoundTotalFiles = 0;

if (!isCli()) {
   $defaults['site_url'] = 'http://' . $_SERVER['HTTP_HOST'] . '/'; 
}

define('CRC32_LIMIT', pow(2, 31) - 1);
define('CRC32_DIFF', CRC32_LIMIT * 2 -2);

error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
srand(time());

set_time_limit(0);
ini_set('max_execution_time', '900000');
ini_set('realpath_cache_size','16M');
ini_set('realpath_cache_ttl','1200');
ini_set('pcre.backtrack_limit','1000000');
ini_set('pcre.recursion_limit','200000');
ini_set('pcre.jit','1');

if (!function_exists('stripos')) {
	function stripos($par_Str, $par_Entry, $Offset = 0) {
		return strpos(strtolower($par_Str), strtolower($par_Entry), $Offset);
	}
}

define('CMS_BITRIX', 'Bitrix');
define('CMS_WORDPRESS', 'Wordpress');
define('CMS_JOOMLA', 'Joomla');
define('CMS_DLE', 'Data Life Engine');
define('CMS_IPB', 'Invision Power Board');
define('CMS_WEBASYST', 'WebAsyst');
define('CMS_OSCOMMERCE', 'OsCommerce');
define('CMS_DRUPAL', 'Drupal');
define('CMS_MODX', 'MODX');
define('CMS_INSTANTCMS', 'Instant CMS');
define('CMS_PHPBB', 'PhpBB');
define('CMS_VBULLETIN', 'vBulletin');
define('CMS_SHOPSCRIPT', 'PHP ShopScript Premium');

define('CMS_VERSION_UNDEFINED', '0.0');

class CmsVersionDetector {
    private $root_path;
    private $versions;
    private $types;

    public function __construct($root_path = '.') {
        $this->root_path = $root_path;
        $this->versions = array();
        $this->types = array();

        $version = '';

        $dir_list = $this->getDirList($root_path);
        $dir_list[] = $root_path;

        foreach ($dir_list as $dir) {
            if ($this->checkBitrix($dir, $version)) {
               $this->addCms(CMS_BITRIX, $version);
            }

            if ($this->checkWordpress($dir, $version)) {
               $this->addCms(CMS_WORDPRESS, $version);
            }

            if ($this->checkJoomla($dir, $version)) {
               $this->addCms(CMS_JOOMLA, $version);
            }

            if ($this->checkDle($dir, $version)) {
               $this->addCms(CMS_DLE, $version);
            }

            if ($this->checkIpb($dir, $version)) {
               $this->addCms(CMS_IPB, $version);
            }

            if ($this->checkWebAsyst($dir, $version)) {
               $this->addCms(CMS_WEBASYST, $version);
            }

            if ($this->checkOsCommerce($dir, $version)) {
               $this->addCms(CMS_OSCOMMERCE, $version);
            }

            if ($this->checkDrupal($dir, $version)) {
               $this->addCms(CMS_DRUPAL, $version);
            }

            if ($this->checkMODX($dir, $version)) {
               $this->addCms(CMS_MODX, $version);
            }

            if ($this->checkInstantCms($dir, $version)) {
               $this->addCms(CMS_INSTANTCMS, $version);
            }

            if ($this->checkPhpBb($dir, $version)) {
               $this->addCms(CMS_PHPBB, $version);
            }

            if ($this->checkVBulletin($dir, $version)) {
               $this->addCms(CMS_VBULLETIN, $version);
            }

            if ($this->checkPhpShopScript($dir, $version)) {
               $this->addCms(CMS_SHOPSCRIPT, $version);
            }

        }
    }

    function getDirList($target) {
       $remove = array('.', '..'); 
       $directories = array_diff(scandir($target), $remove);

       $res = array();
           
       foreach($directories as $value) 
       { 
          if(is_dir($target . '/' . $value)) 
          {
             $res[] = $target . '/' . $value; 
          } 
       }

       return $res;
    }

    function isCms($name, $version) {
		for ($i = 0; $i < count($this->types); $i++) {
			if ((strpos($this->types[$i], $name) !== false) 
				&& 
			    (strpos($this->versions[$i], $version) !== false)) {
				return true;
			}
		}
    	
		return false;
    }

    function getCmsList() {
      return $this->types;
    }

    function getCmsVersions() {
      return $this->versions;
    }

    function getCmsNumber() {
      return count($this->types);
    }

    function getCmsName($index = 0) {
      return $this->types[$index];
    }

    function getCmsVersion($index = 0) {
      return $this->versions[$index];
    }

    private function addCms($type, $version) {
       $this->types[] = $type;
       $this->versions[] = $version;
    }

    private function checkBitrix($dir, &$version) {
       $version = CMS_VERSION_UNDEFINED;
       $res = false;

       if (file_exists($dir .'/bitrix')) {
          $res = true;

          $tmp_content = @file_get_contents($this->root_path .'/bitrix/modules/main/classes/general/version.php');
          if (preg_match('|define\("SM_VERSION","(.+?)"\)|smi', $tmp_content, $tmp_ver)) {
             $version = $tmp_ver[1];
          }

       }

       return $res;
    }

    private function checkWordpress($dir, &$version) {
       $version = CMS_VERSION_UNDEFINED;
       $res = false;

       if (file_exists($dir .'/wp-admin')) {
          $res = true;

          $tmp_content = @file_get_contents($dir .'/wp-includes/version.php');
          if (preg_match('|\$wp_version\s*=\s*\'(.+?)\'|smi', $tmp_content, $tmp_ver)) {
             $version = $tmp_ver[1];
          }
       }

       return $res;
    }

    private function checkJoomla($dir, &$version) {
       $version = CMS_VERSION_UNDEFINED;
       $res = false;

       if (file_exists($dir .'/libraries/joomla')) {
          $res = true;

          // for 1.5.x
          $tmp_content = @file_get_contents($dir .'/libraries/joomla/version.php');
          if (preg_match('|var\s+\$RELEASE\s*=\s*\'(.+?)\'|smi', $tmp_content, $tmp_ver)) {
             $version = $tmp_ver[1];

             if (preg_match('|var\s+\$DEV_LEVEL\s*=\s*\'(.+?)\'|smi', $tmp_content, $tmp_ver)) {
                $version .= '.' . $tmp_ver[1];
             }
          }

          // for 1.7.x
          $tmp_content = @file_get_contents($dir .'/includes/version.php');
          if (preg_match('|public\s+\$RELEASE\s*=\s*\'(.+?)\'|smi', $tmp_content, $tmp_ver)) {
             $version = $tmp_ver[1];

             if (preg_match('|public\s+\$DEV_LEVEL\s*=\s*\'(.+?)\'|smi', $tmp_content, $tmp_ver)) {
                $version .= '.' . $tmp_ver[1];
             }
          }


	  // for 2.5.x and 3.x 
          $tmp_content = @file_get_contents($dir . '/libraries/cms/version/version.php');
   
          if (preg_match('|const\s+RELEASE\s*=\s*\'(.+?)\'|smi', $tmp_content, $tmp_ver)) {
	      $version = $tmp_ver[1];
 
             if (preg_match('|const\s+DEV_LEVEL\s*=\s*\'(.+?)\'|smi', $tmp_content, $tmp_ver)) { 
		$version .= '.' . $tmp_ver[1];
             }
          }

       }

       return $res;
    }

    private function checkDle($dir, &$version) {
       $version = CMS_VERSION_UNDEFINED;
       $res = false;

       if (file_exists($dir .'/engine/engine.php')) {
          $res = true;

          $tmp_content = @file_get_contents($dir . '/engine/data/config.php');
          if (preg_match('|\'version_id\'\s*=>\s*"(.+?)"|smi', $tmp_content, $tmp_ver)) {
             $version = $tmp_ver[1];
          }

          $tmp_content = @file_get_contents($dir . '/install.php');
          if (preg_match('|\'version_id\'\s*=>\s*"(.+?)"|smi', $tmp_content, $tmp_ver)) {
             $version = $tmp_ver[1];
          }

       }

       return $res;
    }

    private function checkIpb($dir, &$version) {
       $version = CMS_VERSION_UNDEFINED;
       $res = false;

       if (file_exists($dir . '/ips_kernel')) {
          $res = true;

          $tmp_content = @file_get_contents($dir . '/ips_kernel/class_xml.php');
          if (preg_match('|IP.Board\s+v([0-9\.]+)|si', $tmp_content, $tmp_ver)) {
             $version = $tmp_ver[1];
          }

       }

       return $res;
    }

    private function checkWebAsyst($dir, &$version) {
       $version = CMS_VERSION_UNDEFINED;
       $res = false;

       if (file_exists($dir . '/wbs/installer')) {
          $res = true;

          $tmp_content = @file_get_contents($dir . '/license.txt');
          if (preg_match('|v([0-9\.]+)|si', $tmp_content, $tmp_ver)) {
             $version = $tmp_ver[1];
          }

       }

       return $res;
    }

    private function checkOsCommerce($dir, &$version) {
       $version = CMS_VERSION_UNDEFINED;
       $res = false;

       if (file_exists($dir . '/includes/version.php')) {
          $res = true;

          $tmp_content = @file_get_contents($dir . '/includes/version.php');
          if (preg_match('|([0-9\.]+)|smi', $tmp_content, $tmp_ver)) {
             $version = $tmp_ver[1];
          }

       }

       return $res;
    }

    private function checkDrupal($dir, &$version) {
       $version = CMS_VERSION_UNDEFINED;
       $res = false;

       if (file_exists($dir . '/sites/all')) {
          $res = true;

          $tmp_content = @file_get_contents($dir . '/CHANGELOG.txt');
          if (preg_match('|Drupal\s+([0-9\.]+)|smi', $tmp_content, $tmp_ver)) {
             $version = $tmp_ver[1];
          }

       }

       return $res;
    }

    private function checkMODX($dir, &$version) {
       $version = CMS_VERSION_UNDEFINED;
       $res = false;

       if (file_exists($dir . '/manager/assets')) {
          $res = true;

          // no way to pick up version
       }

       return $res;
    }

    private function checkInstantCms($dir, &$version) {
       $version = CMS_VERSION_UNDEFINED;
       $res = false;

       if (file_exists($dir . '/plugins/p_usertab')) {
          $res = true;

          $tmp_content = @file_get_contents($dir . '/index.php');
          if (preg_match('|InstantCMS\s+v([0-9\.]+)|smi', $tmp_content, $tmp_ver)) {
             $version = $tmp_ver[1];
          }

       }

       return $res;
    }

    private function checkPhpBb($dir, &$version) {
       $version = CMS_VERSION_UNDEFINED;
       $res = false;

       if (file_exists($dir . '/includes/acp')) {
          $res = true;

          $tmp_content = @file_get_contents($dir . '/config.php');
          if (preg_match('|phpBB\s+([0-9\.x]+)|smi', $tmp_content, $tmp_ver)) {
             $version = $tmp_ver[1];
          }

       }

       return $res;
    }

    private function checkVBulletin($dir, &$version) {
          $version = CMS_VERSION_UNDEFINED;
          $res = false;
          if (file_exists($dir . '/core/includes/md5_sums_vbulletin.php'))
          {
                $res = true;
                require_once($dir . '/core/includes/md5_sums_vbulletin.php');
                $version = $md5_sum_versions['vb5_connect'];
          }
          else if(file_exists($dir . '/includes/md5_sums_vbulletin.php'))
          {
                $res = true;
                require_once($dir . '/includes/md5_sums_vbulletin.php');
                $version = $md5_sum_versions['vbulletin'];
          }
          return $res;
       }

    private function checkPhpShopScript($dir, &$version) {
       $version = CMS_VERSION_UNDEFINED;
       $res = false;

       if (file_exists($dir . '/install/consts.php')) {
          $res = true;

          $tmp_content = @file_get_contents($dir . '/install/consts.php');
          if (preg_match('|STRING_VERSION\',\s*\'(.+?)\'|smi', $tmp_content, $tmp_ver)) {
             $version = $tmp_ver[1];
          }

       }

       return $res;
    }
}

/**
 * Print file
*/
function printFile() {
	$l_FileName = $_GET['fn'];
	$l_CRC = isset($_GET['c']) ? (int)$_GET['c'] : 0;
	$l_Content = file_get_contents($l_FileName);
	$l_FileCRC = realCRC($l_Content);
	if ($l_FileCRC != $l_CRC) {
		echo 'Доступ запрещен.';
		exit;
	}
	
	echo '<pre>' . htmlspecialchars($l_Content) . '</pre>';
}

/**
 *
 */
function realCRC($str_in, $full = false)
{
        $in = crc32( $full ? normal($str_in) : $str_in );
        return ($in > CRC32_LIMIT) ? ($in - CRC32_DIFF) : $in;
}


/**
 * Determine php script is called from the command line interface
 * @return bool
 */
function isCli()
{
	return php_sapi_name() == 'cli';
}

function myCheckSum($str) {
   return hash('crc32b', $str);
}

 function generatePassword ($length = 9)
  {

    // start with a blank password
    $password = "";

    // define possible characters - any character in this string can be
    // picked for use in the password, so if you want to put vowels back in
    // or add special characters such as exclamation marks, this is where
    // you should do it
    $possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";

    // we refer to the length of $possible a few times, so let's grab it now
    $maxlength = strlen($possible);
  
    // check for length overflow and truncate if necessary
    if ($length > $maxlength) {
      $length = $maxlength;
    }
	
    // set up a counter for how many characters are in the password so far
    $i = 0; 
    
    // add random characters to $password until $length is reached
    while ($i < $length) { 

      // pick a random character from the possible ones
      $char = substr($possible, mt_rand(0, $maxlength-1), 1);
        
      // have we already used this character in $password?
      if (!strstr($password, $char)) { 
        // no, so it's OK to add it onto the end of whatever we've already got...
        $password .= $char;
        // ... and increase the counter by one
        $i++;
      }

    }

    // done!
    return $password;

  }

/**
 * Print to console
 * @param mixed $text
 * @param bool $add_lb Add line break
 * @return void
 */
function stdOut($text, $add_lb = true)
{
	if (!isCli())
		return;
		
	if (is_bool($text))
	{
		$text = $text ? 'true' : 'false';
	}
	else if (is_null($text))
	{
		$text = 'null';
	}
	if (!is_scalar($text))
	{
		$text = print_r($text, true);
	}

 	if (!BOOL_RESULT)
 	{
 		@fwrite(STDOUT, $text . ($add_lb ? "\n" : ''));
 	}
}

/**
 * Print progress
 * @param int $num Current file
 */
function printProgress($num, &$par_File)
{
	global $g_CriticalPHP, $g_Base64, $g_Phishing, $g_CriticalJS, $g_Iframer, $g_UpdatedJsonLog, 
               $g_AddPrefix, $g_NoPrefix;

	$total_files = $GLOBALS['g_FoundTotalFiles'];
	$elapsed_time = microtime(true) - START_TIME;
	$percent = number_format($total_files ? $num * 100 / $total_files : 0, 1);
	$stat = '';
	if ($elapsed_time >= 1)
	{
		$elapsed_seconds = round($elapsed_time, 0);
		$fs = floor($num / $elapsed_seconds);
		$left_files = $total_files - $num;
		if ($fs > 0) 
		{
		   $left_time = ($left_files / $fs); //ceil($left_files / $fs);
		   $stat = ' [Avg: ' . round($fs,2) . ' files/s' . ($left_time > 0  ? ' Left: ' . seconds2Human($left_time) : '') . '] [Mlw:' . (count($g_CriticalPHP) + count($g_Base64))  . '|' . (count($g_CriticalJS) + count($g_Iframer) + count($g_Phishing)) . ']';
        }
	}

        $l_FN = $g_AddPrefix . str_replace($g_NoPrefix, '', $par_File); 
	$l_FN = substr($par_File, -60);

	$text = "$percent% [$l_FN] $num of {$total_files}. " . $stat;
	$text = str_pad($text, 160, ' ', STR_PAD_RIGHT);
	stdOut(str_repeat(chr(8), 160) . $text, false);


      	$data = array('self' => __FILE__, 'started' => AIBOLIT_START_TIME, 'updated' => time(), 
                            'progress' => $percent, 'time_elapsed' => $elapsed_seconds, 
                            'time_left' => round($left_time), 'files_left' => $left_files, 
                            'files_total' => $total_files, 'current_file' => substr($g_AddPrefix . str_replace($g_NoPrefix, '', $par_File), -160));

        if (function_exists('aibolit_onProgressUpdate')) { aibolit_onProgressUpdate($data); }

	if (defined('PROGRESS_LOG_FILE') && 
           (time() - $g_UpdatedJsonLog > 1)) {
                if (function_exists('json_encode')) {
             	   file_put_contents(PROGRESS_LOG_FILE, json_encode($data));
                } else {
             	   file_put_contents(PROGRESS_LOG_FILE, serialize($data));
                }

		$g_UpdatedJsonLog = time();
        }
}

/**
 * Seconds to human readable
 * @param int $seconds
 * @return string
 */
function seconds2Human($seconds)
{
	$r = '';
	$_seconds = floor($seconds);
	$ms = $seconds - $_seconds;
	$seconds = $_seconds;
	if ($hours = floor($seconds / 3600))
	{
		$r .= $hours . (isCli() ? ' h ' : ' час ');
		$seconds = $seconds % 3600;
	}

	if ($minutes = floor($seconds / 60))
	{
		$r .= $minutes . (isCli() ? ' m ' : ' мин ');
		$seconds = $seconds % 60;
	}

	if ($minutes < 3) $r .= ' ' . $seconds + ($ms > 0 ? round($ms) : 0) . (isCli() ? ' s' : ' сек'); 

	return $r;
}

if (isCli())
{

	$cli_options = array(
                'c:' => 'avdb:',
		'm:' => 'memory:',
		's:' => 'size:',
		'a' => 'all',
		'd:' => 'delay:',
		'l:' => 'list:',
		'r:' => 'report:',
		'f' => 'fast',
		'j:' => 'file:',
		'p:' => 'path:',
		'q' => 'quite',
		'e:' => 'cms:',
		'x:' => 'mode:',
		'k:' => 'skip:',
		'i:' => 'idb:',
		'n' => 'sc',
		'o:' => 'json_report:',
		't:' => 'php_report:',
		'z:' => 'progress:',
		'g:' => 'handler:',
		'b' => 'smart',
		'h' => 'help',
	);

	$cli_longopts = array(
		'avdb:',
		'cmd:',
		'noprefix:',
		'addprefix:',
		'scan:',
		'one-pass',
		'smart',
		'quarantine',
		'with-2check',
		'skip-cache',
		'imake',
		'icheck'
	);
	
	$cli_longopts = array_merge($cli_longopts, array_values($cli_options));

	$options = getopt(implode('', array_keys($cli_options)), $cli_longopts);

	if (isset($options['h']) OR isset($options['help']))
	{
		$memory_limit = ini_get('memory_limit');
		echo <<<HELP
AI-Bolit - Professional Malware File Scanner.

Usage: php {$_SERVER['PHP_SELF']} [OPTIONS] [PATH]
Current default path is: {$defaults['path']}

  -j, --file=FILE      		Full path to single file to check
  -l, --list=FILE      		Full path to create plain text file with a list of found malware
  -o, --json_report=FILE	Full path to create json-file with a list of found malware
  -p, --path=PATH      		Directory path to scan, by default the file directory is used
                       		Current path: {$defaults['path']}
  -m, --memory=SIZE    		Maximum amount of memory a script may consume. Current value: $memory_limit
                       		Can take shorthand byte values (1M, 1G...)
  -s, --size=SIZE      		Scan files are smaller than SIZE. 0 - All files. Current value: {$defaults['max_size_to_scan']}
  -a, --all            		Scan all files (by default scan. js,. php,. html,. htaccess)
  -d, --delay=INT      		Delay in milliseconds when scanning files to reduce load on the file system (Default: 1)
  -x, --mode=INT       		Set scan mode. 0 - for basic, 1 - for expert and 2 for paranoic.
  -k, --skip=jpg,...   		Skip specific extensions. E.g. --skip=jpg,gif,png,xls,pdf
      --scan=php,...   		Scan only specific extensions. E.g. --scan=php,htaccess,js
  -r, --report=PATH/EMAILS
  -z, --progress=FILE  		Runtime progress of scanning, saved to the file, full path required. 
  -g, --hander=FILE    		External php handler for different events, full path to php file required.
      --cmd="command [args...]"
      --smart                   Enable smart mode (skip cache files and optimize scanning)
                       		Run command after scanning
      --one-pass       		Do not calculate remaining time
      --quarantine     		Archive all malware from report
      --with-2check    		Create or use AI-BOLIT-DOUBLECHECK.php file
      --imake
      --icheck
      --idb=file	   	Integrity Check database file

      --help           		Display this help and exit

* Mandatory arguments listed below are required for both full and short way of usage.

HELP;
		exit;
	}

	$l_FastCli = false;
	
	if (
		(isset($options['memory']) AND !empty($options['memory']) AND ($memory = $options['memory']))
		OR (isset($options['m']) AND !empty($options['m']) AND ($memory = $options['m']))
	)
	{
		$memory = getBytes($memory);
		if ($memory > 0)
		{
			$defaults['memory_limit'] = $memory;
			ini_set('memory_limit', $memory);
		}
	}


	$avdb = '';
	if (
		(isset($options['avdb']) AND !empty($options['avdb']) AND ($avdb = $options['avdb']))
		OR (isset($options['c']) AND !empty($options['c']) AND ($avdb = $options['c']))
	)
	{
		if (file_exists($avdb))
		{
			$defaults['avdb'] = $avdb;
		}
	}

	if (
		(isset($options['file']) AND !empty($options['file']) AND ($file = $options['file']) !== false)
		OR (isset($options['j']) AND !empty($options['j']) AND ($file = $options['j']) !== false)
	)
	{
		define('SCAN_FILE', $file);
	}


	if (
		(isset($options['list']) AND !empty($options['list']) AND ($file = $options['list']) !== false)
		OR (isset($options['l']) AND !empty($options['l']) AND ($file = $options['l']) !== false)
	)
	{

		define('PLAIN_FILE', $file);
	}

	if (
		(isset($options['json_report']) AND !empty($options['json_report']) AND ($file = $options['json_report']) !== false)
		OR (isset($options['o']) AND !empty($options['o']) AND ($file = $options['o']) !== false)
	)
	{
		define('JSON_FILE', $file);
	}

	if (
		(isset($options['php_report']) AND !empty($options['php_report']) AND ($file = $options['php_report']) !== false)
		OR (isset($options['t']) AND !empty($options['t']) AND ($file = $options['t']) !== false)
	)
	{
		define('PHP_FILE', $file);
	}

	if (isset($options['smart']) OR isset($options['b']))
	{
		define('SMART_SCAN', 1);
	}

	if (
		(isset($options['handler']) AND !empty($options['handler']) AND ($file = $options['handler']) !== false)
		OR (isset($options['g']) AND !empty($options['g']) AND ($file = $options['g']) !== false)
	)
	{
	        if (file_exists($file)) {
		   define('AIBOLIT_EXTERNAL_HANDLER', $file);
                }
	}

	if (
		(isset($options['progress']) AND !empty($options['progress']) AND ($file = $options['progress']) !== false)
		OR (isset($options['z']) AND !empty($options['z']) AND ($file = $options['z']) !== false)
	)
	{
		define('PROGRESS_LOG_FILE', $file);
	}
	if (
		(isset($options['size']) AND !empty($options['size']) AND ($size = $options['size']) !== false)
		OR (isset($options['s']) AND !empty($options['s']) AND ($size = $options['s']) !== false)
	)
	{
		$size = getBytes($size);
		$defaults['max_size_to_scan'] = $size > 0 ? $size : 0;
	}

 	if (
 		(isset($options['file']) AND !empty($options['file']) AND ($file = $options['file']) !== false)
 		OR (isset($options['j']) AND !empty($options['j']) AND ($file = $options['j']) !== false)
 		AND (isset($options['q'])) 
 	
 	)
 	{
 		$BOOL_RESULT = true;
 	}
 
	if (isset($options['f'])) 
	{
	   $l_FastCli = true;
	}
		
	if (isset($options['q']) || isset($options['quite'])) 
	{
 	    $BOOL_RESULT = true;
	}

        if (isset($options['x'])) {
            define('AI_EXPERT', $options['x']);
        } else if (isset($options['mode'])) {
            define('AI_EXPERT', $options['mode']);
        } else {
            define('AI_EXPERT', AI_EXPERT_MODE); 
        }

        if (AI_EXPERT < 2) {
           $g_SpecificExt = true;
           $defaults['scan_all_files'] = false;
        } else {
           $defaults['scan_all_files'] = true;
        }	

	define('BOOL_RESULT', $BOOL_RESULT);

	if (
		(isset($options['delay']) AND !empty($options['delay']) AND ($delay = $options['delay']) !== false)
		OR (isset($options['d']) AND !empty($options['d']) AND ($delay = $options['d']) !== false)
	)
	{
		$delay = (int) $delay;
		if (!($delay < 0))
		{
			$defaults['scan_delay'] = $delay;
		}
	}

	if (
		(isset($options['skip']) AND !empty($options['skip']) AND ($ext_list = $options['skip']) !== false)
		OR (isset($options['k']) AND !empty($options['k']) AND ($ext_list = $options['k']) !== false)
	)
	{
		$defaults['skip_ext'] = $ext_list;
	}

	if (isset($options['n']) OR isset($options['skip-cache']))
	{
		$defaults['skip_cache'] = true;
	}

	if (isset($options['scan']))
	{
		$ext_list = strtolower(trim($options['scan'], " ,\t\n\r\0\x0B"));
		if ($ext_list != '')
		{
			$l_FastCli = true;
			$g_SensitiveFiles = explode(",", $ext_list);
			for ($i = 0; $i < count($g_SensitiveFiles); $i++) {
			   if ($g_SensitiveFiles[$i] == '.') {
                              $g_SensitiveFiles[$i] = '';
                           }
                        }

			$g_SpecificExt = true;
		}
	}


    if (isset($options['all']) OR isset($options['a']))
    {
    	$defaults['scan_all_files'] = true;
        $g_SpecificExt = false;
    }

    if (isset($options['cms'])) {
        define('CMS', $options['cms']);
    } else if (isset($options['e'])) {
        define('CMS', $options['e']);
    }


    if (!defined('SMART_SCAN')) {
       define('SMART_SCAN', 1);
    }


	$l_SpecifiedPath = false;
	if (
		(isset($options['path']) AND !empty($options['path']) AND ($path = $options['path']) !== false)
		OR (isset($options['p']) AND !empty($options['p']) AND ($path = $options['p']) !== false)
	)
	{
		$defaults['path'] = $path;
		$l_SpecifiedPath = true;
	}

	if (
		isset($options['noprefix']) AND !empty($options['noprefix']) AND ($g_NoPrefix = $options['noprefix']) !== false)
		
	{
	} else {
		$g_NoPrefix = '';
	}

	if (
		isset($options['addprefix']) AND !empty($options['addprefix']) AND ($g_AddPrefix = $options['addprefix']) !== false)
		
	{
	} else {
		$g_AddPrefix = '';
	}



	$l_SuffixReport = str_replace('/var/www', '', $defaults['path']);
	$l_SuffixReport = str_replace('/home', '', $l_SuffixReport);
        $l_SuffixReport = preg_replace('#[/\\\.\s]#', '_', $l_SuffixReport);
	$l_SuffixReport .=  "-" . rand(1, 999999);
		
	if (
		(isset($options['report']) AND ($report = $options['report']) !== false)
		OR (isset($options['r']) AND ($report = $options['r']) !== false)
	)
	{
		$report = str_replace('@PATH@', $l_SuffixReport, $report);
		$report = str_replace('@RND@', rand(1, 999999), $report);
		$report = str_replace('@DATE@', date('d-m-Y-h-i'), $report);
		define('REPORT', $report);
		define('NEED_REPORT', true);
	}

	if (
		(isset($options['idb']) AND ($ireport = $options['idb']) !== false)
	)
	{
		$ireport = str_replace('@PATH@', $l_SuffixReport, $ireport);
		$ireport = str_replace('@RND@', rand(1, 999999), $ireport);
		$ireport = str_replace('@DATE@', date('d-m-Y-h-i'), $ireport);
		define('INTEGRITY_DB_FILE', $ireport);
	}

  
	defined('REPORT') OR define('REPORT', 'AI-BOLIT-REPORT-' . $l_SuffixReport . '-' . date('d-m-Y_H-i') . '.html');
	
	defined('INTEGRITY_DB_FILE') OR define('INTEGRITY_DB_FILE', 'AINTEGRITY-' . $l_SuffixReport . '-' . date('d-m-Y_H-i'));

	$last_arg = max(1, sizeof($_SERVER['argv']) - 1);
	if (isset($_SERVER['argv'][$last_arg]))
	{
		$path = $_SERVER['argv'][$last_arg];
		if (
			substr($path, 0, 1) != '-'
			AND (substr($_SERVER['argv'][$last_arg - 1], 0, 1) != '-' OR array_key_exists(substr($_SERVER['argv'][$last_arg - 1], -1), $cli_options)))
		{
			$defaults['path'] = $path;
		}
	}	
	
	
	define('ONE_PASS', isset($options['one-pass']));

	define('IMAKE', isset($options['imake']));
	define('ICHECK', isset($options['icheck']));

	if (IMAKE && ICHECK) die('One of the following options must be used --imake or --icheck.');

} else {
   define('AI_EXPERT', AI_EXPERT_MODE); 
   define('ONE_PASS', true);
}


if (isset($defaults['avdb']) && file_exists($defaults['avdb'])) {
   $avdb = explode("\n", gzinflate(base64_decode(str_rot13(strrev(trim(file_get_contents($defaults['avdb'])))))));

   $g_DBShe = explode("\n", base64_decode($avdb[0]));
   $gX_DBShe = explode("\n", base64_decode($avdb[1]));
   $g_FlexDBShe = explode("\n", base64_decode($avdb[2]));
   $gX_FlexDBShe = explode("\n", base64_decode($avdb[3]));
   $gXX_FlexDBShe = explode("\n", base64_decode($avdb[4]));
   $g_ExceptFlex = explode("\n", base64_decode($avdb[5]));
   $g_AdwareSig = explode("\n", base64_decode($avdb[6]));
   $g_PhishingSig = explode("\n", base64_decode($avdb[7]));
   $g_JSVirSig = explode("\n", base64_decode($avdb[8]));
   $gX_JSVirSig = explode("\n", base64_decode($avdb[9]));
   $g_SusDB = explode("\n", base64_decode($avdb[10]));
   $g_SusDBPrio = explode("\n", base64_decode($avdb[11]));
   $g_DeMapper = array_combine(explode("\n", base64_decode($avdb[12])), explode("\n", base64_decode($avdb[13])));

   if (count($g_DBShe) <= 1) {
      $g_DBShe = array();
   }

   if (count($gX_DBShe) <= 1) {
      $gX_DBShe = array();
   }

   if (count($g_FlexDBShe) <= 1) {
      $g_FlexDBShe = array();
   }

   if (count($gX_FlexDBShe) <= 1) {
      $gX_FlexDBShe = array();
   }

   if (count($gXX_FlexDBShe) <= 1) {
      $gXX_FlexDBShe = array();
   }

   if (count($g_ExceptFlex) <= 1) {
      $g_ExceptFlex = array();
   }

   if (count($g_AdwareSig) <= 1) {
      $g_AdwareSig = array();
   }

   if (count($g_PhishingSig) <= 1) {
      $g_PhishingSig = array();
   }

   if (count($gX_JSVirSig) <= 1) {
      $gX_JSVirSig = array();
   }

   if (count($g_JSVirSig) <= 1) {
      $g_JSVirSig = array();
   }

   if (count($g_SusDB) <= 1) {
      $g_SusDB = array();
   }

   if (count($g_SusDBPrio) <= 1) {
      $g_SusDBPrio = array();
   }

   stdOut('Loaded external signatures from ' . $defaults['avdb']);
}

// use only basic signature subset
if (AI_EXPERT < 2) {
   $gX_FlexDBShe = array();
   $gXX_FlexDBShe = array();
   $gX_JSVirSig = array();
}

stdOut('Malware signatures: ' . (count($g_JSVirSig) + count($gX_JSVirSig) + count($g_DBShe) + count($gX_DBShe) + count($gX_DBShe) + count($g_FlexDBShe) + count($gX_FlexDBShe) + count($gXX_FlexDBShe)));

if ($g_SpecificExt) {
  stdOut("Scan specific extensions: " . implode(',', $g_SensitiveFiles));
}

if (!DEBUG_PERFORMANCE) {
   OptimizeSignatures();
} else {
   stdOut("Debug Performance Scan");
}

$g_DBShe  = array_map('strtolower', $g_DBShe);
$gX_DBShe = array_map('strtolower', $gX_DBShe);

if (!defined('PLAIN_FILE')) { define('PLAIN_FILE', ''); }

// Init
define('MAX_ALLOWED_PHP_HTML_IN_DIR', 600);
define('BASE64_LENGTH', 69);
define('MAX_PREVIEW_LEN', 80);
define('MAX_EXT_LINKS', 1001);

if (defined('AIBOLIT_EXTERNAL_HANDLER')) {
   include_once(AIBOLIT_EXTERNAL_HANDLER);
   stdOut("\nLoaded external handler: " . AIBOLIT_EXTERNAL_HANDLER . "\n");
   if (function_exists("aibolit_onStart")) { aibolit_onStart(); }
}

// Perform full scan when running from command line
if (isset($_GET['full'])) {
  $defaults['scan_all_files'] = 1;
}

if ($l_FastCli) {
  $defaults['scan_all_files'] = 0; 
}

if (!isCli()) {
  	define('ICHECK', isset($_GET['icheck']));
  	define('IMAKE', isset($_GET['imake']));
	
	define('INTEGRITY_DB_FILE', 'ai-integrity-db');
}

define('SCAN_ALL_FILES', (bool) $defaults['scan_all_files']);
define('SCAN_DELAY', (int) $defaults['scan_delay']);
define('MAX_SIZE_TO_SCAN', getBytes($defaults['max_size_to_scan']));

if ($defaults['memory_limit'] AND ($defaults['memory_limit'] = getBytes($defaults['memory_limit'])) > 0) {
	ini_set('memory_limit', $defaults['memory_limit']);
    stdOut("Changed memory limit to " . $defaults['memory_limit']);
}

define('ROOT_PATH', realpath($defaults['path']));

if (!ROOT_PATH)
{
    if (isCli())  {
		die(stdOut("Directory '{$defaults['path']}' not found!"));
	}
}
elseif(!is_readable(ROOT_PATH))
{
        if (isCli())  {
		die2(stdOut("Cannot read directory '" . ROOT_PATH . "'!"));
	}
}

define('CURRENT_DIR', getcwd());
chdir(ROOT_PATH);

if (isCli() AND REPORT !== '' AND !getEmails(REPORT))
{
	$report = str_replace('\\', '/', REPORT);
	$abs = strpos($report, '/') === 0 ? DIR_SEPARATOR : '';
	$report = array_values(array_filter(explode('/', $report)));
	$report_file = array_pop($report);
	$report_path = realpath($abs . implode(DIR_SEPARATOR, $report));

	define('REPORT_FILE', $report_file);
	define('REPORT_PATH', $report_path);

	if (REPORT_FILE AND REPORT_PATH AND is_file(REPORT_PATH . DIR_SEPARATOR . REPORT_FILE))
	{
		@unlink(REPORT_PATH . DIR_SEPARATOR . REPORT_FILE);
	}
}

if (defined('REPORT_PATH')) {
   $l_ReportDirName = REPORT_PATH;
}

define('QUEUE_FILENAME', ($l_ReportDirName != '' ? $l_ReportDirName . '/' : '') . 'AI-BOLIT-QUEUE-' . md5($defaults['path']) . '-' . rand(1000,9999) . '.txt');

if (function_exists('phpinfo')) {
   ob_start();
   phpinfo();
   $l_PhpInfo = ob_get_contents();
   ob_end_clean();

   $l_PhpInfo = str_replace('border: 1px', '', $l_PhpInfo);
   preg_match('|<body>(.*)</body>|smi', $l_PhpInfo, $l_PhpInfoBody);
}

////////////////////////////////////////////////////////////////////////////
$l_Template = str_replace("@@MODE@@", AI_EXPERT . '/' . SMART_SCAN, $l_Template);

if (AI_EXPERT == 0) {
   $l_Result .= '<div class="rep">' . AI_STR_057 . '</div>'; 
} else {
}

$l_Template = str_replace('@@HEAD_TITLE@@', AI_STR_051 . $g_AddPrefix . str_replace($g_NoPrefix, '', ROOT_PATH), $l_Template);

define('QCR_INDEX_FILENAME', 'fn');
define('QCR_INDEX_TYPE', 'type');
define('QCR_INDEX_WRITABLE', 'wr');
define('QCR_SVALUE_FILE', '1');
define('QCR_SVALUE_FOLDER', '0');

/**
 * Extract emails from the string
 * @param string $email
 * @return array of strings with emails or false on error
 */
function getEmails($email)
{
	$email = preg_split('#[,\s;]#', $email, -1, PREG_SPLIT_NO_EMPTY);
	$r = array();
	for ($i = 0, $size = sizeof($email); $i < $size; $i++)
	{
	        if (function_exists('filter_var')) {
   		   if (filter_var($email[$i], FILTER_VALIDATE_EMAIL))
   		   {
   		   	$r[] = $email[$i];
    		   }
                } else {
                   // for PHP4
                   if (strpos($email[$i], '@') !== false) {
   		   	$r[] = $email[$i];
                   }
                }
	}
	return empty($r) ? false : $r;
}

/**
 * Get bytes from shorthand byte values (1M, 1G...)
 * @param int|string $val
 * @return int
 */
function getBytes($val)
{
	$val = trim($val);
	$last = strtolower($val{strlen($val) - 1});
	switch($last) {
		case 't':
			$val *= 1024;
		case 'g':
			$val *= 1024;
		case 'm':
			$val *= 1024;
		case 'k':
			$val *= 1024;
	}
	return intval($val);
}

/**
 * Format bytes to human readable
 * @param int $bites
 * @return string
 */
function bytes2Human($bites)
{
	if ($bites < 1024)
	{
		return $bites . ' b';
	}
	elseif (($kb = $bites / 1024) < 1024)
	{
		return number_format($kb, 2) . ' Kb';
	}
	elseif (($mb = $kb / 1024) < 1024)
	{
		return number_format($mb, 2) . ' Mb';
	}
	elseif (($gb = $mb / 1024) < 1024)
	{
		return number_format($gb, 2) . ' Gb';
	}
	else
	{
		return number_format($gb / 1024, 2) . 'Tb';
	}
}

///////////////////////////////////////////////////////////////////////////
function needIgnore($par_FN, $par_CRC) {
  global $g_IgnoreList;
  
  for ($i = 0; $i < count($g_IgnoreList); $i++) {
     if (strpos($par_FN, $g_IgnoreList[$i][0]) !== false) {
		if ($par_CRC == $g_IgnoreList[$i][1]) {
			return true;
		}
	 }
  }
  
  return false;
}

///////////////////////////////////////////////////////////////////////////
function makeSafeFn($par_Str, $replace_path = false) {
  global $g_AddPrefix, $g_NoPrefix;
  if ($replace_path) {
     $lines = explode("\n", $par_Str);
     array_walk($lines, function(&$n) {
          global $g_AddPrefix, $g_NoPrefix;
          $n = $g_AddPrefix . str_replace($g_NoPrefix, '', $n); 
     }); 

     $par_Str = implode("\n", $lines);
  }
 
  return htmlspecialchars($par_Str, ENT_SUBSTITUTE | ENT_QUOTES);
}

function replacePathArray($par_Arr) {
  global $g_AddPrefix, $g_NoPrefix;
     array_walk($par_Arr, function(&$n) {
          global $g_AddPrefix, $g_NoPrefix;
          $n = $g_AddPrefix . str_replace($g_NoPrefix, '', $n); 
     }); 

  return $par_Arr;
}

///////////////////////////////////////////////////////////////////////////
function getRawJsonVuln($par_List) {
  global $g_Structure, $g_NoPrefix, $g_AddPrefix;
   $results = array();
   $l_Src = array('&quot;', '&lt;', '&gt;', '&amp;', '&#039;', '<' . '?php.');
   $l_Dst = array('"',      '<',    '>',    '&', '\'',         '<' . '?php ');

   for ($i = 0; $i < count($par_List); $i++) {
      $l_Pos = $par_List[$i]['ndx'];
      $res['fn'] = $g_AddPrefix . str_replace($g_NoPrefix, '', $g_Structure['n'][$l_Pos]);
      $res['sig'] = $par_List[$i]['id'];

      $res['ct'] = $g_Structure['c'][$l_Pos];
      $res['mt'] = $g_Structure['m'][$l_Pos];
      $res['sz'] = $g_Structure['s'][$l_Pos];
      $res['sigid'] = 'vuln_' . md5($g_Structure['n'][$l_Pos] . $par_List[$i]['id']);

      $results[] = $res; 
   }

   return $results;
}

///////////////////////////////////////////////////////////////////////////
function getRawJson($par_List, $par_Details = null, $par_SigId = null) {
  global $g_Structure, $g_NoPrefix, $g_AddPrefix;
   $results = array();
   $l_Src = array('&quot;', '&lt;', '&gt;', '&amp;', '&#039;', '<' . '?php.');
   $l_Dst = array('"',      '<',    '>',    '&', '\'',         '<' . '?php ');

   for ($i = 0; $i < count($par_List); $i++) {
       if ($par_SigId != null) {
          $l_SigId = 'id_' . $par_SigId[$i];
       } else {
          $l_SigId = 'id_n' . rand(1000000, 9000000);
       }
       


      $l_Pos = $par_List[$i];
      $res['fn'] = $g_AddPrefix . str_replace($g_NoPrefix, '', $g_Structure['n'][$l_Pos]);
      if ($par_Details != null) {
         $res['sig'] = preg_replace('|(L\d+).+__AI_MARKER__|smi', '[$1]: ...', $par_Details[$i]);
         $res['sig'] = preg_replace('/[^\x20-\x7F]/', '.', $res['sig']);
         $res['sig'] = preg_replace('/__AI_LINE1__(\d+)__AI_LINE2__/', '[$1] ', $res['sig']);
         $res['sig'] = preg_replace('/__AI_MARKER__/', ' @!!!>', $res['sig']);
         $res['sig'] = str_replace($l_Src, $l_Dst, $res['sig']);
      }

      $res['ct'] = $g_Structure['c'][$l_Pos];
      $res['mt'] = $g_Structure['m'][$l_Pos];
      $res['sz'] = $g_Structure['s'][$l_Pos];
      $res['sigid'] = $l_SigId;

      $results[] = $res; 
   }

   return $results;
}

///////////////////////////////////////////////////////////////////////////
function printList($par_List, $par_Details = null, $par_NeedIgnore = false, $par_SigId = null, $par_TableName = null) {
  global $g_Structure, $g_NoPrefix, $g_AddPrefix;
  
  $i = 0;

  if ($par_TableName == null) {
     $par_TableName = 'table_' . rand(1000000,9000000);
  }

  $l_Result = '';
  $l_Result .= "<div class=\"flist\"><table cellspacing=1 cellpadding=4 border=0 id=\"" . $par_TableName . "\">";

  $l_Result .= "<thead><tr class=\"tbgh" . ( $i % 2 ). "\">";
  $l_Result .= "<th width=70%>" . AI_STR_004 . "</th>";
  $l_Result .= "<th>" . AI_STR_005 . "</th>";
  $l_Result .= "<th>" . AI_STR_006 . "</th>";
  $l_Result .= "<th width=90>" . AI_STR_007 . "</th>";
  $l_Result .= "<th width=0 class=\"hidd\">CRC32</th>";
  $l_Result .= "<th width=0 class=\"hidd\"></th>";
  $l_Result .= "<th width=0 class=\"hidd\"></th>";
  $l_Result .= "<th width=0 class=\"hidd\"></th>";
  
  $l_Result .= "</tr></thead><tbody>";

  for ($i = 0; $i < count($par_List); $i++) {
    if ($par_SigId != null) {
       $l_SigId = 'id_' . $par_SigId[$i];
    } else {
       $l_SigId = 'id_z' . rand(1000000,9000000);
    }
    
    $l_Pos = $par_List[$i];
        if ($par_NeedIgnore) {
         	if (needIgnore($g_Structure['n'][$par_List[$i]], $g_Structure['crc'][$l_Pos])) {
         		continue;
         	}
        }
  
     $l_Creat = $g_Structure['c'][$l_Pos] > 0 ? date("d/m/Y H:i:s", $g_Structure['c'][$l_Pos]) : '-';
     $l_Modif = $g_Structure['m'][$l_Pos] > 0 ? date("d/m/Y H:i:s", $g_Structure['m'][$l_Pos]) : '-';
     $l_Size = $g_Structure['s'][$l_Pos] > 0 ? bytes2Human($g_Structure['s'][$l_Pos]) : '-';

     if ($par_Details != null) {
        $l_WithMarker = preg_replace('|__AI_MARKER__|smi', '<span class="marker">&nbsp;</span>', $par_Details[$i]);
        $l_WithMarker = preg_replace('|__AI_LINE1__|smi', '<span class="line_no">', $l_WithMarker);
        $l_WithMarker = preg_replace('|__AI_LINE2__|smi', '</span>', $l_WithMarker);
		
        $l_Body = '<div class="details">';

        if ($par_SigId != null) {
           $l_Body .= '<a href="#" onclick="return hsig(\'' . $l_SigId . '\')">[x]</a> ';
        }

        $l_Body .= $l_WithMarker . '</div>';
     } else {
        $l_Body = '';
     }

     $l_Result .= '<tr class="tbg' . ( $i % 2 ). '" o="' . $l_SigId .'">';
	 
	 if (is_file($g_Structure['n'][$l_Pos])) {
//		$l_Result .= '<td><div class="it"><a class="it" target="_blank" href="'. $defaults['site_url'] . 'ai-bolit.php?fn=' .
//	              $g_Structure['n'][$l_Pos] . '&ph=' . realCRC(PASS) . '&c=' . $g_Structure['crc'][$l_Pos] . '">' . $g_Structure['n'][$l_Pos] . '</a></div>' . $l_Body . '</td>';
		$l_Result .= '<td><div class="it"><a class="it">' . makeSafeFn($g_AddPrefix . str_replace($g_NoPrefix, '', $g_Structure['n'][$l_Pos])) . '</a></div>' . $l_Body . '</td>';
	 } else {
		$l_Result .= '<td><div class="it"><a class="it">' . makeSafeFn($g_AddPrefix . str_replace($g_NoPrefix, '', $g_Structure['n'][$par_List[$i]])) . '</a></div></td>';
	 }
	 
     $l_Result .= '<td align=center><div class="ctd">' . $l_Creat . '</div></td>';
     $l_Result .= '<td align=center><div class="ctd">' . $l_Modif . '</div></td>';
     $l_Result .= '<td align=center><div class="ctd">' . $l_Size . '</div></td>';
     $l_Result .= '<td class="hidd"><div class="hidd">' . $g_Structure['crc'][$l_Pos] . '</div></td>';
     $l_Result .= '<td class="hidd"><div class="hidd">' . 'x' . '</div></td>';
     $l_Result .= '<td class="hidd"><div class="hidd">' . $g_Structure['m'][$l_Pos] . '</div></td>';
     $l_Result .= '<td class="hidd"><div class="hidd">' . $l_SigId . '</div></td>';
     $l_Result .= '</tr>';

  }

  $l_Result .= "</tbody></table></div><div class=clear style=\"margin: 20px 0 0 0\"></div>";

  return $l_Result;
}

///////////////////////////////////////////////////////////////////////////
function printPlainList($par_List, $par_Details = null, $par_NeedIgnore = false, $par_SigId = null, $par_TableName = null) {
  global $g_Structure, $g_NoPrefix, $g_AddPrefix;
  
  $l_Result = "";

  $l_Src = array('&quot;', '&lt;', '&gt;', '&amp;', '&#039;');
  $l_Dst = array('"',      '<',    '>',    '&', '\'');

  for ($i = 0; $i < count($par_List); $i++) {
    $l_Pos = $par_List[$i];
        if ($par_NeedIgnore) {
         	if (needIgnore($g_Structure['n'][$par_List[$i]], $g_Structure['crc'][$l_Pos])) {
         		continue;
         	}                      
        }
  

     if ($par_Details != null) {

        $l_Body = preg_replace('|(L\d+).+__AI_MARKER__|smi', '$1: ...', $par_Details[$i]);
        $l_Body = preg_replace('/[^\x20-\x7F]/', '.', $l_Body);
        $l_Body = str_replace($l_Src, $l_Dst, $l_Body);

     } else {
        $l_Body = '';
     }

	 if (is_file($g_Structure['n'][$l_Pos])) {		 
		$l_Result .= $g_AddPrefix . str_replace($g_NoPrefix, '', $g_Structure['n'][$l_Pos]) . "\t\t\t" . $l_Body . "\n";
	 } else {
		$l_Result .= $g_AddPrefix . str_replace($g_NoPrefix, '', $g_Structure['n'][$par_List[$i]]) . "\n";
	 }
	 
  }

  return $l_Result;
}

///////////////////////////////////////////////////////////////////////////
function extractValue(&$par_Str, $par_Name) {
  if (preg_match('|<tr><td class="e">\s*'.$par_Name.'\s*</td><td class="v">(.+?)</td>|sm', $par_Str, $l_Result)) {
     return str_replace('no value', '', strip_tags($l_Result[1]));
  }
}

///////////////////////////////////////////////////////////////////////////
function QCR_ExtractInfo($par_Str) {
   $l_PhpInfoSystem = extractValue($par_Str, 'System');
   $l_PhpPHPAPI = extractValue($par_Str, 'Server API');
   $l_AllowUrlFOpen = extractValue($par_Str, 'allow_url_fopen');
   $l_AllowUrlInclude = extractValue($par_Str, 'allow_url_include');
   $l_DisabledFunction = extractValue($par_Str, 'disable_functions');
   $l_DisplayErrors = extractValue($par_Str, 'display_errors');
   $l_ErrorReporting = extractValue($par_Str, 'error_reporting');
   $l_ExposePHP = extractValue($par_Str, 'expose_php');
   $l_LogErrors = extractValue($par_Str, 'log_errors');
   $l_MQGPC = extractValue($par_Str, 'magic_quotes_gpc');
   $l_MQRT = extractValue($par_Str, 'magic_quotes_runtime');
   $l_OpenBaseDir = extractValue($par_Str, 'open_basedir');
   $l_RegisterGlobals = extractValue($par_Str, 'register_globals');
   $l_SafeMode = extractValue($par_Str, 'safe_mode');


   $l_DisabledFunction = ($l_DisabledFunction == '' ? '-?-' : $l_DisabledFunction);
   $l_OpenBaseDir = ($l_OpenBaseDir == '' ? '-?-' : $l_OpenBaseDir);

   $l_Result = '<div class="title">' . AI_STR_008 . ': ' . phpversion() . '</div>';
   $l_Result .= 'System Version: <span class="php_ok">' . $l_PhpInfoSystem . '</span><br/>';
   $l_Result .= 'PHP API: <span class="php_ok">' . $l_PhpPHPAPI. '</span><br/>';
   $l_Result .= 'allow_url_fopen: <span class="php_' . ($l_AllowUrlFOpen == 'On' ? 'bad' : 'ok') . '">' . $l_AllowUrlFOpen. '</span><br/>';
   $l_Result .= 'allow_url_include: <span class="php_' . ($l_AllowUrlInclude == 'On' ? 'bad' : 'ok') . '">' . $l_AllowUrlInclude. '</span><br/>';
   $l_Result .= 'disable_functions: <span class="php_' . ($l_DisabledFunction == '-?-' ? 'bad' : 'ok') . '">' . $l_DisabledFunction. '</span><br/>';
   $l_Result .= 'display_errors: <span class="php_' . ($l_DisplayErrors == 'On' ? 'ok' : 'bad') . '">' . $l_DisplayErrors. '</span><br/>';
   $l_Result .= 'error_reporting: <span class="php_ok">' . $l_ErrorReporting. '</span><br/>';
   $l_Result .= 'expose_php: <span class="php_' . ($l_ExposePHP == 'On' ? 'bad' : 'ok') . '">' . $l_ExposePHP. '</span><br/>';
   $l_Result .= 'log_errors: <span class="php_' . ($l_LogErrors == 'On' ? 'ok' : 'bad') . '">' . $l_LogErrors . '</span><br/>';
   $l_Result .= 'magic_quotes_gpc: <span class="php_' . ($l_MQGPC == 'On' ? 'ok' : 'bad') . '">' . $l_MQGPC. '</span><br/>';
   $l_Result .= 'magic_quotes_runtime: <span class="php_' . ($l_MQRT == 'On' ? 'bad' : 'ok') . '">' . $l_MQRT. '</span><br/>';
   $l_Result .= 'register_globals: <span class="php_' . ($l_RegisterGlobals == 'On' ? 'bad' : 'ok') . '">' . $l_RegisterGlobals . '</span><br/>';
   $l_Result .= 'open_basedir: <span class="php_' . ($l_OpenBaseDir == '-?-' ? 'bad' : 'ok') . '">' . $l_OpenBaseDir . '</span><br/>';
   
   if (phpversion() < '5.3.0') {
      $l_Result .= 'safe_mode (PHP < 5.3.0): <span class="php_' . ($l_SafeMode == 'On' ? 'ok' : 'bad') . '">' . $l_SafeMode. '</span><br/>';
   }

   return $l_Result . '<p>';
}

///////////////////////////////////////////////////////////////////////////
   function addSlash($dir) {
      return rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
   }

///////////////////////////////////////////////////////////////////////////
function QCR_Debug($par_Str = "") {
  if (!DEBUG_MODE) {
     return;
  }

  $l_MemInfo = ' ';  
  if (function_exists('memory_get_usage')) {
     $l_MemInfo .= ' curmem=' .  bytes2Human(memory_get_usage());
  }

  if (function_exists('memory_get_peak_usage')) {
     $l_MemInfo .= ' maxmem=' .  bytes2Human(memory_get_peak_usage());
  }

  stdOut("\n" . date('H:i:s') . ': ' . $par_Str . $l_MemInfo . "\n");
}


///////////////////////////////////////////////////////////////////////////
function QCR_ScanDirectories($l_RootDir)
{
	global $g_Structure, $g_Counter, $g_Doorway, $g_FoundTotalFiles, $g_FoundTotalDirs, 
			$defaults, $g_SkippedFolders, $g_UrlIgnoreList, $g_DirIgnoreList, $g_UnsafeDirArray, 
                        $g_UnsafeFilesFound, $g_SymLinks, $g_HiddenFiles, $g_UnixExec, $g_IgnoredExt, $g_SensitiveFiles, 
						$g_SuspiciousFiles, $g_ShortListExt, $l_SkipSample;

	static $l_Buffer = '';

	$l_DirCounter = 0;
	$l_DoorwayFilesCounter = 0;
	$l_SourceDirIndex = $g_Counter - 1;

        $l_SkipSample = array();

	QCR_Debug('Scan ' . $l_RootDir);

        $l_QuotedSeparator = quotemeta(DIR_SEPARATOR); 
 	if ($l_DIRH = @opendir($l_RootDir))
	{
		while (($l_FileName = readdir($l_DIRH)) !== false)
		{
			if ($l_FileName == '.' || $l_FileName == '..') continue;

			$l_FileName = $l_RootDir . DIR_SEPARATOR . $l_FileName;

			$l_Type = filetype($l_FileName);
            if ($l_Type == "link") 
            {
                $g_SymLinks[] = $l_FileName;
                continue;
            } else			
			if ($l_Type != "file" && $l_Type != "dir" ) {
			        if (!in_array($l_FileName, $g_UnixExec)) {
				   $g_UnixExec[] = $l_FileName;
				}

				continue;
			}	
						
			$l_Ext = strtolower(pathinfo($l_FileName, PATHINFO_EXTENSION));
			$l_IsDir = is_dir($l_FileName);

			if (in_array($l_Ext, $g_SuspiciousFiles)) 
			{
			        if (!in_array($l_FileName, $g_UnixExec)) {
                		   $g_UnixExec[] = $l_FileName;
                                } 
            		}

			// which files should be scanned
			$l_NeedToScan = SCAN_ALL_FILES || (in_array($l_Ext, $g_SensitiveFiles));

			if (in_array(strtolower($l_Ext), $g_IgnoredExt)) {    
		           $l_NeedToScan = false;
                        }

      			// if folder in ignore list
      			$l_Skip = false;
      			for ($dr = 0; $dr < count($g_DirIgnoreList); $dr++) {
      				if (($g_DirIgnoreList[$dr] != '') &&
      				   preg_match('#' . $g_DirIgnoreList[$dr] . '#', $l_FileName, $l_Found)) {
      				   if (!in_array($g_DirIgnoreList[$dr], $l_SkipSample)) {
                                      $l_SkipSample[] = $g_DirIgnoreList[$dr];
                                   } else {
        		             $l_Skip = true;
                                     $l_NeedToScan = false;
                                   }
      				}
      			}


			if ($l_IsDir)
			{
				// skip on ignore
				if ($l_Skip) {
				   $g_SkippedFolders[] = $l_FileName;
				   continue;
				}
				
				$l_BaseName = basename($l_FileName);

				if ((strpos($l_BaseName, '.') === 0) && ($l_BaseName != '.htaccess')) {
	               $g_HiddenFiles[] = $l_FileName;
	            }

//				$g_Structure['d'][$g_Counter] = $l_IsDir;
//				$g_Structure['n'][$g_Counter] = $l_FileName;
				if (ONE_PASS) {
					$g_Structure['n'][$g_Counter] = $l_FileName . DIR_SEPARATOR;
				} else {
					$l_Buffer .= $l_FileName . DIR_SEPARATOR . "\n";
				}

				$l_DirCounter++;

				if ($l_DirCounter > MAX_ALLOWED_PHP_HTML_IN_DIR)
				{
					$g_Doorway[] = $l_SourceDirIndex;
					$l_DirCounter = -655360;
				}

				$g_Counter++;
				$g_FoundTotalDirs++;

				QCR_ScanDirectories($l_FileName);
			} else
			{
				if ($l_NeedToScan)
				{
					$g_FoundTotalFiles++;
					if (in_array($l_Ext, $g_ShortListExt)) 
					{
						$l_DoorwayFilesCounter++;
						
						if ($l_DoorwayFilesCounter > MAX_ALLOWED_PHP_HTML_IN_DIR)
						{
							$g_Doorway[] = $l_SourceDirIndex;
							$l_DoorwayFilesCounter = -655360;
						}
					}

					if (ONE_PASS) {
						QCR_ScanFile($l_FileName, $g_Counter++);
					} else {
						$l_Buffer .= $l_FileName."\n";
					}

					$g_Counter++;
				}
			}

			if (strlen($l_Buffer) > 32000)
			{ 
				file_put_contents(QUEUE_FILENAME, $l_Buffer, FILE_APPEND) or die2("Cannot write to file ".QUEUE_FILENAME);
				$l_Buffer = '';
			}

		}

		closedir($l_DIRH);
	}
	
	if (($l_RootDir == ROOT_PATH) && !empty($l_Buffer)) {
		file_put_contents(QUEUE_FILENAME, $l_Buffer, FILE_APPEND) or die2("Cannot write to file " . QUEUE_FILENAME);
		$l_Buffer = '';                                                                            
	}

}


///////////////////////////////////////////////////////////////////////////
function getFragment($par_Content, $par_Pos) {
  $l_MaxChars = MAX_PREVIEW_LEN;
  $l_MaxLen = strlen($par_Content);
  $l_RightPos = min($par_Pos + $l_MaxChars, $l_MaxLen); 
  $l_MinPos = max(0, $par_Pos - $l_MaxChars);

  $l_FoundStart = substr($par_Content, 0, $par_Pos);
  $l_FoundStart = str_replace("\r", '', $l_FoundStart);
  $l_LineNo = strlen($l_FoundStart) - strlen(str_replace("\n", '', $l_FoundStart)) + 1;

  $par_Content = preg_replace('/[\x00-\x1F\x80-\xFF]/', '~', $par_Content);

  $l_Res = '__AI_LINE1__' . $l_LineNo . "__AI_LINE2__  " . ($l_MinPos > 0 ? '…' : '') . substr($par_Content, $l_MinPos, $par_Pos - $l_MinPos) . 
           '__AI_MARKER__' . substr($par_Content, $par_Pos, $l_RightPos - $par_Pos - 1);

  $l_Res = makeSafeFn(UnwrapObfu($l_Res));
  $l_Res = str_replace('~', '·', $l_Res);
  $l_Res = preg_replace('/\s+/smi', ' ', $l_Res);
  $l_Res = str_replace('' . '?php', '' . '?php ', $l_Res);

  return $l_Res;
}

///////////////////////////////////////////////////////////////////////////
function escapedHexToHex($escaped)
{ $GLOBALS['g_EncObfu']++; return chr(hexdec($escaped[1])); }
function escapedOctDec($escaped)
{ $GLOBALS['g_EncObfu']++; return chr(octdec($escaped[1])); }
function escapedDec($escaped)
{ $GLOBALS['g_EncObfu']++; return chr($escaped[1]); }

///////////////////////////////////////////////////////////////////////////
if (!defined('T_ML_COMMENT')) {
   define('T_ML_COMMENT', T_COMMENT);
} else {
   define('T_DOC_COMMENT', T_ML_COMMENT);
}
          	
function UnwrapObfu($par_Content) {
  $GLOBALS['g_EncObfu'] = 0;
  
  $search  = array( ' ;', ' =', ' ,', ' .', ' (', ' )', ' {', ' }', '; ', '= ', ', ', '. ', '( ', '( ', '{ ', '} ', ' !', ' >', ' <', ' _', '_ ', '< ',  '> ', ' $', ' %',   '% ', '# ', ' #', '^ ', ' ^', ' &', '& ', ' ?', '? ');
  $replace = array(  ';',  '=',  ',',  '.',  '(',  ')',  '{',  '}', ';',  '=',  ',',  '.',  '(',   ')', '{',  '}',   '!',  '>',  '<',  '_', '_',  '<',   '>',   '$',  '%',   '%',  '#',   '#', '^',   '^',  '&', '&',   '?', '?');
  $par_Content = str_replace('@', '', $par_Content);
  $par_Content = preg_replace('~(\(\s*)+~', '(', $par_Content);
  $par_Content = preg_replace('~\s+~', ' ', $par_Content);
  $par_Content = str_replace($search, $replace, $par_Content);
  $par_Content = preg_replace_callback('~\bchr\(\s*([0-9a-fA-FxX]+)\s*\)~', function ($m) { return "'".chr(intval($m[1], 0))."'"; }, $par_Content );

  $par_Content = preg_replace_callback('/\\\\x([a-fA-F0-9]{1,2})/i','escapedHexToHex', $par_Content);
  $par_Content = preg_replace_callback('/\\\\([0-9]{1,3})/i','escapedOctDec', $par_Content);

  $par_Content = preg_replace('/[\'"]\s*?\.+\s*?[\'"]/smi', '', $par_Content);
  $par_Content = preg_replace('/[\'"]\s*?\++\s*?[\'"]/smi', '', $par_Content);

  $content = str_replace('<?$', '<?php$', $content);
  $content = str_replace('<?php', '<?php ', $content);

  return $par_Content;
}

///////////////////////////////////////////////////////////////////////////
// Unicode BOM is U+FEFF, but after encoded, it will look like this.
define ('UTF32_BIG_ENDIAN_BOM'   , chr(0x00) . chr(0x00) . chr(0xFE) . chr(0xFF));
define ('UTF32_LITTLE_ENDIAN_BOM', chr(0xFF) . chr(0xFE) . chr(0x00) . chr(0x00));
define ('UTF16_BIG_ENDIAN_BOM'   , chr(0xFE) . chr(0xFF));
define ('UTF16_LITTLE_ENDIAN_BOM', chr(0xFF) . chr(0xFE));
define ('UTF8_BOM'               , chr(0xEF) . chr(0xBB) . chr(0xBF));

function detect_utf_encoding($text) {
    $first2 = substr($text, 0, 2);
    $first3 = substr($text, 0, 3);
    $first4 = substr($text, 0, 3);
    
    if ($first3 == UTF8_BOM) return 'UTF-8';
    elseif ($first4 == UTF32_BIG_ENDIAN_BOM) return 'UTF-32BE';
    elseif ($first4 == UTF32_LITTLE_ENDIAN_BOM) return 'UTF-32LE';
    elseif ($first2 == UTF16_BIG_ENDIAN_BOM) return 'UTF-16BE';
    elseif ($first2 == UTF16_LITTLE_ENDIAN_BOM) return 'UTF-16LE';

    return false;
}

///////////////////////////////////////////////////////////////////////////
function QCR_SearchPHP($src)
{
  if (preg_match("/(<\?php[\w\s]{5,})/smi", $src, $l_Found, PREG_OFFSET_CAPTURE)) {
	  return $l_Found[0][1];
  }

  if (preg_match("/(<script[^>]*language\s*=\s*)('|\"|)php('|\"|)([^>]*>)/i", $src, $l_Found, PREG_OFFSET_CAPTURE)) {
    return $l_Found[0][1];
  }

  return false;
}


///////////////////////////////////////////////////////////////////////////
function knowUrl($par_URL) {
  global $g_UrlIgnoreList;

  for ($jk = 0; $jk < count($g_UrlIgnoreList); $jk++) {
     if  (stripos($par_URL, $g_UrlIgnoreList[$jk]) !== false) {
     	return true;
     }
  }

  return false;
}

///////////////////////////////////////////////////////////////////////////

function makeSummary($par_Str, $par_Number, $par_Style) {
   return '<tr><td class="' . $par_Style . '" width=400>' . $par_Str . '</td><td class="' . $par_Style . '">' . $par_Number . '</td></tr>';
}

///////////////////////////////////////////////////////////////////////////

function CheckVulnerability($par_Filename, $par_Index, $par_Content) {
    global $g_Vulnerable, $g_CmsListDetector;
	
	$l_Vuln = array();

        $par_Filename = strtolower($par_Filename);


	if (
	    (strpos($par_Filename, 'libraries/joomla/session/session.php') !== false) &&
		(strpos($par_Content, '&& filter_var($_SERVER[\'HTTP_X_FORWARDED_FOR') === false)
		) 
	{		
			$l_Vuln['id'] = 'RCE : https://docs.joomla.org/Security_hotfixes_for_Joomla_EOL_versions';
			$l_Vuln['ndx'] = $par_Index;
			$g_Vulnerable[] = $l_Vuln;
			return true;
	}

	if (
	    (strpos($par_Filename, 'administrator/components/com_media/helpers/media.php') !== false) &&
		(strpos($par_Content, '$format == \'\' || $format == false ||') === false)
		) 
	{		
		if ($g_CmsListDetector->isCms(CMS_JOOMLA, '1.5')) {
			$l_Vuln['id'] = 'AFU : https://docs.joomla.org/Security_hotfixes_for_Joomla_EOL_versions';
			$l_Vuln['ndx'] = $par_Index;
			$g_Vulnerable[] = $l_Vuln;
			return true;
		}
		
		return false;
	}

	if (
	    (strpos($par_Filename, 'joomla/filesystem/file.php') !== false) &&
		(strpos($par_Content, '$file = rtrim($file, \'.\');') === false)
		) 
	{		
		if ($g_CmsListDetector->isCms(CMS_JOOMLA, '1.5')) {
			$l_Vuln['id'] = 'AFU : https://docs.joomla.org/Security_hotfixes_for_Joomla_EOL_versions';
			$l_Vuln['ndx'] = $par_Index;
			$g_Vulnerable[] = $l_Vuln;
			return true;
		}
		
		return false;
	}

	if ((strpos($par_Filename, 'editor/filemanager/upload/test.html') !== false) ||
		(stripos($par_Filename, 'editor/filemanager/browser/default/connectors/php/') !== false) ||
		(stripos($par_Filename, 'editor/filemanager/connectors/uploadtest.html') !== false) ||
	   (strpos($par_Filename, 'editor/filemanager/browser/default/connectors/test.html') !== false)) {
		$l_Vuln['id'] = 'AFU : FCKEDITOR : http://www.exploit-db.com/exploits/17644/ & /exploit/249';
		$l_Vuln['ndx'] = $par_Index;
		$g_Vulnerable[] = $l_Vuln;
		return true;
	}

	if ((strpos($par_Filename, 'inc_php/image_view.class.php') !== false) ||
	    (strpos($par_Filename, '/inc_php/framework/image_view.class.php') !== false)) {
		if (strpos($par_Content, 'showImageByID') === false) {
			$l_Vuln['id'] = 'AFU : REVSLIDER : http://www.exploit-db.com/exploits/35385/';
			$l_Vuln['ndx'] = $par_Index;
			$g_Vulnerable[] = $l_Vuln;
			return true;
		}
		
		return false;
	}

	if ((strpos($par_Filename, 'elfinder/php/connector.php') !== false) ||
	    (strpos($par_Filename, 'elfinder/elfinder.') !== false)) {
			$l_Vuln['id'] = 'AFU : elFinder';
			$l_Vuln['ndx'] = $par_Index;
			$g_Vulnerable[] = $l_Vuln;
			return true;
	}

	if (strpos($par_Filename, 'includes/database/database.inc') !== false) {
		if (strpos($par_Content, 'foreach ($data as $i => $value)') !== false) {
			$l_Vuln['id'] = 'SQLI : DRUPAL : CVE-2014-3704';
			$l_Vuln['ndx'] = $par_Index;
			$g_Vulnerable[] = $l_Vuln;
			return true;
		}
		
		return false;
	}

	if (strpos($par_Filename, 'engine/classes/min/index.php') !== false) {
		if (strpos($par_Content, 'tr_replace(chr(0)') === false) {
			$l_Vuln['id'] = 'AFD : MINIFY : CVE-2013-6619';
			$l_Vuln['ndx'] = $par_Index;
			$g_Vulnerable[] = $l_Vuln;
			return true;
		}
		
		return false;
	}

	if (( strpos($par_Filename, 'timthumb.php') !== false ) || 
	    ( strpos($par_Filename, 'thumb.php') !== false ) || 
	    ( strpos($par_Filename, 'cache.php') !== false ) || 
	    ( strpos($par_Filename, '_img.php') !== false )) {
		if (strpos($par_Content, 'code.google.com/p/timthumb') !== false && strpos($par_Content, '2.8.14') === false ) {
			$l_Vuln['id'] = 'RCE : TIMTHUMB : CVE-2011-4106,CVE-2014-4663';
			$l_Vuln['ndx'] = $par_Index;
			$g_Vulnerable[] = $l_Vuln;
			return true;
		}
		
		return false;
	}

	if (strpos($par_Filename, 'components/com_rsform/helpers/rsform.php') !== false) {
		if (strpos($par_Content, 'eval($form->ScriptDisplay);') !== false) {
			$l_Vuln['id'] = 'RCE : RSFORM : rsform.php, LINE 1605';
			$l_Vuln['ndx'] = $par_Index;
			$g_Vulnerable[] = $l_Vuln;
			return true;
		}
		
		return false;
	}

	if (strpos($par_Filename, 'fancybox-for-wordpress/fancybox.php') !== false) {
		if (strpos($par_Content, '\'reset\' == $_REQUEST[\'action\']') !== false) {
			$l_Vuln['id'] = 'CODE INJECTION : FANCYBOX';
			$l_Vuln['ndx'] = $par_Index;
			$g_Vulnerable[] = $l_Vuln;
			return true;
		}
		
		return false;
	}


	if (strpos($par_Filename, 'cherry-plugin/admin/import-export/upload.php') !== false) {
		if (strpos($par_Content, 'verify nonce') === false) {
			$l_Vuln['id'] = 'AFU : Cherry Plugin';
			$l_Vuln['ndx'] = $par_Index;
			$g_Vulnerable[] = $l_Vuln;
			return true;
		}
		
		return false;
	}
	
	
	if (strpos($par_Filename, 'tiny_mce/plugins/tinybrowser/tinybrowser.php') !== false) {	
		$l_Vuln['id'] = 'AFU : TINYMCE : http://www.exploit-db.com/exploits/9296/';
		$l_Vuln['ndx'] = $par_Index;
		$g_Vulnerable[] = $l_Vuln;
		
		return true;
	}

	if (strpos($par_Filename, '/bx_1c_import.php') !== false) {	
		if (strpos($par_Content, '$_GET[\'action\']=="getfiles"') !== false) {
   		   $l_Vuln['id'] = 'AFD : https://habrahabr.ru/company/dsec/blog/326166/';
   		   $l_Vuln['ndx'] = $par_Index;
   		   $g_Vulnerable[] = $l_Vuln;
   		
   		   return true;
                }
	}

	if (strpos($par_Filename, 'scripts/setup.php') !== false) {		
		if (strpos($par_Content, 'PMA_Config') !== false) {
			$l_Vuln['id'] = 'CODE INJECTION : PHPMYADMIN : http://1337day.com/exploit/5334';
			$l_Vuln['ndx'] = $par_Index;
			$g_Vulnerable[] = $l_Vuln;
			return true;
		}
		
		return false;
	}

	if (strpos($par_Filename, '/uploadify.php') !== false) {		
		if (strpos($par_Content, 'move_uploaded_file($tempFile,$targetFile') !== false) {
			$l_Vuln['id'] = 'AFU : UPLOADIFY : CVE: 2012-1153';
			$l_Vuln['ndx'] = $par_Index;
			$g_Vulnerable[] = $l_Vuln;
			return true;
		}
		
		return false;
	}

	if (strpos($par_Filename, 'com_adsmanager/controller.php') !== false) {		
		if (strpos($par_Content, 'move_uploaded_file($file[\'tmp_name\'], $tempPath.\'/\'.basename($file[') !== false) {
			$l_Vuln['id'] = 'AFU : https://revisium.com/ru/blog/adsmanager_afu.html';
			$l_Vuln['ndx'] = $par_Index;
			$g_Vulnerable[] = $l_Vuln;
			return true;
		}
		
		return false;
	}

	if (strpos($par_Filename, 'wp-content/plugins/wp-mobile-detector/resize.php') !== false) {		
		if (strpos($par_Content, 'file_put_contents($path, file_get_contents($_REQUEST[\'src\']));') !== false) {
			$l_Vuln['id'] = 'AFU : https://www.pluginvulnerabilities.com/2016/05/31/aribitrary-file-upload-vulnerability-in-wp-mobile-detector/';
			$l_Vuln['ndx'] = $par_Index;
			$g_Vulnerable[] = $l_Vuln;
			return true;
		}
		
		return false;
	}

	if (strpos($par_Filename, 'phpmailer.php') !== false) {		
		if (strpos($par_Content, 'PHPMailer') !== false) {
                        $l_Found = preg_match('~Version:\s*(\d+)\.(\d+)\.(\d+)~', $par_Content, $l_Match);

                        if ($l_Found) {
                           $l_Version = $l_Match[1] * 1000 + $l_Match[2] * 100 + $l_Match[3];

                           if ($l_Version < 2520) {
                              $l_Found = false;
                           }
                        }

                        if (!$l_Found) {

                           $l_Found = preg_match('~Version\s*=\s*\'(\d+)\.*(\d+)\.(\d+)~', $par_Content, $l_Match);
                           if ($l_Found) {
                              $l_Version = $l_Match[1] * 1000 + $l_Match[2] * 100 + $l_Match[3];
                              if ($l_Version < 5220) {
                                 $l_Found = false;
                              }
                           }
			}


		        if (!$l_Found) {
	   		   $l_Vuln['id'] = 'RCE : CVE-2016-10045, CVE-2016-10031';
			   $l_Vuln['ndx'] = $par_Index;
			   $g_Vulnerable[] = $l_Vuln;
			   return true;
                        }
		}
		
		return false;
	}




}

///////////////////////////////////////////////////////////////////////////
function QCR_GoScan($par_Offset)
{
	global $g_IframerFragment, $g_Iframer, $g_Redirect, $g_Doorway, $g_EmptyLink, $g_Structure, $g_Counter, 
		   $g_HeuristicType, $g_HeuristicDetected, $g_TotalFolder, $g_TotalFiles, $g_WarningPHP, $g_AdwareList,
		   $g_CriticalPHP, $g_Phishing, $g_CriticalJS, $g_UrlIgnoreList, $g_CriticalJSFragment, $g_PHPCodeInside, $g_PHPCodeInsideFragment, 
		   $g_NotRead, $g_WarningPHPFragment, $g_WarningPHPSig, $g_BigFiles, $g_RedirectPHPFragment, $g_EmptyLinkSrc, $g_CriticalPHPSig, $g_CriticalPHPFragment, 
           $g_Base64Fragment, $g_UnixExec, $g_PhishingSigFragment, $g_PhishingFragment, $g_PhishingSig, $g_CriticalJSSig, $g_IframerFragment, $g_CMS, $defaults, $g_AdwareListFragment, $g_KnownList,$g_Vulnerable;

    QCR_Debug('QCR_GoScan ' . $par_Offset);

	$i = 0;
	
	try {
		$s_file = new SplFileObject(QUEUE_FILENAME);
		$s_file->setFlags(SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);

		foreach ($s_file as $l_Filename) {
			QCR_ScanFile($l_Filename, $i++);
		}
		
		unset($s_file);	
	}
	catch (Exception $e) { QCR_Debug( $e->getMessage() ); }
}

///////////////////////////////////////////////////////////////////////////
function QCR_ScanFile($l_Filename, $i = 0)
{
	global $g_IframerFragment, $g_Iframer, $g_Redirect, $g_Doorway, $g_EmptyLink, $g_Structure, $g_Counter, 
		   $g_HeuristicType, $g_HeuristicDetected, $g_TotalFolder, $g_TotalFiles, $g_WarningPHP, $g_AdwareList,
		   $g_CriticalPHP, $g_Phishing, $g_CriticalJS, $g_UrlIgnoreList, $g_CriticalJSFragment, $g_PHPCodeInside, $g_PHPCodeInsideFragment, 
		   $g_NotRead, $g_WarningPHPFragment, $g_WarningPHPSig, $g_BigFiles, $g_RedirectPHPFragment, $g_EmptyLinkSrc, $g_CriticalPHPSig, $g_CriticalPHPFragment, 
           $g_Base64Fragment, $g_UnixExec, $g_PhishingSigFragment, $g_PhishingFragment, $g_PhishingSig, $g_CriticalJSSig, $g_IframerFragment, $g_CMS, $defaults, $g_AdwareListFragment, 
           $g_KnownList,$g_Vulnerable, $g_CriticalFiles, $g_DeMapper;

	global $g_CRC;
	static $_files_and_ignored = 0;

			$l_CriticalDetected = false;
			$l_Stat = stat($l_Filename);

			if (substr($l_Filename, -1) == DIR_SEPARATOR) {
				// FOLDER
				$g_Structure['n'][$i] = $l_Filename;
				$g_TotalFolder++;
				printProgress($_files_and_ignored, $l_Filename);
				return;
			}

			QCR_Debug('Scan file ' . $l_Filename);
			printProgress(++$_files_and_ignored, $l_Filename);

     			// ignore itself
     			if ($l_Filename == __FILE__) {
     				return;
     			}

			// FILE
			if ((MAX_SIZE_TO_SCAN > 0 AND $l_Stat['size'] > MAX_SIZE_TO_SCAN) || ($l_Stat['size'] < 0))
			{
				$g_BigFiles[] = $i;

                                if (function_exists('aibolit_onBigFile')) { aibolit_onBigFile($l_Filename); }

				AddResult($l_Filename, $i);

		                $l_Ext = strtolower(pathinfo($l_Filename, PATHINFO_EXTENSION));
                                if ((!AI_HOSTER) && in_array($l_Ext, $g_CriticalFiles)) {
				    $g_CriticalPHP[] = $i;
				    $g_CriticalPHPFragment[] = "BIG FILE. SKIPPED.";
				    $g_CriticalPHPSig[] = "big_1";
                                }
			}
			else
			{
				$g_TotalFiles++;

			$l_TSStartScan = microtime(true);

		$l_Ext = strtolower(pathinfo($l_Filename, PATHINFO_EXTENSION));
		if (filetype($l_Filename) == 'file') {
                   $l_Content = @file_get_contents($l_Filename);
		   if (SHORT_PHP_TAG) {
//                      $l_Content = preg_replace('|<\?\s|smiS', '<?php ', $l_Content); 
                   }

                   $l_Unwrapped = @php_strip_whitespace($l_Filename);
                }

		
                if ((($l_Content == '') || ($l_Unwrapped == '')) && ($l_Stat['size'] > 0)) {
                   $g_NotRead[] = $i;
                   if (function_exists('aibolit_onReadError')) { aibolit_onReadError($l_Filename, 'io'); }
                   AddResult('[io] ' . $l_Filename, $i);
                   return;
                }

				// unix executables
				if (strpos($l_Content, chr(127) . 'ELF') !== false) 
				{
			        	if (!in_array($l_Filename, $g_UnixExec)) {
                    				$g_UnixExec[] = $l_Filename;
					}

				        return;
                		}

				$g_CRC = _hash_($l_Unwrapped);

				$l_UnicodeContent = detect_utf_encoding($l_Content);
				//$l_Unwrapped = $l_Content;

				// check vulnerability in files
				$l_CriticalDetected = CheckVulnerability($l_Filename, $i, $l_Content);				

				if ($l_UnicodeContent !== false) {
       				   if (function_exists('iconv')) {
				      $l_Unwrapped = iconv($l_UnicodeContent, "CP1251//IGNORE", $l_Unwrapped);
//       			   if (function_exists('mb_convert_encoding')) {
//                                    $l_Unwrapped = mb_convert_encoding($l_Unwrapped, $l_UnicodeContent, "CP1251");
                                   } else {
                                      $g_NotRead[] = $i;
                                      if (function_exists('aibolit_onReadError')) { aibolit_onReadError($l_Filename, 'ec'); }
                                      AddResult('[ec] ' . $l_Filename, $i);
				   }
                                }

				// critical
				$g_SkipNextCheck = false;

                                $l_DeobfType = '';
				if (!AI_HOSTER) {
                                   $l_DeobfType = getObfuscateType($l_Unwrapped);
                                }

                                if ($l_DeobfType != '') {
                                   $l_Unwrapped = deobfuscate($l_Unwrapped);
				   $g_SkipNextCheck = checkFalsePositives($l_Filename, $l_Unwrapped, $l_DeobfType);
                                } else {
     				   if (DEBUG_MODE) {
				      stdOut("\n...... NOT OBFUSCATED\n");
				   }
				}

				$l_Unwrapped = UnwrapObfu($l_Unwrapped);
				
				if ((!$g_SkipNextCheck) && CriticalPHP($l_Filename, $i, $l_Unwrapped, $l_Pos, $l_SigId))
				{
				        if ($l_Ext == 'js') {
 					   $g_CriticalJS[] = $i;
 					   $g_CriticalJSFragment[] = getFragment($l_Unwrapped, $l_Pos);
 					   $g_CriticalJSSig[] = $l_SigId;
                                        } else {
       					   $g_CriticalPHP[] = $i;
       					   $g_CriticalPHPFragment[] = getFragment($l_Unwrapped, $l_Pos);
      					   $g_CriticalPHPSig[] = $l_SigId;
                                        }

					$g_SkipNextCheck = true;
				} else {
         				if ((!$g_SkipNextCheck) && CriticalPHP($l_Filename, $i, $l_Content, $l_Pos, $l_SigId))
         				{
					        if ($l_Ext == 'js') {
         					   $g_CriticalJS[] = $i;
         					   $g_CriticalJSFragment[] = getFragment($l_Content, $l_Pos);
         					   $g_CriticalJSSig[] = $l_SigId;
                                                } else {
               					   $g_CriticalPHP[] = $i;
               					   $g_CriticalPHPFragment[] = getFragment($l_Content, $l_Pos);
      						   $g_CriticalPHPSig[] = $l_SigId;
                                                }

         					$g_SkipNextCheck = true;
         				}
				}

				$l_TypeDe = 0;
			    if ((!$g_SkipNextCheck) && HeuristicChecker($l_Content, $l_TypeDe, $l_Filename)) {
					$g_HeuristicDetected[] = $i;
					$g_HeuristicType[] = $l_TypeDe;
					$l_CriticalDetected = true;
				}

				// critical JS
				if (!$g_SkipNextCheck) {
					$l_Pos = CriticalJS($l_Filename, $i, $l_Unwrapped, $l_SigId);
					if ($l_Pos !== false)
					{
					        if ($l_Ext == 'js') {
         					   $g_CriticalJS[] = $i;
         					   $g_CriticalJSFragment[] = getFragment($l_Unwrapped, $l_Pos);
         					   $g_CriticalJSSig[] = $l_SigId;
                                                } else {
               					   $g_CriticalPHP[] = $i;
               					   $g_CriticalPHPFragment[] = getFragment($l_Unwrapped, $l_Pos);
      						   $g_CriticalPHPSig[] = $l_SigId;
                                                }

						$g_SkipNextCheck = true;
					}
			    }

				// phishing
				if (!$g_SkipNextCheck) {
					$l_Pos = Phishing($l_Filename, $i, $l_Unwrapped, $l_SigId);
					if ($l_Pos === false) {
                                            $l_Pos = Phishing($l_Filename, $i, $l_Content, $l_SigId);
                                        }

					if ($l_Pos !== false)
					{
						$g_Phishing[] = $i;
						$g_PhishingFragment[] = getFragment($l_Unwrapped, $l_Pos);
						$g_PhishingSigFragment[] = $l_SigId;
						$g_SkipNextCheck = true;
					}
				}

			
			if (!$g_SkipNextCheck) {
				if (SCAN_ALL_FILES || stripos($l_Filename, 'index.'))
				{
					// check iframes
					if (preg_match_all('|<iframe[^>]+src.+?>|smi', $l_Unwrapped, $l_Found, PREG_SET_ORDER)) 
					{
						for ($kk = 0; $kk < count($l_Found); $kk++) {
						    $l_Pos = stripos($l_Found[$kk][0], 'http://');
						    $l_Pos = $l_Pos || stripos($l_Found[$kk][0], 'https://');
						    $l_Pos = $l_Pos || stripos($l_Found[$kk][0], 'ftp://');
							if  (($l_Pos !== false ) && (!knowUrl($l_Found[$kk][0]))) {
         						$g_Iframer[] = $i;
         						$g_IframerFragment[] = getFragment($l_Found[$kk][0], $l_Pos);
         						$l_CriticalDetected = true;
							}
						}
					}

					// check empty links
					if ((($defaults['report_mask'] & REPORT_MASK_SPAMLINKS) == REPORT_MASK_SPAMLINKS) &&
					   (preg_match_all('|<a[^>]+href([^>]+?)>(.*?)</a>|smi', $l_Unwrapped, $l_Found, PREG_SET_ORDER)))
					{
						for ($kk = 0; $kk < count($l_Found); $kk++) {
							if  ((stripos($l_Found[$kk][1], 'http://') !== false) &&
                                                            (trim(strip_tags($l_Found[$kk][2])) == '')) {

								$l_NeedToAdd = true;

							    if  ((stripos($l_Found[$kk][1], $defaults['site_url']) !== false)
                                                                 || knowUrl($l_Found[$kk][1])) {
										$l_NeedToAdd = false;
								}
								
								if ($l_NeedToAdd && (count($g_EmptyLink) < MAX_EXT_LINKS)) {
									$g_EmptyLink[] = $i;
									$g_EmptyLinkSrc[$i][] = substr($l_Found[$kk][0], 0, MAX_PREVIEW_LEN);
									$l_CriticalDetected = true;
								}
							}
						}
					}
				}

				// check for PHP code inside any type of file
				if (stripos($l_Ext, 'ph') === false)
				{
					$l_Pos = QCR_SearchPHP($l_Content);
					if ($l_Pos !== false)
					{
						$g_PHPCodeInside[] = $i;
						$g_PHPCodeInsideFragment[] = getFragment($l_Unwrapped, $l_Pos);
						$l_CriticalDetected = true;
					}
				}

				// htaccess
				if (stripos($l_Filename, '.htaccess'))
				{
				
					if (stripos($l_Content, 'index.php?name=$1') !== false ||
						stripos($l_Content, 'index.php?m=1') !== false
					)
					{
						$g_SuspDir[] = $i;
					}

					$l_HTAContent = preg_replace('|^\s*#.+$|m', '', $l_Content);

					$l_Pos = stripos($l_Content, 'auto_prepend_file');
					if ($l_Pos !== false) {
						$g_Redirect[] = $i;
						$g_RedirectPHPFragment[] = getFragment($l_Content, $l_Pos);
						$l_CriticalDetected = true;
					}
					
					$l_Pos = stripos($l_Content, 'auto_append_file');
					if ($l_Pos !== false) {
						$g_Redirect[] = $i;
						$g_RedirectPHPFragment[] = getFragment($l_Content, $l_Pos);
						$l_CriticalDetected = true;
					}

					$l_Pos = stripos($l_Content, '^(%2d|-)[^=]+$');
					if ($l_Pos !== false)
					{
						$g_Redirect[] = $i;
                        			$g_RedirectPHPFragment[] = getFragment($l_Content, $l_Pos);
						$l_CriticalDetected = true;
					}

					if (!$l_CriticalDetected) {
						$l_Pos = stripos($l_Content, '%{HTTP_USER_AGENT}');
						if ($l_Pos !== false)
						{
							$g_Redirect[] = $i;
							$g_RedirectPHPFragment[] = getFragment($l_Content, $l_Pos);
							$l_CriticalDetected = true;
						}
					}

					if (!$l_CriticalDetected) {
						if (
							preg_match_all("|RewriteRule\s+.+?\s+http://(.+?)/.+\s+\[.*R=\d+.*\]|smi", $l_HTAContent, $l_Found, PREG_SET_ORDER)
						)
						{
							$l_Host = str_replace('www.', '', $_SERVER['HTTP_HOST']);
							for ($j = 0; $j < sizeof($l_Found); $j++)
							{
								$l_Found[$j][1] = str_replace('www.', '', $l_Found[$j][1]);
								if ($l_Found[$j][1] != $l_Host)
								{
									$g_Redirect[] = $i;
									$l_CriticalDetected = true;
									break;
								}
							}
						}
					}

					unset($l_HTAContent);
			    }
			

			    // warnings
				$l_Pos = '';
				
			    if (WarningPHP($l_Filename, $l_Unwrapped, $l_Pos, $l_SigId))
				{       
					$l_Prio = 1;
					if (strpos($l_Filename, '.ph') !== false) {
					   $l_Prio = 0;
					}
					
					$g_WarningPHP[$l_Prio][] = $i;
					$g_WarningPHPFragment[$l_Prio][] = getFragment($l_Unwrapped, $l_Pos);
					$g_WarningPHPSig[] = $l_SigId;

					$l_CriticalDetected = true;
				}
				

				// adware
				if (Adware($l_Filename, $l_Unwrapped, $l_Pos))
				{
					$g_AdwareList[] = $i;
					$g_AdwareListFragment[] = getFragment($l_Unwrapped, $l_Pos);
					$l_CriticalDetected = true;
				}

				// articles
				if (stripos($l_Filename, 'article_index'))
				{
					$g_AdwareList[] = $i;
					$l_CriticalDetected = true;
				}
			}
		} // end of if (!$g_SkipNextCheck) {
			
			unset($l_Unwrapped);
			unset($l_Content);
			
			//printProgress(++$_files_and_ignored, $l_Filename);

			$l_TSEndScan = microtime(true);
                        if ($l_TSEndScan - $l_TSStartScan >= 0.5) {
			   			   usleep(SCAN_DELAY * 1000);
                        }

			if ($g_SkipNextCheck || $l_CriticalDetected) {
				AddResult($l_Filename, $i);
			}
}

function AddResult($l_Filename, $i)
{
	global $g_Structure, $g_CRC;
	
	$l_Stat = stat($l_Filename);
	$g_Structure['n'][$i] = $l_Filename;
	$g_Structure['s'][$i] = $l_Stat['size'];
	$g_Structure['c'][$i] = $l_Stat['ctime'];
	$g_Structure['m'][$i] = $l_Stat['mtime'];
	$g_Structure['crc'][$i] = $g_CRC;
}

///////////////////////////////////////////////////////////////////////////
function WarningPHP($l_FN, $l_Content, &$l_Pos, &$l_SigId)
{
	   global $g_SusDB,$g_ExceptFlex, $gXX_FlexDBShe, $gX_FlexDBShe, $g_FlexDBShe, $gX_DBShe, $g_DBShe, $g_Base64, $g_Base64Fragment;

  $l_Res = false;

  if (AI_EXTRA_WARN) {
  	foreach ($g_SusDB as $l_Item) {
    	if (preg_match('#' . $l_Item . '#smiS', $l_Content, $l_Found, PREG_OFFSET_CAPTURE)) {
       	 	if (!CheckException($l_Content, $l_Found)) {
           	 	$l_Pos = $l_Found[0][1];
           	 	//$l_SigId = myCheckSum($l_Item);
           	 	$l_SigId = getSigId($l_Found);
           	 	return true;
       	 	}
    	}
  	}
  }

  if (AI_EXPERT < 2) {
    	foreach ($gXX_FlexDBShe as $l_Item) {
      		if (preg_match('#' . $l_Item . '#smiS', $l_Content, $l_Found, PREG_OFFSET_CAPTURE)) {
             	$l_Pos = $l_Found[0][1];
           	    //$l_SigId = myCheckSum($l_Item);
           	    $l_SigId = getSigId($l_Found);
        	    return true;
	  		}
    	}

	}

    if (AI_EXPERT < 1) {
    	foreach ($gX_FlexDBShe as $l_Item) {
      		if (preg_match('#' . $l_Item . '#smiS', $l_Content, $l_Found, PREG_OFFSET_CAPTURE)) {
             	$l_Pos = $l_Found[0][1];
           	 	//$l_SigId = myCheckSum($l_Item);
           	 	$l_SigId = getSigId($l_Found);
        	    return true;
	  		}
    	}

	    $l_Content_lo = strtolower($l_Content);

	    foreach ($gX_DBShe as $l_Item) {
	      $l_Pos = strpos($l_Content_lo, $l_Item);
	      if ($l_Pos !== false) {
	         $l_SigId = myCheckSum($l_Item);
	         return true;
	      }
		}
	}

}

///////////////////////////////////////////////////////////////////////////
function Adware($l_FN, $l_Content, &$l_Pos)
{
  global $g_AdwareSig;

  $l_Res = false;

foreach ($g_AdwareSig as $l_Item) {
    $offset = 0;
    while (preg_match('#' . $l_Item . '#smi', $l_Content, $l_Found, PREG_OFFSET_CAPTURE, $offset)) {
       if (!CheckException($l_Content, $l_Found)) {
           $l_Pos = $l_Found[0][1];
           return true;
       }

       $offset = $l_Found[0][1] + 1;
    }
  }

  return $l_Res;
}

///////////////////////////////////////////////////////////////////////////
function CheckException(&$l_Content, &$l_Found) {
  global $g_ExceptFlex, $gX_FlexDBShe, $gXX_FlexDBShe, $g_FlexDBShe, $gX_DBShe, $g_DBShe, $g_Base64, $g_Base64Fragment;
   $l_FoundStrPlus = substr($l_Content, max($l_Found[0][1] - 10, 0), 70);

   foreach ($g_ExceptFlex as $l_ExceptItem) {
      if (@preg_match('#' . $l_ExceptItem . '#smi', $l_FoundStrPlus, $l_Detected)) {
//         print("\n\nEXCEPTION FOUND\n[" . $l_ExceptItem .  "]\n" . $l_Content . "\n\n----------\n\n");
         return true;
      }
   }

   return false;
}

///////////////////////////////////////////////////////////////////////////
function Phishing($l_FN, $l_Index, $l_Content, &$l_SigId)
{
  global $g_PhishingSig, $g_PhishFiles, $g_PhishEntries;

  $l_Res = false;

  // need check file (by extension) ?
  $l_SkipCheck = SMART_SCAN;

if ($l_SkipCheck) {
  	foreach($g_PhishFiles as $l_Ext) {
  		  if (strpos($l_FN, $l_Ext) !== false) {
		  			$l_SkipCheck = false;
		  		  	break;
  	  	  }
  	  }
  }

  // need check file (by signatures) ?
  if ($l_SkipCheck && preg_match('~' . $g_PhishEntries . '~smiS', $l_Content, $l_Found)) {
	  $l_SkipCheck = false;
  }

  if ($l_SkipCheck && SMART_SCAN) {
      if (DEBUG_MODE) {
         echo "Skipped phs file, not critical.\n";
      }

	  return false;
  }


  foreach ($g_PhishingSig as $l_Item) {
    $offset = 0;
    while (preg_match('#' . $l_Item . '#smi', $l_Content, $l_Found, PREG_OFFSET_CAPTURE, $offset)) {
       if (!CheckException($l_Content, $l_Found)) {
           $l_Pos = $l_Found[0][1];
//           $l_SigId = myCheckSum($l_Item);
           $l_SigId = getSigId($l_Found);

           if (DEBUG_MODE) {
              echo "Phis: $l_FN matched [$l_Item] in $l_Pos\n";
           }

           return $l_Pos;
       }
       $offset = $l_Found[0][1] + 1;

    }
  }

  return $l_Res;
}

///////////////////////////////////////////////////////////////////////////
function CriticalJS($l_FN, $l_Index, $l_Content, &$l_SigId)
{
  global $g_JSVirSig, $gX_JSVirSig, $g_VirusFiles, $g_VirusEntries, $g_RegExpStat;

  $l_Res = false;
  
    // need check file (by extension) ?
    $l_SkipCheck = SMART_SCAN;
	
	if ($l_SkipCheck) {
       	   foreach($g_VirusFiles as $l_Ext) {
    		  if (strpos($l_FN, $l_Ext) !== false) {
  		  			$l_SkipCheck = false;
  		  		  	break;
    	  	  }
    	  }
	  }
  
    // need check file (by signatures) ?
    if ($l_SkipCheck && preg_match('~' . $g_VirusEntries . '~smiS', $l_Content, $l_Found)) {
  	  $l_SkipCheck = false;
    }
  
    if ($l_SkipCheck && SMART_SCAN) {
        if (DEBUG_MODE) {
           echo "Skipped js file, not critical.\n";
        }

  	  return false;
    }
  

  foreach ($g_JSVirSig as $l_Item) {
    $offset = 0;
    if (DEBUG_PERFORMANCE) { 
       $stat_start = microtime(true);
    }

    while (preg_match('#' . $l_Item . '#smi', $l_Content, $l_Found, PREG_OFFSET_CAPTURE, $offset)) {

       if (!CheckException($l_Content, $l_Found)) {
           $l_Pos = $l_Found[0][1];
//           $l_SigId = myCheckSum($l_Item);
           $l_SigId = getSigId($l_Found);

           if (DEBUG_MODE) {
              echo "JS: $l_FN matched [$l_Item] in $l_Pos\n";
           }

           return $l_Pos;
       }

       $offset = $l_Found[0][1] + 1;

    }

    if (DEBUG_PERFORMANCE) { 
       $stat_stop = microtime(true);
       $g_RegExpStat[$l_Item] += $stat_stop - $stat_start;
    }
//   if (pcre_error($l_FN, $l_Index)) {  }

  }

if (AI_EXPERT > 1) {
  foreach ($gX_JSVirSig as $l_Item) {
    if (DEBUG_PERFORMANCE) { 
       $stat_start = microtime(true);
    }

    if (preg_match('#' . $l_Item . '#smi', $l_Content, $l_Found, PREG_OFFSET_CAPTURE)) {
       if (!CheckException($l_Content, $l_Found)) {
           $l_Pos = $l_Found[0][1];
           //$l_SigId = myCheckSum($l_Item);
           $l_SigId = getSigId($l_Found);

           if (DEBUG_MODE) {
              echo "JS PARA: $l_FN matched [$l_Item] in $l_Pos\n";
           }

           return $l_Pos;
       }
    }

    if (DEBUG_PERFORMANCE) { 
       $stat_stop = microtime(true);
       $g_RegExpStat[$l_Item] += $stat_stop - $stat_start;
    }

//   if (pcre_error($l_FN, $l_Index)) {  }

  }
}

  return $l_Res;
}

////////////////////////////////////////////////////////////////////////////
function pcre_error($par_FN, $par_Index) {
   global $g_NotRead, $g_Structure;

   $err = preg_last_error();
   if (($err == PREG_BACKTRACK_LIMIT_ERROR) || ($err == PREG_RECURSION_LIMIT_ERROR)) {
      if (!in_array($par_Index, $g_NotRead)) {
         if (function_exists('aibolit_onReadError')) { aibolit_onReadError($l_Filename, 're'); }
         $g_NotRead[] = $par_Index;
         AddResult('[re] ' . $par_FN, $par_Index);
      }
 
      return true;
   }

   return false;
}



////////////////////////////////////////////////////////////////////////////
define('SUSP_MTIME', 1); // suspicious mtime (greater than ctime)
define('SUSP_PERM', 2); // suspicious permissions 
define('SUSP_PHP_IN_UPLOAD', 3); // suspicious .php file in upload or image folder 

  function get_descr_heur($type) {
     switch ($type) {
	     case SUSP_MTIME: return AI_STR_077; 
	     case SUSP_PERM: return AI_STR_078;  
	     case SUSP_PHP_IN_UPLOAD: return AI_STR_079; 
	 }
	 
	 return "---";
  }

  ///////////////////////////////////////////////////////////////////////////
  function HeuristicChecker($l_Content, &$l_Type, $l_Filename) {
     $res = false;
	 
	 $l_Stat = stat($l_Filename);
	 // most likely changed by touch
	 if ($l_Stat['ctime'] < $l_Stat['mtime']) {
	     $l_Type = SUSP_MTIME;
		 return true;
	 }

	 	 
	 $l_Perm = fileperms($l_Filename) & 0777;
	 if (($l_Perm & 0400 != 0400) || // not readable by owner
		($l_Perm == 0000) ||
		($l_Perm == 0404) ||
		($l_Perm == 0505))
	 {
		 $l_Type = SUSP_PERM;
		 return true;
	 }

	 
     if ((strpos($l_Filename, '.ph')) && (
	     strpos($l_Filename, '/images/stories/') ||
	     //strpos($l_Filename, '/img/') ||
		 //strpos($l_Filename, '/images/') ||
	     //strpos($l_Filename, '/uploads/') ||
		 strpos($l_Filename, '/wp-content/upload/') 
	    )	    
	 ) {
		$l_Type = SUSP_PHP_IN_UPLOAD;
	 	return true;
	 }

     return false;
  }

///////////////////////////////////////////////////////////////////////////
function CriticalPHP($l_FN, $l_Index, $l_Content, &$l_Pos, &$l_SigId)
{
  global $g_ExceptFlex, $gXX_FlexDBShe, $gX_FlexDBShe, $g_FlexDBShe, $gX_DBShe, $g_DBShe, $g_Base64, $g_Base64Fragment,
  $g_CriticalFiles, $g_CriticalEntries, $g_RegExpStat;

  // need check file (by extension) ?
  $l_SkipCheck = SMART_SCAN;

  if ($l_SkipCheck) {
	  foreach($g_CriticalFiles as $l_Ext) {
  	  	if ((strpos($l_FN, $l_Ext) !== false) && (strpos($l_FN, '.js') === false)) {
		   $l_SkipCheck = false;
		   break;
  	  	}
  	  }
  }
  
  // need check file (by signatures) ?
  if ($l_SkipCheck && preg_match('~' . $g_CriticalEntries . '~smiS', $l_Content, $l_Found)) {
     $l_SkipCheck = false;
  }
  
  
  // if not critical - skip it 
  if ($l_SkipCheck && SMART_SCAN) {
      if (DEBUG_MODE) {
         echo "Skipped file, not critical.\n";
      }

	  return false;
  }

  foreach ($g_FlexDBShe as $l_Item) {
    $offset = 0;

    if (DEBUG_PERFORMANCE) { 
       $stat_start = microtime(true);
    }

    while (preg_match('#' . $l_Item . '#smiS', $l_Content, $l_Found, PREG_OFFSET_CAPTURE, $offset)) {
       if (!CheckException($l_Content, $l_Found)) {
           $l_Pos = $l_Found[0][1];
           //$l_SigId = myCheckSum($l_Item);
           $l_SigId = getSigId($l_Found);

           if (DEBUG_MODE) {
              echo "CRIT 1: $l_FN matched [$l_Item] in $l_Pos\n";
           }

           return true;
       }

       $offset = $l_Found[0][1] + 1;

    }

    if (DEBUG_PERFORMANCE) { 
       $stat_stop = microtime(true);
       $g_RegExpStat[$l_Item] += $stat_stop - $stat_start;
    }

//   if (pcre_error($l_FN, $l_Index)) {  }

  }

if (AI_EXPERT > 0) {
  foreach ($gX_FlexDBShe as $l_Item) {
    if (DEBUG_PERFORMANCE) { 
       $stat_start = microtime(true);
    }

    if (preg_match('#' . $l_Item . '#smiS', $l_Content, $l_Found, PREG_OFFSET_CAPTURE)) {
       if (!CheckException($l_Content, $l_Found)) {
           $l_Pos = $l_Found[0][1];
           //$l_SigId = myCheckSum($l_Item);
           $l_SigId = getSigId($l_Found);

           if (DEBUG_MODE) {
              echo "CRIT 3: $l_FN matched [$l_Item] in $l_Pos\n";
           }

           return true;
       }
    }

    if (DEBUG_PERFORMANCE) { 
       $stat_stop = microtime(true);
       $g_RegExpStat[$l_Item] += $stat_stop - $stat_start;
    }

//   if (pcre_error($l_FN, $l_Index)) {  }
  }
}

if (AI_EXPERT > 1) {
  foreach ($gXX_FlexDBShe as $l_Item) {
    if (DEBUG_PERFORMANCE) { 
       $stat_start = microtime(true);
    }

    if (preg_match('#' . $l_Item . '#smiS', $l_Content, $l_Found, PREG_OFFSET_CAPTURE)) {
       if (!CheckException($l_Content, $l_Found)) {
           $l_Pos = $l_Found[0][1];
           //$l_SigId = myCheckSum($l_Item);
           $l_SigId = getSigId($l_Found);

           if (DEBUG_MODE) {
              echo "CRIT 2: $l_FN matched [$l_Item] in $l_Pos\n";
           }

           return true;
       }
    }

    if (DEBUG_PERFORMANCE) { 
       $stat_stop = microtime(true);
       $g_RegExpStat[$l_Item] += $stat_stop - $stat_start;
    }

//   if (pcre_error($l_FN, $l_Index)) {  }
  }
}

  $l_Content_lo = strtolower($l_Content);

  foreach ($g_DBShe as $l_Item) {
    $l_Pos = strpos($l_Content_lo, $l_Item);
    if ($l_Pos !== false) {
       $l_SigId = myCheckSum($l_Item);

       if (DEBUG_MODE) {
          echo "CRIT 4: $l_FN matched [$l_Item] in $l_Pos\n";
       }

       return true;
    }
  }

if (AI_EXPERT > 0) {
  foreach ($gX_DBShe as $l_Item) {
    $l_Pos = strpos($l_Content_lo, $l_Item);
    if ($l_Pos !== false) {
       $l_SigId = myCheckSum($l_Item);

       if (DEBUG_MODE) {
          echo "CRIT 5: $l_FN matched [$l_Item] in $l_Pos\n";
       }

       return true;
    }
  }
}

if (AI_HOSTER) return false;

if (AI_EXPERT > 0) {
  if ((strpos($l_Content, 'GIF89') === 0) && (strpos($l_FN, '.php') !== false )) {
     $l_Pos = 0;

     if (DEBUG_MODE) {
          echo "CRIT 6: $l_FN matched [$l_Item] in $l_Pos\n";
     }

     return true;
  }
}

  // detect uploaders / droppers
if (AI_EXPERT > 1) {
  $l_Found = null;
  if (
     (filesize($l_FN) < 1024) &&
     (strpos($l_FN, '.ph') !== false) &&
     (
       (($l_Pos = strpos($l_Content, 'multipart/form-data')) > 0) || 
       (($l_Pos = strpos($l_Content, '$_FILE[') > 0)) ||
       (($l_Pos = strpos($l_Content, 'move_uploaded_file')) > 0) ||
       (preg_match('|\bcopy\s*\(|smi', $l_Content, $l_Found, PREG_OFFSET_CAPTURE))
     )
     ) {
       if ($l_Found != null) {
          $l_Pos = $l_Found[0][1];
       } 
     if (DEBUG_MODE) {
          echo "CRIT 7: $l_FN matched [$l_Item] in $l_Pos\n";
     }

     return true;
  }
}

  return false;
}

///////////////////////////////////////////////////////////////////////////
if (!isCli()) {
   header('Content-type: text/html; charset=utf-8');
}

if (!isCli()) {

  $l_PassOK = false;
  if (strlen(PASS) > 8) {
     $l_PassOK = true;   
  } 

  if ($l_PassOK && preg_match('|[0-9]|', PASS, $l_Found) && preg_match('|[A-Z]|', PASS, $l_Found) && preg_match('|[a-z]|', PASS, $l_Found) ) {
     $l_PassOK = true;   
  }
  
  if (!$l_PassOK) {  
    echo sprintf(AI_STR_009, generatePassword());
    exit;
  }

  if (isset($_GET['fn']) && ($_GET['ph'] == crc32(PASS))) {
     printFile();
     exit;
  }

  if ($_GET['p'] != PASS) {
    $generated_pass = generatePassword(); 
    echo sprintf(AI_STR_010, $generated_pass, $generated_pass);
    exit;
  }
}

if (!is_readable(ROOT_PATH)) {
  echo AI_STR_011;
  exit;
}

if (isCli()) {
	if (defined('REPORT_PATH') AND REPORT_PATH)
	{
		if (!is_writable(REPORT_PATH))
		{
			die2("\nCannot write report. Report dir " . REPORT_PATH . " is not writable.");
		}

		else if (!REPORT_FILE)
		{
			die2("\nCannot write report. Report filename is empty.");
		}

		else if (($file = REPORT_PATH . DIR_SEPARATOR . REPORT_FILE) AND is_file($file) AND !is_writable($file))
		{
			die2("\nCannot write report. Report file '$file' exists but is not writable.");
		}
	}
}


// detect version CMS
$g_KnownCMS = array();
$tmp_cms = array();
$g_CmsListDetector = new CmsVersionDetector(ROOT_PATH);
$l_CmsDetectedNum = $g_CmsListDetector->getCmsNumber();
for ($tt = 0; $tt < $l_CmsDetectedNum; $tt++) {
    $g_CMS[] = $g_CmsListDetector->getCmsName($tt) . ' v' . makeSafeFn($g_CmsListDetector->getCmsVersion($tt));
    $tmp_cms[strtolower($g_CmsListDetector->getCmsName($tt))] = 1;
}

if (count($tmp_cms) > 0) {
   $g_KnownCMS = array_keys($tmp_cms);
   $len = count($g_KnownCMS);
   for ($i = 0; $i < $len; $i++) {
      if ($g_KnownCMS[$i] == strtolower(CMS_WORDPRESS)) $g_KnownCMS[] = 'wp';
      if ($g_KnownCMS[$i] == strtolower(CMS_WEBASYST)) $g_KnownCMS[] = 'shopscript';
      if ($g_KnownCMS[$i] == strtolower(CMS_IPB)) $g_KnownCMS[] = 'ipb';
      if ($g_KnownCMS[$i] == strtolower(CMS_DLE)) $g_KnownCMS[] = 'dle';
      if ($g_KnownCMS[$i] == strtolower(CMS_INSTANTCMS)) $g_KnownCMS[] = 'instantcms';
      if ($g_KnownCMS[$i] == strtolower(CMS_SHOPSCRIPT)) $g_KnownCMS[] = 'shopscript';
   }
}


$g_DirIgnoreList = array();
$g_IgnoreList = array();
$g_UrlIgnoreList = array();
$g_KnownList = array();

$l_IgnoreFilename = $g_AiBolitAbsolutePath . '/.aignore';
$l_DirIgnoreFilename = $g_AiBolitAbsolutePath . '/.adirignore';
$l_UrlIgnoreFilename = $g_AiBolitAbsolutePath . '/.aurlignore';

if (file_exists($l_IgnoreFilename)) {
    $l_IgnoreListRaw = file($l_IgnoreFilename);
    for ($i = 0; $i < count($l_IgnoreListRaw); $i++) 
    {
    	$g_IgnoreList[] = explode("\t", trim($l_IgnoreListRaw[$i]));
    }
    unset($l_IgnoreListRaw);
}

if (file_exists($l_DirIgnoreFilename)) {
    $g_DirIgnoreList = file($l_DirIgnoreFilename);
	
	for ($i = 0; $i < count($g_DirIgnoreList); $i++) {
		$g_DirIgnoreList[$i] = trim($g_DirIgnoreList[$i]);
	}
}

if (file_exists($l_UrlIgnoreFilename)) {
    $g_UrlIgnoreList = file($l_UrlIgnoreFilename);
	
	for ($i = 0; $i < count($g_UrlIgnoreList); $i++) {
		$g_UrlIgnoreList[$i] = trim($g_UrlIgnoreList[$i]);
	}
}


$l_SkipMask = array(
            '/template_\w{32}.css',
            '/cache/templates/.{1,150}\.tpl\.php',
	    '/system/cache/templates_c/\w{1,40}\.php',
	    '/assets/cache/rss/\w{1,60}',
            '/cache/minify/minify_\w{32}',
            '/cache/page/\w{32}\.php',
            '/cache/object/\w{1,10}/\w{1,10}/\w{1,10}/\w{32}\.php',
            '/cache/wp-cache-\d{32}\.php',
            '/cache/page/\w{32}\.php_expire',
	    '/cache/page/\w{32}-cache-page-\w{32}\.php',
	    '\w{32}-cache-com_content-\w{32}\.php',
	    '\w{32}-cache-mod_custom-\w{32}\.php',
	    '\w{32}-cache-mod_templates-\w{32}\.php',
            '\w{32}-cache-_system-\w{32}\.php',
            '/cache/twig/\w{1,32}/\d+/\w{1,100}\.php', 
            '/autoptimize/js/autoptimize_\w{32}\.js',
            '/bitrix/cache/\w{32}\.php',
            '/bitrix/cache/.+/\w{32}\.php',
            '/bitrix/cache/iblock_find/',
            '/bitrix/managed_cache/MYSQL/user_option/[^/]+/',
            '/bitrix/cache/s1/bitrix/catalog\.section/',
            '/bitrix/cache/s1/bitrix/catalog\.element/',
            '/bitrix/cache/s1/bitrix/menu/',
            '/catalog.element/[^/]+/[^/]+/\w{32}\.php',
            '/bitrix/managed\_cache/.*/\.\w{32}\.php',
            '/core/cache/mgr/smarty/default/.{1,100}\.tpl\.php',
            '/core/cache/resource/web/resources/[0-9]{1,50}\.cache\.php',
            '/smarty/compiled/SC/.*/%%.*\.php',
            '/smarty/.{1,150}\.tpl\.php',
            '/smarty/compile/.{1,150}\.tpl\.cache\.php',
            '/files/templates_c/.{1,150}\.html\.php',
            '/uploads/javascript_global/.{1,150}\.js',
            '/assets/cache/rss/\w{32}',
	    '/assets/cache/docid_\d+_\w{32}\.pageCache\.php',
            '/t3-assets/dev/t3/.*-cache-\w{1,20}-.{1,150}\.php',
	    '/t3-assets/js/js-\w{1,30}\.js',
            '/temp/cache/SC/.*/\.cache\..*\.php',
            '/tmp/sess\_\w{32}$',
            '/assets/cache/docid\_.*\.pageCache\.php',
            '/stat/usage\_\w+\.html',
            '/stat/site\_\w+\.html',
            '/gallery/item/list/\w+\.cache\.php',
            '/core/cache/registry/.*/ext-.*\.php',
            '/core/cache/resource/shk\_/\w+\.cache\.php',
            '/webstat/awstats.*\.txt',
            '/awstats/awstats.*\.txt',
            '/awstats/.{1,80}\.pl',
            '/awstats/.{1,80}\.html',
            '/inc/min/styles_\w+\.min\.css',
            '/inc/min/styles_\w+\.min\.js',
            '/logs/error\_log\..*',
            '/logs/xferlog\..*',
            '/logs/access_log\..*',
            '/logs/cron\..*',
            '/logs/exceptions/.+\.log$',
            '/hyper-cache/[^/]+/[^/]+/[^/]+/index\.html',
            '/mail/new/[^,]+,S=[^,]+,W=.+',
            '/mail/new/[^,]=,S=.+',
            '/application/logs/\d+/\d+/\d+\.php',
            '/sites/default/files/js/js_\w{32}\.js',
            '/yt-assets/\w{32}\.css',
);

$l_SkipSample = array();

if (SMART_SCAN) {
   $g_DirIgnoreList = array_merge($g_DirIgnoreList, $l_SkipMask);
}

QCR_Debug();

// Load custom signatures

try {
	$s_file = new SplFileObject($g_AiBolitAbsolutePath."/ai-bolit.sig");
	$s_file->setFlags(SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);
	foreach ($s_file as $line) {
		$g_FlexDBShe[] = preg_replace('~\G(?:[^#\\\\]+|\\\\.)*+\K#~', '\\#', $line); // escaping #
	}
	stdOut("Loaded " . $s_file->key() . " signatures from ai-bolit.sig");
	$s_file = null; // file handler is closed
} catch (Exception $e) { QCR_Debug( "Import ai-bolit.sig " . $e->getMessage() ); }

QCR_Debug();

	$defaults['skip_ext'] = strtolower(trim($defaults['skip_ext']));
         if ($defaults['skip_ext'] != '') {
	    $g_IgnoredExt = explode(',', $defaults['skip_ext']);
	    for ($i = 0; $i < count($g_IgnoredExt); $i++) {
                $g_IgnoredExt[$i] = trim($g_IgnoredExt[$i]);
             }

	    QCR_Debug('Skip files with extensions: ' . implode(',', $g_IgnoredExt));
	    stdOut('Skip extensions: ' . implode(',', $g_IgnoredExt));
         } 

// scan single file
if (defined('SCAN_FILE')) {
   if (file_exists(SCAN_FILE) && is_file(SCAN_FILE) && is_readable(SCAN_FILE)) {
       stdOut("Start scanning file '" . SCAN_FILE . "'.");
       QCR_ScanFile(SCAN_FILE); 
   } else { 
       stdOut("Error:" . SCAN_FILE . " either is not a file or readable");
   }
} else {
	if (isset($_GET['2check'])) {
		$options['with-2check'] = 1;
	}
   
   // scan list of files from file
   if (!(ICHECK || IMAKE) && isset($options['with-2check']) && file_exists(DOUBLECHECK_FILE)) {
      stdOut("Start scanning the list from '" . DOUBLECHECK_FILE . "'.\n");
      $lines = file(DOUBLECHECK_FILE);
      for ($i = 0, $size = count($lines); $i < $size; $i++) {
         $lines[$i] = trim($lines[$i]);
         if (empty($lines[$i])) unset($lines[$i]);
      }
      /* skip first line with <?php die("Forbidden"); ?> */
      unset($lines[0]);
      $g_FoundTotalFiles = count($lines);
      $i = 1;
      foreach ($lines as $l_FN) {
         is_dir($l_FN) && $g_TotalFolder++;
         printProgress( $i++, $l_FN);
         $BOOL_RESULT = true; // display disable
         is_file($l_FN) && QCR_ScanFile($l_FN, $i);
         $BOOL_RESULT = false; // display enable
      }

      $g_FoundTotalDirs = $g_TotalFolder;
      $g_FoundTotalFiles = $g_TotalFiles;

   } else {
      // scan whole file system
      stdOut("Start scanning '" . ROOT_PATH . "'.\n");
      
      file_exists(QUEUE_FILENAME) && unlink(QUEUE_FILENAME);
      if (ICHECK || IMAKE) {
      // INTEGRITY CHECK
        IMAKE and unlink(INTEGRITY_DB_FILE);
        ICHECK and load_integrity_db();
        QCR_IntegrityCheck(ROOT_PATH);
        stdOut("Found $g_FoundTotalFiles files in $g_FoundTotalDirs directories.");
        if (IMAKE) exit(0);
        if (ICHECK) {
            $i = $g_Counter;
            $g_CRC = 0;
            $changes = array();
            $ref =& $g_IntegrityDB;
            foreach ($g_IntegrityDB as $l_FileName => $type) {
                unset($g_IntegrityDB[$l_FileName]);
                $l_Ext2 = substr(strstr(basename($l_FileName), '.'), 1);
                if (in_array(strtolower($l_Ext2), $g_IgnoredExt)) {
                    continue;
                }
                for ($dr = 0; $dr < count($g_DirIgnoreList); $dr++) {
                    if (($g_DirIgnoreList[$dr] != '') && preg_match('#' . $g_DirIgnoreList[$dr] . '#', $l_FileName, $l_Found)) {
                        continue 2;
                    }
                }
                $type = in_array($type, array('added', 'modified')) ? $type : 'deleted';
                $type .= substr($l_FileName, -1) == '/' ? 'Dirs' : 'Files';
                $changes[$type][] = ++$i;
                AddResult($l_FileName, $i);
            }
            $g_FoundTotalFiles = count($changes['addedFiles']) + count($changes['modifiedFiles']);
            stdOut("Found changes " . count($changes['modifiedFiles']) . " files and added " . count($changes['addedFiles']) . " files.");
        }
        
      } else {
      QCR_ScanDirectories(ROOT_PATH);
      stdOut("Found $g_FoundTotalFiles files in $g_FoundTotalDirs directories.");
      }

      QCR_Debug();
      stdOut(str_repeat(' ', 160),false);
      QCR_GoScan(0);
      unlink(QUEUE_FILENAME);
      if (defined('PROGRESS_LOG_FILE') && file_exists(PROGRESS_LOG_FILE)) @unlink(PROGRESS_LOG_FILE);
   }
}

QCR_Debug();

if (true) {
   $g_HeuristicDetected = array();
   $g_Iframer = array();
   $g_Base64 = array();
}


// whitelist

$snum = 0;
$list = check_whitelist($g_Structure['crc'], $snum);

foreach (array('g_CriticalPHP', 'g_CriticalJS', 'g_Iframer', 'g_Base64', 'g_Phishing', 'g_AdwareList', 'g_Redirect') as $p) {
	if (empty($$p)) continue;
	
	$p_Fragment = $p . "Fragment";
	$p_Sig = $p . "Sig";
	if ($p == 'g_Redirect') $p_Fragment = $p . "PHPFragment";
	if ($p == 'g_Phishing') $p_Sig = $p . "SigFragment";

	$count = count($$p);
	for ($i = 0; $i < $count; $i++) {
		$id = "{${$p}[$i]}";
		if (in_array($g_Structure['crc'][$id], $list)) {
			unset($GLOBALS[$p][$i]);
			unset($GLOBALS[$p_Sig][$i]);
			unset($GLOBALS[$p_Fragment][$i]);
		}
	}

	$$p = array_values($$p);
	$$p_Fragment = array_values($$p_Fragment);
	if (!empty($$p_Sig)) $$p_Sig = array_values($$p_Sig);
}


////////////////////////////////////////////////////////////////////////////
if (AI_HOSTER) {
   $g_IframerFragment = array();
   $g_Iframer = array();
   $g_Redirect = array();
   $g_Doorway = array();
   $g_EmptyLink = array();
   $g_HeuristicType = array();
   $g_HeuristicDetected = array();
   $g_WarningPHP = array();
   $g_AdwareList = array();
   $g_Phishing = array(); 
   $g_PHPCodeInside = array();
   $g_PHPCodeInsideFragment = array();
   $g_NotRead = array();
   $g_WarningPHPFragment = array();
   $g_WarningPHPSig = array();
   $g_BigFiles = array();
   $g_RedirectPHPFragment = array();
   $g_EmptyLinkSrc = array();
   $g_Base64Fragment = array();
   $g_UnixExec = array();
   $g_PhishingSigFragment = array();
   $g_PhishingFragment = array();
   $g_PhishingSig = array();
   $g_IframerFragment = array();
   $g_CMS = array();
   $g_AdwareListFragment = array(); 
   $g_Vulnerable = array();
}

 if (BOOL_RESULT && (!defined('NEED_REPORT'))) {
  if ((count($g_CriticalPHP) > 0) OR (count($g_CriticalJS) > 0) OR (count($g_Base64) > 0) OR  (count($g_Iframer) > 0) OR  (count($g_UnixExec) > 0))
  {
  echo "1\n";
  exit(0);
  }
 }
////////////////////////////////////////////////////////////////////////////
$l_Template = str_replace("@@SERVICE_INFO@@", htmlspecialchars("[" . $int_enc . "][" . $snum . "]"), $l_Template);

$l_Template = str_replace("@@PATH_URL@@", (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $g_AddPrefix . str_replace($g_NoPrefix, '', addSlash(ROOT_PATH))), $l_Template);

$time_taken = seconds2Human(microtime(true) - START_TIME);

$l_Template = str_replace("@@SCANNED@@", sprintf(AI_STR_013, $g_TotalFolder, $g_TotalFiles), $l_Template);

$l_ShowOffer = false;

stdOut("\nBuilding report [ mode = " . AI_EXPERT . " ]\n");

//stdOut("\nLoaded signatures: " . count($g_FlexDBShe) . " / " . count($g_JSVirSig) . "\n");

////////////////////////////////////////////////////////////////////////////
// save 
if (!(ICHECK || IMAKE))
if (isset($options['with-2check']) || isset($options['quarantine']))
if ((count($g_CriticalPHP) > 0) OR (count($g_CriticalJS) > 0) OR (count($g_Base64) > 0) OR 
   (count($g_Iframer) > 0) OR  (count($g_UnixExec))) 
{
  if (!file_exists(DOUBLECHECK_FILE)) {	  
      if ($l_FH = fopen(DOUBLECHECK_FILE, 'w')) {
         fputs($l_FH, '<?php die("Forbidden"); ?>' . "\n");

         $l_CurrPath = dirname(__FILE__);
		 
		 if (!isset($g_CriticalPHP)) { $g_CriticalPHP = array(); }
		 if (!isset($g_CriticalJS)) { $g_CriticalJS = array(); }
		 if (!isset($g_Iframer)) { $g_Iframer = array(); }
		 if (!isset($g_Base64)) { $g_Base64 = array(); }
		 if (!isset($g_Phishing)) { $g_Phishing = array(); }
		 if (!isset($g_AdwareList)) { $g_AdwareList = array(); }
		 if (!isset($g_Redirect)) { $g_Redirect = array(); }
		 
         $tmpIndex = array_merge($g_CriticalPHP, $g_CriticalJS, $g_Phishing, $g_Base64, $g_Iframer, $g_AdwareList, $g_Redirect);
         $tmpIndex = array_values(array_unique($tmpIndex));

         for ($i = 0; $i < count($tmpIndex); $i++) {
             $tmpIndex[$i] = str_replace($l_CurrPath, '.', $g_Structure['n'][$tmpIndex[$i]]);
         }

         for ($i = 0; $i < count($g_UnixExec); $i++) {
             $tmpIndex[] = str_replace($l_CurrPath, '.', $g_UnixExec[$i]);
         }

         $tmpIndex = array_values(array_unique($tmpIndex));

         for ($i = 0; $i < count($tmpIndex); $i++) {
             fputs($l_FH, $tmpIndex[$i] . "\n");
         }

         fclose($l_FH);
      } else {
         stdOut("Error! Cannot create " . DOUBLECHECK_FILE);
      }      
  } else {
      stdOut(DOUBLECHECK_FILE . ' already exists.');
      if (AI_STR_044 != '') $l_Result .= '<div class="rep">' . AI_STR_044 . '</div>';
  }
 
}

////////////////////////////////////////////////////////////////////////////

$l_Summary = '<div class="title">' . AI_STR_074 . '</div>';
$l_Summary .= '<table cellspacing=0 border=0>';

if (count($g_Redirect) > 0) {
   $l_Summary .= makeSummary(AI_STR_059, count($g_Redirect), "crit");
}

if (count($g_CriticalPHP) > 0) {
   $l_Summary .= makeSummary(AI_STR_060, count($g_CriticalPHP), "crit");
}

if (count($g_CriticalJS) > 0) {
   $l_Summary .= makeSummary(AI_STR_061, count($g_CriticalJS), "crit");
}

if (count($g_Phishing) > 0) {
   $l_Summary .= makeSummary(AI_STR_062, count($g_Phishing), "crit");
}

if (count($g_UnixExec) > 0) {
   $l_Summary .= makeSummary(AI_STR_063, count($g_UnixExec), (AI_EXPERT > 1 ? 'crit' : 'warn'));
}

if (count($g_Iframer) > 0) {
   $l_Summary .= makeSummary(AI_STR_064, count($g_Iframer), "crit");
}

if (count($g_NotRead) > 0) {
   $l_Summary .= makeSummary(AI_STR_066, count($g_NotRead), "crit");
}

if (count($g_Base64) > 0) {
   $l_Summary .= makeSummary(AI_STR_067, count($g_Base64), (AI_EXPERT > 1 ? 'crit' : 'warn'));
}

if (count($g_BigFiles) > 0) {
   $l_Summary .= makeSummary(AI_STR_065, count($g_BigFiles), "warn");
}

if (count($g_HeuristicDetected) > 0) {
   $l_Summary .= makeSummary(AI_STR_068, count($g_HeuristicDetected), "warn");
}

if (count($g_SymLinks) > 0) {
   $l_Summary .= makeSummary(AI_STR_069, count($g_SymLinks), "warn");
}

if (count($g_HiddenFiles) > 0) {
   $l_Summary .= makeSummary(AI_STR_070, count($g_HiddenFiles), "warn");
}

if (count($g_AdwareList) > 0) {
   $l_Summary .= makeSummary(AI_STR_072, count($g_AdwareList), "warn");
}

if (count($g_EmptyLink) > 0) {
   $l_Summary .= makeSummary(AI_STR_073, count($g_EmptyLink), "warn");
}

 $l_Summary .= "</table>";

$l_ArraySummary = array();
$l_ArraySummary["redirect"] = count($g_Redirect);
$l_ArraySummary["critical_php"] = count($g_CriticalPHP);
$l_ArraySummary["critical_js"] = count($g_CriticalJS);
$l_ArraySummary["phishing"] = count($g_Phishing);
$l_ArraySummary["unix_exec"] = count($g_UnixExec);
$l_ArraySummary["iframes"] = count($g_Iframer);
$l_ArraySummary["not_read"] = count($g_NotRead);
$l_ArraySummary["base64"] = count($g_Base64);
$l_ArraySummary["heuristics"] = count($g_HeuristicDetected);
$l_ArraySummary["symlinks"] = count($g_SymLinks);
$l_ArraySummary["big_files_skipped"] = count($g_BigFiles);

 if (function_exists('json_encode')) { $l_Summary .= "<!--[json]" . json_encode($l_ArraySummary) . "[/json]-->"; }

 $l_Summary .= "<div class=details style=\"margin: 20px 20px 20px 0\">" . AI_STR_080 . "</div>\n";

 $l_Template = str_replace("@@SUMMARY@@", $l_Summary, $l_Template);


 $l_Result .= AI_STR_015;
 
 $l_Template = str_replace("@@VERSION@@", AI_VERSION, $l_Template);
 
////////////////////////////////////////////////////////////////////////////



if (function_exists("gethostname") && is_callable("gethostname")) {
  $l_HostName = gethostname();
} else {
  $l_HostName = '???';
}

$l_PlainResult = "# Malware list detected by AI-Bolit (https://revisium.com/ai/) on " . date("d/m/Y H:i:s", time()) . " " . $l_HostName .  "\n\n";

$l_RawReport = array();

if (!AI_HOSTER) {
   stdOut("Building list of vulnerable scripts " . count($g_Vulnerable));

   if (count($g_Vulnerable) > 0) {
       $l_Result .= '<div class="note_vir">' . AI_STR_081 . ' (' . count($g_Vulnerable) . ')</div><div class="crit">';
    	foreach ($g_Vulnerable as $l_Item) {
   	    $l_Result .= '<li>' . makeSafeFn($g_Structure['n'][$l_Item['ndx']], true) . ' - ' . $l_Item['id'] . '</li>';
               $l_PlainResult .= '[VULNERABILITY] ' . replacePathArray($g_Structure['n'][$l_Item['ndx']]) . ' - ' . $l_Item['id'] . "\n";
    	}
   	
     $l_Result .= '</div><p>' . PHP_EOL;
     $l_PlainResult .= "\n";
   }
}


stdOut("Building list of shells " . count($g_CriticalPHP));

$l_RawReport['vulners'] = getRawJsonVuln($g_Vulnerable);

if (count($g_CriticalPHP) > 0) {
  $g_CriticalPHP = array_slice($g_CriticalPHP, 0, 15000);
  $l_RawReport['php_malware'] = getRawJson($g_CriticalPHP, $g_CriticalPHPFragment, $g_CriticalPHPSig);
  $l_Result .= '<div class="note_vir">' . AI_STR_016 . ' (' . count($g_CriticalPHP) . ')</div><div class="crit">';
  $l_Result .= printList($g_CriticalPHP, $g_CriticalPHPFragment, true, $g_CriticalPHPSig, 'table_crit');
  $l_PlainResult .= '[SERVER MALWARE]' . "\n" . printPlainList($g_CriticalPHP, $g_CriticalPHPFragment, true, $g_CriticalPHPSig, 'table_crit') . "\n";
  $l_Result .= '</div>' . PHP_EOL;

  $l_ShowOffer = true;
} else {
  $l_Result .= '<div class="ok"><b>' . AI_STR_017. '</b></div>';
}

stdOut("Building list of js " . count($g_CriticalJS));

if (count($g_CriticalJS) > 0) {
  $g_CriticalJS = array_slice($g_CriticalJS, 0, 15000);
  $l_RawReport['js_malware'] = getRawJson($g_CriticalJS, $g_CriticalJSFragment, $g_CriticalJSSig);
  $l_Result .= '<div class="note_vir">' . AI_STR_018 . ' (' . count($g_CriticalJS) . ')</div><div class="crit">';
  $l_Result .= printList($g_CriticalJS, $g_CriticalJSFragment, true, $g_CriticalJSSig, 'table_vir');
  $l_PlainResult .= '[CLIENT MALWARE / JS]'  . "\n" . printPlainList($g_CriticalJS, $g_CriticalJSFragment, true, $g_CriticalJSSig, 'table_vir') . "\n";
  $l_Result .= "</div>" . PHP_EOL;

  $l_ShowOffer = true;
}

if (!AI_HOSTER) {
   stdOut("Building phishing pages " . count($g_Phishing));

   if (count($g_Phishing) > 0) {
     $l_RawReport['phishing'] = getRawJson($g_Phishing, $g_PhishingFragment, $g_PhishingSigFragment);
     $l_Result .= '<div class="note_vir">' . AI_STR_058 . ' (' . count($g_Phishing) . ')</div><div class="crit">';
     $l_Result .= printList($g_Phishing, $g_PhishingFragment, true, $g_PhishingSigFragment, 'table_vir');
     $l_PlainResult .= '[PHISHING]'  . "\n" . printPlainList($g_Phishing, $g_PhishingFragment, true, $g_PhishingSigFragment, 'table_vir') . "\n";
     $l_Result .= "</div>". PHP_EOL;

     $l_ShowOffer = true;
   }

   stdOut("Building list of iframes " . count($g_Iframer));

   if (count($g_Iframer) > 0) {
     $l_RawReport['iframer'] = getRawJson($g_Iframer, $g_IframerFragment);
     $l_ShowOffer = true;
     $l_Result .= '<div class="note_vir">' . AI_STR_021 . ' (' . count($g_Iframer) . ')</div><div class="crit">';
     $l_Result .= printList($g_Iframer, $g_IframerFragment, true);
     $l_Result .= "</div>" . PHP_EOL;

   }

   stdOut("Building list of base64s " . count($g_Base64));

   if (count($g_Base64) > 0) {
     $l_RawReport['warn_enc'] = getRawJson($g_Base64, $g_Base64Fragment);
     if (AI_EXPERT > 1) $l_ShowOffer = true;
     
     $l_Result .= '<div class="note_' . (AI_EXPERT > 1 ? 'vir' : 'warn') . '">' . AI_STR_020 . ' (' . count($g_Base64) . ')</div><div class="' . (AI_EXPERT > 1 ? 'crit' : 'warn') . '">';
     $l_Result .= printList($g_Base64, $g_Base64Fragment, true);
     $l_PlainResult .= '[ENCODED / SUSP_EXT]' . "\n" . printPlainList($g_Base64, $g_Base64Fragment, true) . "\n";
     $l_Result .= "</div>" . PHP_EOL;

   }

   stdOut("Building list of redirects " . count($g_Redirect));
   if (count($g_Redirect) > 0) {
     $l_RawReport['redirect'] = getRawJson($g_Redirect, $g_RedirectPHPFragment);
     $l_ShowOffer = true;
     $l_Result .= '<div class="note_vir">' . AI_STR_027 . ' (' . count($g_Redirect) . ')</div><div class="crit">';
     $l_Result .= printList($g_Redirect, $g_RedirectPHPFragment, true);
     $l_Result .= "</div>" . PHP_EOL;
   }


   stdOut("Building list of unread files " . count($g_NotRead));

   if (count($g_NotRead) > 0) {
     $g_NotRead = array_slice($g_NotRead, 0, AIBOLIT_MAX_NUMBER);
     $l_RawReport['not_read'] = $g_NotRead;
     $l_Result .= '<div class="note_vir">' . AI_STR_030 . ' (' . count($g_NotRead) . ')</div><div class="crit">';
     $l_Result .= printList($g_NotRead);
     $l_Result .= "</div><div class=\"spacer\"></div>" . PHP_EOL;
     $l_PlainResult .= '[SCAN ERROR / SKIPPED]' . "\n" . printPlainList($g_NotRead) . "\n\n";
   }

   stdOut("Building list of symlinks " . count($g_SymLinks));

   if (count($g_SymLinks) > 0) {
     $g_SymLinks = array_slice($g_SymLinks, 0, AIBOLIT_MAX_NUMBER);
     $l_RawReport['sym_links'] = $g_SymLinks;
     $l_Result .= '<div class="note_vir">' . AI_STR_022 . ' (' . count($g_SymLinks) . ')</div><div class="crit">';
     $l_Result .= nl2br(makeSafeFn(implode("\n", $g_SymLinks), true));
     $l_Result .= "</div><div class=\"spacer\"></div>";
   }

   stdOut("Building list of unix executables and odd scripts " . count($g_UnixExec));

   if (count($g_UnixExec) > 0) {
     $g_UnixExec = array_slice($g_UnixExec, 0, AIBOLIT_MAX_NUMBER);
     $l_RawReport['unix_exec'] = $g_UnixExec;
     $l_Result .= '<div class="note_' . (AI_EXPERT > 1 ? 'vir' : 'warn') . '">' . AI_STR_019 . ' (' . count($g_UnixExec) . ')</div><div class="' . (AI_EXPERT > 1 ? 'crit' : 'warn') . '">';
     $l_Result .= nl2br(makeSafeFn(implode("\n", $g_UnixExec), true));
     $l_PlainResult .= '[UNIX EXEC]' . "\n" . implode("\n", replacePathArray($g_UnixExec)) . "\n\n";
     $l_Result .= "</div>" . PHP_EOL;

     if (AI_EXPERT > 1) $l_ShowOffer = true;
   }
}
////////////////////////////////////
if (!AI_HOSTER) {
   $l_WarningsNum = count($g_HeuristicDetected) + count($g_HiddenFiles) + count($g_BigFiles) + count($g_PHPCodeInside) + count($g_AdwareList) + count($g_EmptyLink) + count($g_Doorway) + (count($g_WarningPHP[0]) + count($g_WarningPHP[1]) + count($g_SkippedFolders));

   if ($l_WarningsNum > 0) {
   	$l_Result .= "<div style=\"margin-top: 20px\" class=\"title\">" . AI_STR_026 . "</div>";
   }

   stdOut("Building list of links/adware " . count($g_AdwareList));

   if (count($g_AdwareList) > 0) {
     $l_RawReport['adware'] = getRawJson($g_AdwareList, $g_AdwareListFragment);
     $l_Result .= '<div class="note_warn">' . AI_STR_029 . '</div><div class="warn">';
     $l_Result .= printList($g_AdwareList, $g_AdwareListFragment, true);
     $l_PlainResult .= '[ADWARE]' . "\n" . printPlainList($g_AdwareList, $g_AdwareListFragment, true) . "\n";
     $l_Result .= "</div>" . PHP_EOL;

   }

   stdOut("Building list of heuristics " . count($g_HeuristicDetected));

   if (count($g_HeuristicDetected) > 0) {
     $l_RawReport['heuristic'] = $g_HeuristicDetected;
     $l_Result .= '<div class="note_warn">' . AI_STR_052 . ' (' . count($g_HeuristicDetected) . ')</div><div class="warn">';
     for ($i = 0; $i < count($g_HeuristicDetected); $i++) {
   	   $l_Result .= '<li>' . makeSafeFn($g_Structure['n'][$g_HeuristicDetected[$i]], true) . ' (' . get_descr_heur($g_HeuristicType[$i]) . ')</li>';
     }
     
     $l_Result .= '</ul></div><div class=\"spacer\"></div>' . PHP_EOL;
   }

   stdOut("Building list of hidden files " . count($g_HiddenFiles));
   if (count($g_HiddenFiles) > 0) {
     $g_HiddenFiles = array_slice($g_HiddenFiles, 0, AIBOLIT_MAX_NUMBER);
     $l_RawReport['hidden'] = $g_HiddenFiles;
     $l_Result .= '<div class="note_warn">' . AI_STR_023 . ' (' . count($g_HiddenFiles) . ')</div><div class="warn">';
     $l_Result .= nl2br(makeSafeFn(implode("\n", $g_HiddenFiles), true));
     $l_Result .= "</div><div class=\"spacer\"></div>" . PHP_EOL;
     $l_PlainResult .= '[HIDDEN]' . "\n" . implode("\n", replacePathArray($g_HiddenFiles)) . "\n\n";
   }

   stdOut("Building list of bigfiles " . count($g_BigFiles));
   $max_size_to_scan = getBytes(MAX_SIZE_TO_SCAN);
   $max_size_to_scan = $max_size_to_scan > 0 ? $max_size_to_scan : getBytes('1m');

   if (count($g_BigFiles) > 0) {
     $g_BigFiles = array_slice($g_BigFiles, 0, AIBOLIT_MAX_NUMBER);
     $l_RawReport['big_files'] = getRawJson($g_BigFiles);
     $l_Result .= "<div class=\"note_warn\">" . sprintf(AI_STR_038, bytes2Human($max_size_to_scan)) . '</div><div class="warn">';
     $l_Result .= printList($g_BigFiles);
     $l_Result .= "</div>";
     $l_PlainResult .= '[BIG FILES / SKIPPED]' . "\n" . printPlainList($g_BigFiles) . "\n\n";
   } 

   stdOut("Building list of php inj " . count($g_PHPCodeInside));

   if ((count($g_PHPCodeInside) > 0) && (($defaults['report_mask'] & REPORT_MASK_PHPSIGN) == REPORT_MASK_PHPSIGN)) {
     $l_Result .= '<div class="note_warn">' . AI_STR_028 . '</div><div class="warn">';
     $l_Result .= printList($g_PHPCodeInside, $g_PHPCodeInsideFragment, true);
     $l_Result .= "</div>" . PHP_EOL;

   }

   stdOut("Building list of empty links " . count($g_EmptyLink));
   if (count($g_EmptyLink) > 0) {
     $g_EmptyLink = array_slice($g_EmptyLink, 0, AIBOLIT_MAX_NUMBER);
     $l_Result .= '<div class="note_warn">' . AI_STR_031 . '</div><div class="warn">';
     $l_Result .= printList($g_EmptyLink, '', true);

     $l_Result .= AI_STR_032 . '<br/>';
     
     if (count($g_EmptyLink) == MAX_EXT_LINKS) {
         $l_Result .= '(' . AI_STR_033 . MAX_EXT_LINKS . ')<br/>';
       }
      
     for ($i = 0; $i < count($g_EmptyLink); $i++) {
   	$l_Idx = $g_EmptyLink[$i];
       for ($j = 0; $j < count($g_EmptyLinkSrc[$l_Idx]); $j++) {
         $l_Result .= '<span class="details">' . makeSafeFn($g_Structure['n'][$g_EmptyLink[$i]], true) . ' &rarr; ' . htmlspecialchars($g_EmptyLinkSrc[$l_Idx][$j]) . '</span><br/>';
   	}
     }

     $l_Result .= "</div>";

   }

   stdOut("Building list of doorways " . count($g_Doorway));

   if ((count($g_Doorway) > 0) && (($defaults['report_mask'] & REPORT_MASK_DOORWAYS) == REPORT_MASK_DOORWAYS)) {
     $g_Doorway = array_slice($g_Doorway, 0, AIBOLIT_MAX_NUMBER);
     $l_RawReport['doorway'] = getRawJson($g_Doorway);
     $l_Result .= '<div class="note_warn">' . AI_STR_034 . '</div><div class="warn">';
     $l_Result .= printList($g_Doorway);
     $l_Result .= "</div>" . PHP_EOL;

   }

   stdOut("Building list of php warnings " . (count($g_WarningPHP[0]) + count($g_WarningPHP[1])));

   if (($defaults['report_mask'] & REPORT_MASK_SUSP) == REPORT_MASK_SUSP) {
      if ((count($g_WarningPHP[0]) + count($g_WarningPHP[1])) > 0) {
        $g_WarningPHP[0] = array_slice($g_WarningPHP[0], 0, AIBOLIT_MAX_NUMBER);
        $g_WarningPHP[1] = array_slice($g_WarningPHP[1], 0, AIBOLIT_MAX_NUMBER);
        $l_Result .= '<div class="note_warn">' . AI_STR_035 . '</div><div class="warn">';

        for ($i = 0; $i < count($g_WarningPHP); $i++) {
            if (count($g_WarningPHP[$i]) > 0) 
               $l_Result .= printList($g_WarningPHP[$i], $g_WarningPHPFragment[$i], true, $g_WarningPHPSig, 'table_warn' . $i);
        }                                                                                                                    
        $l_Result .= "</div>" . PHP_EOL;

      } 
   }

   stdOut("Building list of skipped dirs " . count($g_SkippedFolders));
   if (count($g_SkippedFolders) > 0) {
        $l_Result .= '<div class="note_warn">' . AI_STR_036 . '</div><div class="warn">';
        $l_Result .= nl2br(makeSafeFn(implode("\n", $g_SkippedFolders), true));   
        $l_Result .= "</div>" . PHP_EOL;
    }

    if (count($g_CMS) > 0) {
         $l_RawReport['cms'] = $g_CMS;
         $l_Result .= "<div class=\"note_warn\">" . AI_STR_037 . "<br/>";
         $l_Result .= nl2br(makeSafeFn(implode("\n", $g_CMS)));
         $l_Result .= "</div>";
    }
}

if (ICHECK) {
	$l_Result .= "<div style=\"margin-top: 20px\" class=\"title\">" . AI_STR_087 . "</div>";
	
    stdOut("Building list of added files " . count($changes['addedFiles']));
    if (count($changes['addedFiles']) > 0) {
      $l_Result .= '<div class="note_int">' . AI_STR_082 . ' (' . count($changes['addedFiles']) . ')</div><div class="intitem">';
      $l_Result .= printList($changes['addedFiles']);
      $l_Result .= "</div>" . PHP_EOL;
    }

    stdOut("Building list of modified files " . count($changes['modifiedFiles']));
    if (count($changes['modifiedFiles']) > 0) {
      $l_Result .= '<div class="note_int">' . AI_STR_083 . ' (' . count($changes['modifiedFiles']) . ')</div><div class="intitem">';
      $l_Result .= printList($changes['modifiedFiles']);
      $l_Result .= "</div>" . PHP_EOL;
    }

    stdOut("Building list of deleted files " . count($changes['deletedFiles']));
    if (count($changes['deletedFiles']) > 0) {
      $l_Result .= '<div class="note_int">' . AI_STR_084 . ' (' . count($changes['deletedFiles']) . ')</div><div class="intitem">';
      $l_Result .= printList($changes['deletedFiles']);
      $l_Result .= "</div>" . PHP_EOL;
    }

    stdOut("Building list of added dirs " . count($changes['addedDirs']));
    if (count($changes['addedDirs']) > 0) {
      $l_Result .= '<div class="note_int">' . AI_STR_085 . ' (' . count($changes['addedDirs']) . ')</div><div class="intitem">';
      $l_Result .= printList($changes['addedDirs']);
      $l_Result .= "</div>" . PHP_EOL;
    }

    stdOut("Building list of deleted dirs " . count($changes['deletedDirs']));
    if (count($changes['deletedDirs']) > 0) {
      $l_Result .= '<div class="note_int">' . AI_STR_086 . ' (' . count($changes['deletedDirs']) . ')</div><div class="intitem">';
      $l_Result .= printList($changes['deletedDirs']);
      $l_Result .= "</div>" . PHP_EOL;
    }
}

if (!isCli()) {
   $l_Result .= QCR_ExtractInfo($l_PhpInfoBody[1]);
}


if (function_exists('memory_get_peak_usage')) {
  $l_Template = str_replace("@@MEMORY@@", AI_STR_043 . bytes2Human(memory_get_peak_usage()), $l_Template);
}

$l_Template = str_replace('@@WARN_QUICK@@', ((SCAN_ALL_FILES || $g_SpecificExt) ? '' : AI_STR_045), $l_Template);

if ($l_ShowOffer) {
	$l_Template = str_replace('@@OFFER@@', $l_Offer, $l_Template);
} else {
	$l_Template = str_replace('@@OFFER@@', AI_STR_002, $l_Template);
}

$l_Template = str_replace('@@OFFER2@@', $l_Offer2, $l_Template);

$l_Template = str_replace('@@CAUTION@@', AI_STR_003, $l_Template);

$l_Template = str_replace('@@CREDITS@@', AI_STR_075, $l_Template);

$l_Template = str_replace('@@FOOTER@@', AI_STR_076, $l_Template);

$l_Template = str_replace('@@STAT@@', sprintf(AI_STR_012, $time_taken, date('d-m-Y в H:i:s', floor(START_TIME)) , date('d-m-Y в H:i:s')), $l_Template);

////////////////////////////////////////////////////////////////////////////
$l_Template = str_replace("@@MAIN_CONTENT@@", $l_Result, $l_Template);

if (!isCli())
{
    echo $l_Template;
    exit;
}

if (!defined('REPORT') OR REPORT === '')
{
	die2('Report not written.');
}
 
// write plain text result
if (PLAIN_FILE != '') {
	
    $l_PlainResult = preg_replace('|__AI_LINE1__|smi', '[', $l_PlainResult);
    $l_PlainResult = preg_replace('|__AI_LINE2__|smi', '] ', $l_PlainResult);
    $l_PlainResult = preg_replace('|__AI_MARKER__|smi', ' %> ', $l_PlainResult);

   if ($l_FH = fopen(PLAIN_FILE, "w")) {
      fputs($l_FH, $l_PlainResult);
      fclose($l_FH);
   }
}

// write json result
if (defined('JSON_FILE')) {	
   if ($l_FH = fopen(JSON_FILE, "w")) {
      fputs($l_FH, json_encode($l_RawReport));
      fclose($l_FH);
   }
}

// write serialized result
if (defined('PHP_FILE')) {	
   if ($l_FH = fopen(PHP_FILE, "w")) {
      fputs($l_FH, serialize($l_RawReport));
      fclose($l_FH);
   }
}

$emails = getEmails(REPORT);

if (!$emails) {
	if ($l_FH = fopen($file, "w")) {
	   fputs($l_FH, $l_Template);
	   fclose($l_FH);
	   stdOut("\nReport written to '$file'.");
	} else {
		stdOut("\nCannot create '$file'.");
	}
}	else	{
		$headers = array(
			'MIME-Version: 1.0',
			'Content-type: text/html; charset=UTF-8',
			'From: ' . ($defaults['email_from'] ? $defaults['email_from'] : 'AI-Bolit@myhost')
		);

		for ($i = 0, $size = sizeof($emails); $i < $size; $i++)
		{
			mail($emails[$i], 'AI-Bolit Report ' . date("d/m/Y H:i", time()), $l_Result, implode("\r\n", $headers));
		}

		stdOut("\nReport sended to " . implode(', ', $emails));
}


$time_taken = microtime(true) - START_TIME;
$time_taken = number_format($time_taken, 5);


stdOut("Scanning complete! Time taken: " . seconds2Human($time_taken));

if (DEBUG_PERFORMANCE) {
   $keys = array_keys($g_RegExpStat);
   for ($i = 0; $i < count($keys); $i++) {
       $g_RegExpStat[$keys[$i]] = round($g_RegExpStat[$keys[$i]] * 1000000);
   }

   arsort($g_RegExpStat);

   foreach ($g_RegExpStat as $r => $v) {
      echo $v . "\t\t" . $r . "\n";
   }

   die();
}

stdOut("\n\n!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!");
stdOut("Attention! DO NOT LEAVE either ai-bolit.php or AI-BOLIT-REPORT-<xxxx>-<yy>.html \nfile on server. COPY it locally then REMOVE from server. ");
stdOut("!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!");

if (isset($options['quarantine'])) {
	Quarantine();
}

if (isset($options['cmd'])) {
	stdOut("Run \"{$options['cmd']}\" ");
	system($options['cmd']);
}

QCR_Debug();

# exit with code

$l_EC1 = count($g_CriticalPHP);
$l_EC2 = count($g_CriticalJS) + count($g_Phishing) + count($g_WarningPHP[0]) + count($g_WarningPHP[1]);
$code = 0;

if ($l_EC1 > 0) {
	$code = 2;
} else {
	if ($l_EC2 > 0) {
		$code = 1;
	}
}

$stat = array('php_malware' => count($g_CriticalPHP), 'js_malware' => count($g_CriticalJS), 'phishing' => count($g_Phishing));

if (function_exists('aibolit_onComplete')) { aibolit_onComplete($code, $stat); }

stdOut('Exit code ' . $code);
exit($code);

############################################# END ###############################################

function Quarantine()
{
	if (!file_exists(DOUBLECHECK_FILE)) {
		return;
	}
	
	$g_QuarantinePass = 'aibolit';
	
	$archive = "AI-QUARANTINE-" .rand(100000, 999999) . ".zip";
	$infoFile = substr($archive, 0, -3) . "txt";
	$report = REPORT_PATH . DIR_SEPARATOR . REPORT_FILE;
	

	foreach (file(DOUBLECHECK_FILE) as $file) {
		$file = trim($file);
		if (!is_file($file)) continue;
	
		$lStat = stat($file);
		
		// skip files over 300KB
		if ($lStat['size'] > 300*1024) continue;

		// http://www.askapache.com/security/chmod-stat.html
		$p = $lStat['mode'];
		$perm ='-';
		$perm.=(($p&0x0100)?'r':'-').(($p&0x0080)?'w':'-');
		$perm.=(($p&0x0040)?(($p&0x0800)?'s':'x'):(($p&0x0800)?'S':'-'));
		$perm.=(($p&0x0020)?'r':'-').(($p&0x0010)?'w':'-');
		$perm.=(($p&0x0008)?(($p&0x0400)?'s':'x'):(($p&0x0400)?'S':'-'));
		$perm.=(($p&0x0004)?'r':'-').(($p&0x0002)?'w':'-');
		$perm.=(($p&0x0001)?(($p&0x0200)?'t':'x'):(($p&0x0200)?'T':'-'));
		
		$owner = (function_exists('posix_getpwuid'))? @posix_getpwuid($lStat['uid']) : array('name' => $lStat['uid']);
		$group = (function_exists('posix_getgrgid'))? @posix_getgrgid($lStat['gid']) : array('name' => $lStat['uid']);

		$inf['permission'][] = $perm;
		$inf['owner'][] = $owner['name'];
		$inf['group'][] = $group['name'];
		$inf['size'][] = $lStat['size'] > 0 ? bytes2Human($lStat['size']) : '-';
		$inf['ctime'][] = $lStat['ctime'] > 0 ? date("d/m/Y H:i:s", $lStat['ctime']) : '-';
		$inf['mtime'][] = $lStat['mtime'] > 0 ? date("d/m/Y H:i:s", $lStat['mtime']) : '-';
		$files[] = strpos($file, './') === 0 ? substr($file, 2) : $file;
	}
	
	// get config files for cleaning
	$configFilesRegex = 'config(uration|\.in[ic])?\.php$|dbconn\.php$';
	$configFiles = preg_grep("~$configFilesRegex~", $files);

	// get columns width
	$width = array();
	foreach (array_keys($inf) as $k) {
		$width[$k] = strlen($k);
		for ($i = 0; $i < count($inf[$k]); ++$i) {
			$len = strlen($inf[$k][$i]);
			if ($len > $width[$k])
				$width[$k] = $len;
		}
	}

	// headings of columns
	$info = '';
	foreach (array_keys($inf) as $k) {
		$info .= str_pad($k, $width[$k], ' ', STR_PAD_LEFT). ' ';
	}
	$info .= "name\n";
	
	for ($i = 0; $i < count($files); ++$i) {
		foreach (array_keys($inf) as $k) {
			$info .= str_pad($inf[$k][$i], $width[$k], ' ', STR_PAD_LEFT). ' ';
		}
		$info .= $files[$i]."\n";
	}
	unset($inf, $width);

	exec("zip -v 2>&1", $output,$code);

	if ($code == 0) {
		$filter = '';
		if ($configFiles && exec("grep -V 2>&1", $output, $code) && $code == 0) {
			$filter = "|grep -v -E '$configFilesRegex'";
		}

		exec("cat AI-BOLIT-DOUBLECHECK.php $filter |zip -@ --password $g_QuarantinePass $archive", $output, $code);
		if ($code == 0) {
			file_put_contents($infoFile, $info);
			$m = array();
			if (!empty($filter)) {
				foreach ($configFiles as $file) {
					$tmp = file_get_contents($file);
					// remove  passwords
					$tmp = preg_replace('~^.*?pass.*~im', '', $tmp);
					// new file name
					$file = preg_replace('~.*/~', '', $file) . '-' . rand(100000, 999999);
					file_put_contents($file, $tmp);
					$m[] = $file;
				}
			}

			exec("zip -j --password $g_QuarantinePass $archive $infoFile $report " . DOUBLECHECK_FILE . ' ' . implode(' ', $m));
			stdOut("\nCreate archive '" . realpath($archive) . "'");
			stdOut("This archive have password '$g_QuarantinePass'");
			foreach ($m as $file) unlink($file);
			unlink($infoFile);
			return;
		}
	}
	
	$zip = new ZipArchive;
	
	if ($zip->open($archive, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE) === false) {
		stdOut("Cannot create '$archive'.");
		return;
	}

	foreach ($files as $file) {
		if (in_array($file, $configFiles)) {
			$tmp = file_get_contents($file);
			// remove  passwords
			$tmp = preg_replace('~^.*?pass.*~im', '', $tmp);
			$zip->addFromString($file, $tmp);
		} else {
			$zip->addFile($file);
		}
	}
	$zip->addFile(DOUBLECHECK_FILE, DOUBLECHECK_FILE);
	$zip->addFile($report, REPORT_FILE);
	$zip->addFromString($infoFile, $info);
	$zip->close();

	stdOut("\nCreate archive '" . realpath($archive) . "'.");
	stdOut("This archive has no password!");
}



///////////////////////////////////////////////////////////////////////////
function QCR_IntegrityCheck($l_RootDir)
{
	global $g_Structure, $g_Counter, $g_Doorway, $g_FoundTotalFiles, $g_FoundTotalDirs, 
			$defaults, $g_SkippedFolders, $g_UrlIgnoreList, $g_DirIgnoreList, $g_UnsafeDirArray, 
                        $g_UnsafeFilesFound, $g_SymLinks, $g_HiddenFiles, $g_UnixExec, $g_IgnoredExt, $g_SuspiciousFiles, $l_SkipSample;
	global $g_IntegrityDB, $g_ICheck;
	static $l_Buffer = '';
	
	$l_DirCounter = 0;
	$l_DoorwayFilesCounter = 0;
	$l_SourceDirIndex = $g_Counter - 1;
	
	QCR_Debug('Check ' . $l_RootDir);

 	if ($l_DIRH = @opendir($l_RootDir))
	{
		while (($l_FileName = readdir($l_DIRH)) !== false)
		{
			if ($l_FileName == '.' || $l_FileName == '..') continue;

			$l_FileName = $l_RootDir . DIR_SEPARATOR . $l_FileName;

			$l_Type = filetype($l_FileName);
			$l_IsDir = ($l_Type == "dir");
            if ($l_Type == "link") 
            {
				$g_SymLinks[] = $l_FileName;
                continue;
            } else 
			if ($l_Type != "file" && (!$l_IsDir)) {
				$g_UnixExec[] = $l_FileName;
				continue;
			}	
						
			$l_Ext = substr($l_FileName, strrpos($l_FileName, '.') + 1);

			$l_NeedToScan = true;
			$l_Ext2 = substr(strstr(basename($l_FileName), '.'), 1);
			if (in_array(strtolower($l_Ext2), $g_IgnoredExt)) {
                           $l_NeedToScan = false;
            		}

      			// if folder in ignore list
      			$l_Skip = false;
      			for ($dr = 0; $dr < count($g_DirIgnoreList); $dr++) {
      				if (($g_DirIgnoreList[$dr] != '') &&
      				   preg_match('#' . $g_DirIgnoreList[$dr] . '#', $l_FileName, $l_Found)) {
      				   if (!in_array($g_DirIgnoreList[$dr], $l_SkipSample)) {
                                      $l_SkipSample[] = $g_DirIgnoreList[$dr];
                                   } else {
        		             $l_Skip = true;
                                     $l_NeedToScan = false;
                                   }
      				}
      			}
      					
			if (getRelativePath($l_FileName) == "./" . INTEGRITY_DB_FILE) $l_NeedToScan = false;

			if ($l_IsDir)
			{
				// skip on ignore
				if ($l_Skip) {
				   $g_SkippedFolders[] = $l_FileName;
				   continue;
				}
				
				$l_BaseName = basename($l_FileName);

				$l_DirCounter++;

				$g_Counter++;
				$g_FoundTotalDirs++;

				QCR_IntegrityCheck($l_FileName);

			} else
			{
				if ($l_NeedToScan)
				{
					$g_FoundTotalFiles++;
					$g_Counter++;
				}
			}
			
			if (!$l_NeedToScan) continue;

			if (IMAKE) {
				write_integrity_db_file($l_FileName);
				continue;
			}

			// ICHECK
			// skip if known and not modified.
			if (icheck($l_FileName)) continue;
			
			$l_Buffer .= getRelativePath($l_FileName);
			$l_Buffer .= $l_IsDir ? DIR_SEPARATOR . "\n" : "\n";

			if (strlen($l_Buffer) > 32000)
			{
				file_put_contents(QUEUE_FILENAME, $l_Buffer, FILE_APPEND) or die2("Cannot write to file " . QUEUE_FILENAME);
				$l_Buffer = '';
			}

		}

		closedir($l_DIRH);
	}
	
	if (($l_RootDir == ROOT_PATH) && !empty($l_Buffer)) {
		file_put_contents(QUEUE_FILENAME, $l_Buffer, FILE_APPEND) or die2("Cannot write to file " . QUEUE_FILENAME);
		$l_Buffer = '';
	}

	if (($l_RootDir == ROOT_PATH)) {
		write_integrity_db_file();
	}

}


function getRelativePath($l_FileName) {
	return "./" . substr($l_FileName, strlen(ROOT_PATH) + 1) . (is_dir($l_FileName) ? DIR_SEPARATOR : '');
}
/**
 *
 * @return true if known and not modified
 */
function icheck($l_FileName) {
	global $g_IntegrityDB, $g_ICheck;
	static $l_Buffer = '';
	static $l_status = array( 'modified' => 'modified', 'added' => 'added' );
    
	$l_RelativePath = getRelativePath($l_FileName);
	$l_known = isset($g_IntegrityDB[$l_RelativePath]);

	if (is_dir($l_FileName)) {
		if ( $l_known ) {
			unset($g_IntegrityDB[$l_RelativePath]);
		} else {
			$g_IntegrityDB[$l_RelativePath] =& $l_status['added'];
		}
		return $l_known;
	}

	if ($l_known == false) {
		$g_IntegrityDB[$l_RelativePath] =& $l_status['added'];
		return false;
	}

	$hash = is_file($l_FileName) ? hash_file('sha1', $l_FileName) : '';
	
	if ($g_IntegrityDB[$l_RelativePath] != $hash) {
		$g_IntegrityDB[$l_RelativePath] =& $l_status['modified'];
		return false;
	}

	unset($g_IntegrityDB[$l_RelativePath]);
	return true;
}

function write_integrity_db_file($l_FileName = '') {
	static $l_Buffer = '';

	if (empty($l_FileName)) {
		empty($l_Buffer) or file_put_contents('compress.zlib://' . INTEGRITY_DB_FILE, $l_Buffer, FILE_APPEND) or die2("Cannot write to file " . INTEGRITY_DB_FILE);
		$l_Buffer = '';
		return;
	}

	$l_RelativePath = getRelativePath($l_FileName);
		
	$hash = is_file($l_FileName) ? hash_file('sha1', $l_FileName) : '';

	$l_Buffer .= "$l_RelativePath|$hash\n";
	
	if (strlen($l_Buffer) > 32000)
	{
		file_put_contents('compress.zlib://' . INTEGRITY_DB_FILE, $l_Buffer, FILE_APPEND) or die2("Cannot write to file " . INTEGRITY_DB_FILE);
		$l_Buffer = '';
	}
}

function load_integrity_db() {
	global $g_IntegrityDB;
	file_exists(INTEGRITY_DB_FILE) or die2('Not found ' . INTEGRITY_DB_FILE);

	$s_file = new SplFileObject('compress.zlib://'.INTEGRITY_DB_FILE);
	$s_file->setFlags(SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);

	foreach ($s_file as $line) {
		$i = strrpos($line, '|');
		if (!$i) continue;
		$g_IntegrityDB[substr($line, 0, $i)] = substr($line, $i+1);
	}

	$s_file = null;
}


function OptimizeSignatures()
{
	global $g_DBShe, $g_FlexDBShe, $gX_FlexDBShe, $gXX_FlexDBShe;
	global $g_JSVirSig, $gX_JSVirSig;
	global $g_AdwareSig;
	global $g_PhishingSig;
	global $g_ExceptFlex, $g_SusDBPrio, $g_SusDB;

	(AI_EXPERT == 2) && ($g_FlexDBShe = array_merge($g_FlexDBShe, $gX_FlexDBShe, $gXX_FlexDBShe));
	(AI_EXPERT == 1) && ($g_FlexDBShe = array_merge($g_FlexDBShe, $gX_FlexDBShe));
	$gX_FlexDBShe = $gXX_FlexDBShe = array();

	(AI_EXPERT == 2) && ($g_JSVirSig = array_merge($g_JSVirSig, $gX_JSVirSig));
	$gX_JSVirSig = array();

	$count = count($g_FlexDBShe);

	for ($i = 0; $i < $count; $i++) {
		if ($g_FlexDBShe[$i] == '[a-zA-Z0-9_]+?\(\s*[a-zA-Z0-9_]+?=\s*\)') $g_FlexDBShe[$i] = '\((?<=[a-zA-Z0-9_].)\s*[a-zA-Z0-9_]++=\s*\)';
		if ($g_FlexDBShe[$i] == '([^\?\s])\({0,1}\.[\+\*]\){0,1}\2[a-z]*e') $g_FlexDBShe[$i] = '(?J)\.[+*](?<=(?<d>[^\?\s])\(..|(?<d>[^\?\s])..)\)?\g{d}[a-z]*e';
		if ($g_FlexDBShe[$i] == '$[a-zA-Z0-9_]\{\d+\}\s*\.$[a-zA-Z0-9_]\{\d+\}\s*\.$[a-zA-Z0-9_]\{\d+\}\s*\.') $g_FlexDBShe[$i] = '\$[a-zA-Z0-9_]\{\d+\}\s*\.\$[a-zA-Z0-9_]\{\d+\}\s*\.\$[a-zA-Z0-9_]\{\d+\}\s*\.';

		$g_FlexDBShe[$i] = str_replace('http://.+?/.+?\.php\?a', 'http://[^?\s]++(?<=\.php)\?a', $g_FlexDBShe[$i]);
		$g_FlexDBShe[$i] = preg_replace('~\[a-zA-Z0-9_\]\+\K\?~', '+', $g_FlexDBShe[$i]);
		$g_FlexDBShe[$i] = preg_replace('~^\\\\[d]\+&@~', '&@(?<=\d..)', $g_FlexDBShe[$i]);
		$g_FlexDBShe[$i] = str_replace('\s*[\'"]{0,1}.+?[\'"]{0,1}\s*', '.+?', $g_FlexDBShe[$i]);
		$g_FlexDBShe[$i] = str_replace('[\'"]{0,1}.+?[\'"]{0,1}', '.+?', $g_FlexDBShe[$i]);

		$g_FlexDBShe[$i] = preg_replace('~^\[\'"\]\{0,1\}\.?|^@\*|^\\\\s\*~', '', $g_FlexDBShe[$i]);
		$g_FlexDBShe[$i] = preg_replace('~^\[\'"\]\{0,1\}\.?|^@\*|^\\\\s\*~', '', $g_FlexDBShe[$i]);
	}

	optSig($g_FlexDBShe);

	optSig($g_JSVirSig);
	optSig($g_AdwareSig);
	optSig($g_PhishingSig);
        optSig($g_SusDB);
        //optSig($g_SusDBPrio);
        //optSig($g_ExceptFlex);

        // convert exception rules
        $cnt = count($g_ExceptFlex);
        for ($i = 0; $i < $cnt; $i++) {                		
            $g_ExceptFlex[$i] = trim(UnwrapObfu($g_ExceptFlex[$i]));
            if (!strlen($g_ExceptFlex[$i])) unset($g_ExceptFlex[$i]);
        }

        $g_ExceptFlex = array_values($g_ExceptFlex);
}

function optSig(&$sigs)
{
	$sigs = array_unique($sigs);

	// Add SigId
	foreach ($sigs as &$s) {
		$s .= '(?<X' . myCheckSum($s) . '>)';
	}
	unset($s);
	
	$fix = array(
		'([^\?\s])\({0,1}\.[\+\*]\){0,1}\2[a-z]*e' => '(?J)\.[+*](?<=(?<d>[^\?\s])\(..|(?<d>[^\?\s])..)\)?\g{d}[a-z]*e',
		'http://.+?/.+?\.php\?a' => 'http://[^?\s]++(?<=\.php)\?a',
		'\s*[\'"]{0,1}.+?[\'"]{0,1}\s*' => '.+?',
		'[\'"]{0,1}.+?[\'"]{0,1}' => '.+?'
	);

	$sigs = str_replace(array_keys($fix), array_values($fix), $sigs);
	
	$fix = array(
		'~^\\\\[d]\+&@~' => '&@(?<=\d..)',
		'~^((\[\'"\]|\\\\s|@)(\{0,1\}\.?|[?*]))+~' => ''
	);

	$sigs = preg_replace(array_keys($fix), array_values($fix), $sigs);

	optSigCheck($sigs);

	$tmp = array();
	foreach ($sigs as $i => $s) {
		if (!preg_match('#^(?>(?!\.[*+]|\\\\\d)(?:\\\\.|\[.+?\]|.))+$#', $s)) {
			unset($sigs[$i]);
			$tmp[] = $s;
		}
	}
	
	usort($sigs, 'strcasecmp');
	$txt = implode("\n", $sigs);

	for ($i = 24; $i >= 1; ($i > 4 ) ? $i -= 4 : --$i) {
	    $txt = preg_replace_callback('#^((?>(?:\\\\.|\\[.+?\\]|[^(\n]|\((?:\\\\.|[^)(\n])++\))(?:[*?+]\+?|\{\d+(?:,\d*)?\}[+?]?|)){' . $i . ',})[^\n]*+(?:\\n\\1(?![{?*+]).+)+#im', 'optMergePrefixes', $txt);
	}

	$sigs = array_merge(explode("\n", $txt), $tmp);
	
	optSigCheck($sigs);
}

function optMergePrefixes($m)
{
	$limit = 8000;
	
	$prefix = $m[1];
	$prefix_len = strlen($prefix);

	$len = $prefix_len;
	$r = array();

	$suffixes = array();
	foreach (explode("\n", $m[0]) as $line) {
	
	  if (strlen($line)>$limit) {
	    $r[] = $line;
	    continue;
	  }
	
	  $s = substr($line, $prefix_len);
	  $len += strlen($s);
	  if ($len > $limit) {
	    if (count($suffixes) == 1) {
	      $r[] = $prefix . $suffixes[0];
	    } else {
	      $r[] = $prefix . '(?:' . implode('|', $suffixes) . ')';
	    }
	    $suffixes = array();
	    $len = $prefix_len + strlen($s);
	  }
	  $suffixes[] = $s;
	}

	if (!empty($suffixes)) {
	  if (count($suffixes) == 1) {
	    $r[] = $prefix . $suffixes[0];
	  } else {
	    $r[] = $prefix . '(?:' . implode('|', $suffixes) . ')';
	  }
	}
	
	return implode("\n", $r);
}

function optMergePrefixes_Old($m)
{
	$prefix = $m[1];
	$prefix_len = strlen($prefix);

	$suffixes = array();
	foreach (explode("\n", $m[0]) as $line) {
	  $suffixes[] = substr($line, $prefix_len);
	}

	return $prefix . '(?:' . implode('|', $suffixes) . ')';
}

/*
 * Checking errors in pattern
 */
function optSigCheck(&$sigs)
{
	$result = true;

	foreach ($sigs as $k => $sig) {
                if (trim($sig) == "") {
                   if (DEBUG_MODE) {
                      echo("************>>>>> EMPTY\n     pattern: " . $sig . "\n");
                   }
	           unset($sigs[$k]);
		   $result = false;
                }

		if (@preg_match('#' . $sig . '#smiS', '') === false) {
			$error = error_get_last();
                        if (DEBUG_MODE) {
			   echo("************>>>>> " . $error['message'] . "\n     pattern: " . $sig . "\n");
                        }
			unset($sigs[$k]);
			$result = false;
		}
	}
	
	return $result;
}

function _hash_($text)
{
	static $r;
	
	if (empty($r)) {
		for ($i = 0; $i < 256; $i++) {
			if ($i < 33 OR $i > 127 ) $r[chr($i)] = '';
		}
	}

	return sha1(strtr($text, $r));
}

function check_whitelist($list, &$snum) 
{
	if (empty($list)) return array();
	
	$file = dirname(__FILE__) . '/AIBOLIT-WHITELIST.db';

	$snum = max(0, @filesize($file) - 1024) / 20;
	stdOut("\nLoaded " . ceil($snum) . " known files\n");
	
	sort($list);

	$hash = reset($list);
	
	$fp = @fopen($file, 'rb');
	
	if (false === $fp) return array();
	
	$header = unpack('V256', fread($fp, 1024));
	
	$result = array();
	
	foreach ($header as $chunk_id => $chunk_size) {
		if ($chunk_size > 0) {
			$str = fread($fp, $chunk_size);
			
			do {
				$raw = pack("H*", $hash);
				$id = ord($raw[0]) + 1;
				
				if ($chunk_id == $id AND binarySearch($str, $raw)) {
					$result[] = $hash;
				}
				
			} while ($chunk_id >= $id AND $hash = next($list));
			
			if ($hash === false) break;
		}
	}
	
	fclose($fp);

	return $result;
}


function binarySearch($str, $item)
{
	$item_size = strlen($item);	
	if ( $item_size == 0 ) return false;
	
	$first = 0;

	$last = floor(strlen($str) / $item_size);
	
	while ($first < $last) {
		$mid = $first + (($last - $first) >> 1);
		$b = substr($str, $mid * $item_size, $item_size);
		if (strcmp($item, $b) <= 0)
			$last = $mid;
		else
			$first = $mid + 1;
	}

	$b = substr($str, $last * $item_size, $item_size);
	if ($b == $item) {
		return true;
	} else {
		return false;
	}
}

function getSigId($l_Found)
{
	foreach ($l_Found as $key => &$v) {
		if (is_string($key) AND $v[1] != -1 AND strlen($key) == 9) {
			return substr($key, 1);
		}
	}
	
	return null;
}

function die2($str) {
  if (function_exists('aibolit_onFatalError')) { aibolit_onFatalError($str); }
  die($str);
}

function checkFalsePositives($l_Filename, $l_Unwrapped, $l_DeobfType) {
  global $g_DeMapper;

  if ($l_DeobfType != '') {
     if (DEBUG_MODE) {
       stdOut("\n-----------------------------------------------------------------------------\n");
       stdOut("[DEBUG]" . $l_Filename . "\n");
       var_dump(getFragment($l_Unwrapped, $l_Pos));
       stdOut("\n...... $l_DeobfType ...........\n");
       var_dump($l_Unwrapped);
       stdOut("\n");
     }

     switch ($l_DeobfType) {
        case '_GLOBALS_': 
           foreach ($g_DeMapper as $fkey => $fvalue) {
              if (DEBUG_MODE) {
                 stdOut("[$fkey] => [$fvalue]\n");
              }

              if ((strpos($l_Filename, $fkey) !== false) &&
                  (strpos($l_Unwrapped, $fvalue) !== false)) {
                 if (DEBUG_MODE) {
                    stdOut("\n[DEBUG] *** SKIP: False Positive\n");
                 } 

                 return true;
              }
           }
        break;
     }


     return false;
  }
}

function deobfuscate_bitrix($str)
{
	global $varname,$funclist,$strlist;
	$res = $str;
	$funclist = array();
	$strlist = array();
	$res = preg_replace("|'\s*\.\s*'|smi", '', $res);
	$res = preg_replace_callback(
		'|(round\((.+?)\))|smi',
		function ($matches) {
		   return round($matches[2]);
		},
		$res
	);
	$res = preg_replace_callback(
			'|base64_decode\(\'(.*?)\'\)|smi',
			function ($matches) {
				return "'" . base64_decode($matches[1]) . "'";
			},
			$res
	);

	$res = preg_replace_callback(
			'|\'(.*?)\'|sm',
			function ($matches) {
				$temp = base64_decode($matches[1]);
				if (base64_encode($temp) === $matches[1] && preg_match('#^[ -~]*$#', $temp)) { 
				   return "'" . $temp . "'";
				} else {
				   return "'" . $matches[1] . "'";
				}
			},
			$res
	);	

	if (preg_match_all('|\$GLOBALS\[\'(.+?)\'\]\s*=\s*Array\((.+?)\);|smi', $res, $founds, PREG_SET_ORDER)) {
   	foreach($founds as $found)
   	{
   		$varname = $found[1];
   		$funclist[$varname] = explode(',', $found[2]);
   		$funclist[$varname] = array_map(function($value) { return trim($value, "'"); }, $funclist[$varname]);

   		$res = preg_replace_callback(
   				'|\$GLOBALS\[\'' . $varname . '\'\]\[(\d+)\]|smi',
   				function ($matches) {
   				   global $varname, $funclist;
   				   return $funclist[$varname][$matches[1]];
   				},
   				$res
   		);
   		
     	        $res = preg_replace('~' . quotemeta(str_replace('~', '.', $found[0])) . '~smi', '', $res);
   	}
        }
		

	if (preg_match_all('|function _+(\d+)\(\$i\){\$a=Array\((.+?)\);[^}]+}|smi', $res, $founds, PREG_SET_ORDER)) {
	foreach($founds as $found)
	{
		$strlist = explode(',', $found[2]);

		$res = preg_replace_callback(
				'|_' . $found[1] . '\((\d+)\)|smi',
				function ($matches) {
				   global $strlist;
				   return $strlist[$matches[1]];
				},
				$res
		);

  	        $res = preg_replace('~' . quotemeta(str_replace('~', '\\~', $found[0])) . '~smi', '', $res);
	}
        }

  	$res = preg_replace('~<\?(php)?\s*\?>~smi', '', $res);

	preg_match_all('~function (_+(.+?))\(\$[_0-9]+\)\{\s*static\s*\$([_0-9]+)\s*=\s*(true|false);.*?\$\3=array\((.*?)\);\s*return\s*base64_decode\(\$\3~smi', $res, $founds,PREG_SET_ORDER);
	foreach($founds as $found)
	{
		$strlist = explode("',",$found[5]);
		$res = preg_replace_callback(
				'|' . $found[1] . '\((\d+)\)|sm',
				function ($matches) {
				   global $strlist;
				   return $strlist[$matches[1]]."'";
				},
				$res
		);
				
	}

	$res = preg_replace('|;|sm', ";\n", $res);

	return $res;
}

function my_eval($matches)
{
    $string = $matches[0];
    $string = substr($string, 5, strlen($string) - 7);
    return decode($string);
}

function decode($string, $level = 0)
{
    if (trim($string) == '') return '';
    if ($level > 100) return '';

    if (($string[0] == '\'') || ($string[0] == '"')) {
        return substr($string, 1, strlen($string) - 2); //
	} elseif ($string[0] == '$') {
        return $string; //
    } else {
        $pos      = strpos($string, '(');
        $function = substr($string, 0, $pos);
		
        $arg      = decode(substr($string, $pos + 1), $level + 1);
    	if ($function == 'base64_decode') return @base64_decode($arg);
    	else if ($function == 'gzinflate') return @gzinflate($arg);
		else if ($function == 'gzuncompress') return @gzuncompress($arg);
    	else if ($function == 'strrev')  return @strrev($arg);
    	else if ($function == 'str_rot13')  return @str_rot13($arg);
    	else return $arg;
    }    
}
    
function deobfuscate_eval($str)
{
    $res = preg_replace_callback('~eval\((base64_decode|gzinflate|strrev|str_rot13|gzuncompress).*?\);~ms', "my_eval", $str);
    return $res;
}

function getEvalCode($string)
{
    preg_match("/eval\((.*?)\);/", $string, $matches);
    return (empty($matches)) ? '' : end($matches);
}
function getTextInsideQuotes($string)
{
    preg_match('/("(.*?)")/', $string, $matches);
    return (empty($matches)) ? '' : end($matches);
}

function deobfuscate_lockit($str)
{    
    $obfPHP        = $str;
    $phpcode       = base64_decode(getTextInsideQuotes(getEvalCode($obfPHP)));
    $hexvalues     = getHexValues($phpcode);
    $tmp_point     = getHexValues($obfPHP);
    $pointer1      = hexdec($tmp_point[0]);
    $pointer2      = hexdec($hexvalues[0]);
    $pointer3      = hexdec($hexvalues[1]);
    $needles       = getNeedles($phpcode);
    $needle        = $needles[count($needles) - 2];
    $before_needle = end($needles);
    
    
    $phpcode = base64_decode(strtr(substr($obfPHP, $pointer2 + $pointer3, $pointer1), $needle, $before_needle));
    return "<?php {$phpcode} ?>";
}


    function getNeedles($string)
    {
        preg_match_all("/'(.*?)'/", $string, $matches);
        
        return (empty($matches)) ? array() : $matches[1];
    }
    function getHexValues($string)
    {
        preg_match_all('/0x[a-fA-F0-9]{1,8}/', $string, $matches);
        return (empty($matches)) ? array() : $matches[0];
    }

function deobfuscate_als($str)
{
	preg_match('~__FILE__;\$[O0]+=[0-9a-fx]+;eval\(\$[O0]+\(\'([^\']+)\'\)\);return;~msi',$str,$layer1);

	preg_match('~\$[O0]+=(\$[O0]+\()+\$[O0]+,[0-9a-fx]+\),\'([^\']+)\',\'([^\']+)\'\)\);eval\(~msi',base64_decode($layer1[1]),$layer2);
    $res = explode("?>", $str);
	if (strlen($res[1])>0)
	{
		$res = substr($res[1], 380);
		$res = base64_decode(strtr($res, $layer2[2], $layer2[3]));
	}
    return "<?php {$res} ?>";
}

function deobfuscate_byterun($str)
{
	preg_match('~\$_F=__FILE__;\$_X=\'([^\']+)\';eval\(~ms',$str,$matches);
	$res = base64_decode($matches[1]);
	$res = strtr($res,'123456aouie','aouie123456');
    return "<?php {$res} ?>";
}

function deobfuscate_urldecode($str)
{
	preg_match('~(\$[O0_]+)=urldecode\("([%0-9a-f]+)"\);((\$[O0_]+=(\1\{\d+\}\.?)+;)+)~msi',$str,$matches);
	$alph = urldecode($matches[2]);
	$funcs=$matches[3];
	for($i = 0; $i < strlen($alph); $i++)
	{
		$funcs = str_replace($matches[1].'{'.$i.'}.',$alph[$i],$funcs);
		$funcs = str_replace($matches[1].'{'.$i.'}',$alph[$i],$funcs);
	}

	$str = str_replace($matches[3], $funcs, $str);
	$funcs = explode(';', $funcs);
	foreach($funcs as $func)
	{
		$func_arr = explode("=", $func);
		if (count($func_arr) == 2)
		{
			$func_arr[0] = str_replace('$', '', $func_arr[0]);
			$str = str_replace('${"GLOBALS"}["' . $func_arr[0] . '"]', $func_arr[1], $str);
		}			
	}

	return $str;
}


function formatPHP($string)
{
    $string = str_replace('<?php', '', $string);
    $string = str_replace('?>', '', $string);
    $string = str_replace(PHP_EOL, "", $string);
    $string = str_replace(";", ";\n", $string);
    return $string;
}

function deobfuscate_fopo($str)
{
    $phpcode = formatPHP($str);
    $phpcode = base64_decode(getTextInsideQuotes(getEvalCode($phpcode)));
    @$phpcode = gzinflate(base64_decode(str_rot13(getTextInsideQuotes(end(explode(':', $phpcode))))));
    $old = '';
    while (($old != $phpcode) && (strlen(strstr($phpcode, '@eval($')) > 0)) {
        $old = $phpcode;
        $funcs = explode(';', $phpcode);
		if (count($funcs) == 5) $phpcode = gzinflate(base64_decode(str_rot13(getTextInsideQuotes(getEvalCode($phpcode)))));
		else if (count($funcs) == 4) $phpcode = gzinflate(base64_decode(getTextInsideQuotes(getEvalCode($phpcode))));
    }
    
    return substr($phpcode, 2);
}

function getObfuscateType($str)
{
if (preg_match('~eval\((base64_decode|gzinflate|strrev|str_rot13|gzuncompress)~ms', $str))
        return "eval";
    if (preg_match('~\$GLOBALS\[\'_+\d+\'\]=\s*array\(base64_decode\(~msi', $str))
        return "_GLOBALS_";
    if (preg_match('~function _+\d+\(\$i\){\$a=Array~msi', $str))
        return "_GLOBALS_";
    if (preg_match('~__FILE__;\$[O0]+=[0-9a-fx]+;eval\(\$[O0]+\(\'([^\']+)\'\)\);return;~msi', $str))
        return "ALS-Fullsite";
    if (preg_match('~\$[O0]*=urldecode\(\'%66%67%36%73%62%65%68%70%72%61%34%63%6f%5f%74%6e%64\'\);\s*\$GLOBALS\[\'[O0]*\'\]=\$[O0]*~msi', $str))
        return "LockIt!";
    if (preg_match('~\$\w+="(\\\x?[0-9a-f]+){13}";@eval\(\$\w+\(~msi', $str))
        return "FOPO";
	if (preg_match('~\$_F=__FILE__;\$_X=\'([^\']+\');eval\(~ms', $str))
        return "ByteRun";
    if (preg_match('~(\$[O0_]+)=urldecode\("([%0-9a-f]+)"\);((\$[O0_]+=(\1\{\d+\}\.?)+;)+)~msi', $str))
        return "urldecode_globals";
	
}

function deobfuscate($str)
{
    switch (getObfuscateType($str)) {
        case '_GLOBALS_':
            $str = deobfuscate_bitrix($str);
            break;
        case 'eval':
            $str = deobfuscate_eval($str);
            break;
        case 'ALS-Fullsite':
            $str = deobfuscate_als($str);
            break;
        case 'LockIt!':
            $str = deobfuscate_lockit($str);
            break;
        case 'FOPO':
            $str = deobfuscate_fopo($str);
            break;
	case 'ByteRun':
            $str = deobfuscate_byterun($str);
            break;
	case 'urldecode_globals' :
            $str = deobfuscate_urldecode($str);
	    break;
    }
    
    return $str;
}
