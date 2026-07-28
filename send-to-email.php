<?php
// --- Настройки ---
$to = 'hello@smirnov-law.ru'; // адрес, куда получать письма

// --- Получаем тип формы ---
$formType = $_POST['form_type'] ?? 'unknown';

// --- Общие поля ---
$name    = trim($_POST['name']    ?? '');
$contact = trim($_POST['contact'] ?? '');

// Дополнительные поля
$topic   = trim($_POST['topic']   ?? '');
$message = trim($_POST['message'] ?? '');
$tariff  = trim($_POST['tariff']  ?? '');
$consent = isset($_POST['consent']) ? 'Да' : 'Нет';

// --- Простая проверка обязательных полей ---
if ($name === '' || $contact === '') {
    header('Location: form-error.html');
    exit;
}

// --- Формируем тему письма ---
switch ($formType) {
    case 'request':
        $subject = 'Заявка на консультацию с сайта Смирнова';
        break;
    case 'order':
        $subject = 'Заказ консультации по прайс-листу с сайта Смирнова';
        break;
    default:
        $subject = 'Обращение с сайта Смирнова';
        break;
}

// --- Формируем тело письма ---
$lines   = [];
$lines[] = "Тип формы: {$formType}";
$lines[] = "";
$lines[] = "Имя: {$name}";
$lines[] = "Контакт: {$contact}";

if ($formType === 'request') {
    $lines[] = "Тема обращения: " . ($topic !== '' ? $topic : '(не указана)');
    $lines[] = "";
    $lines[] = "Описание ситуации:";
    $lines[] = $message !== '' ? $message : '(не заполнено)';
    $lines[] = "";
    $lines[] = "Согласие на обработку ПД: {$consent}";
}

if ($formType === 'order') {
    $lines[] = "Выбранный тариф: " . ($tariff !== '' ? $tariff : '(не выбран)');
}

$lines[] = "";
$lines[] = "Источник: pointofyou.xsph.ru";

$body = implode("\n", $lines);

// --- Заголовки письма ---
$headers   = [];
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-type: text/plain; charset=utf-8';
$headers[] = 'From: Сайт Смирнова <no-reply@smirnov-law.ru>';

$headersString = implode("\r\n", $headers);

// --- Отправка ---
$mailSent = mail($to, $subject, $body, $headersString);

// --- Редирект ---
if ($mailSent) {
    header('Location: thank-you.html');
} else {
    header('Location: form-error.html');
}
exit;
