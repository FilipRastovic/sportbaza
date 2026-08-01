<?php
// Sport Baza – lead form handler
// Validates the submission, emails the lead, and redirects to a thank-you
// page (where the Meta Pixel "Lead" event fires) or back with an error.

$leadRecipient = "igorpajovic86@gmail.com";

function redirect($url) {
    header("Location: $url");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.html');
}

// Honeypot: bots fill every field, real users never see/fill this one.
if (!empty($_POST['website'])) {
    redirect('thank-you.html');
}

function field($key) {
    return isset($_POST[$key]) ? trim($_POST[$key]) : '';
}

$name    = field('Ime_i_Prezime');
$phone   = field('Broj_Telefona');
$sport   = field('Interesovanje_Sport');
$kvart   = field('Kvart_Novi_Sad');
$consent = field('consent');

if ($name === '' || $phone === '' || $sport === '' || $consent === '') {
    redirect('index.html?error=1');
}

// Strip anything that could be used for header injection.
$clean = function ($value) {
    return str_replace(["\r", "\n"], '', $value);
};

$name  = $clean($name);
$phone = $clean($phone);
$sport = $clean($sport);
$kvart = $clean($kvart) ?: 'Nije navedeno';

$subject = "Novi Lead - Sport Baza: $name";

$body = "Novi lead sa Sport Baza landing stranice:\n\n"
      . "Ime i prezime: $name\n"
      . "Telefon: $phone\n"
      . "Interesovanje: $sport\n"
      . "Deo grada: $kvart\n"
      . "Vreme: " . date('Y-m-d H:i:s') . "\n";

$host = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'sportbaza.rs';
$fromAddress = "noreply@$host";

$headers  = "From: Sport Baza <$fromAddress>\r\n";
$headers .= "Reply-To: $fromAddress\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

$sent = mail($leadRecipient, $subject, $body, $headers);

if ($sent) {
    redirect('thank-you.html');
} else {
    redirect('index.html?error=1');
}
