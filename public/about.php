<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/render.php';
require_once __DIR__ . '/../lib/analytics.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/nav.php';
require_once __DIR__ . '/../includes/footer.php';

$pdo = get_pdo();
$user = current_user();
track_visit($pdo, '/about.php');

echo render_header(
    'Despre acest proiect',
    'Tema, rolurile, arhitectura și structura bazei de date pentru aplicația Hipermarket.'
);
echo render_nav($user);
echo '<h1>Despre acest proiect</h1>';

echo '<h2>Tema</h2>';
echo '<p>Activitățile unui hipermarket — proiect pentru cursul DAW, realizat în '
    . 'PHP și MySQL. Clienții răsfoiesc catalogul de produse și lasă recenzii; '
    . 'angajații gestionează produsele și categoriile; administratorii gestionează '
    . 'conturile angajaților și vizualizează rapoarte de vânzări/vizite.</p>';

echo '<h2>Roluri</h2>';
echo '<ul>'
    . '<li><strong>Client</strong> — se înregistrează singur, răsfoiește/caută în catalog, '
    . 'vizualizează detaliile produselor și își scrie/editează/șterge propriile recenzii.</li>'
    . '<li><strong>Angajat</strong> — creat de un administrator; gestionează produsele și '
    . 'categoriile (creare/actualizare/ștergere).</li>'
    . '<li><strong>Administrator</strong> — creat de un alt administrator (sau inițializat '
    . 'direct); gestionează conturile angajaților și vizualizează rapoarte exportabile de '
    . 'vânzări/vizite, în plus față de tot ce poate face un angajat.</li>'
    . '</ul>';

echo '<h2>Componente principale</h2>';
echo '<ul>'
    . '<li><code>/public</code> — pagini de intrare: catalog, detalii produs, autentificare/'
    . 'înregistrare, contact și panouri per rol.</li>'
    . '<li><code>/includes</code> — șabloane comune de header/footer/navigare.</li>'
    . '<li><code>/lib</code> — funcții generice pentru baza de date, autentificare, CSRF, '
    . 'CAPTCHA, rapoarte, export PDF/CSV și preluare de conținut extern.</li>'
    . '<li><code>/actions</code> — handlere pentru formulare: autentificare, înregistrare, '
    . 'CRUD produse/categorii/recenzii, CRUD angajați, contact și export rapoarte.</li>'
    . '</ul>';

echo '<h2>Baza de date</h2>';
echo '<p>MySQL, accesată prin PDO cu interogări pregătite. Tabele principale:</p>';
echo '<ul>'
    . '<li><code>users</code> (id, name, email, password_hash, role, created_at)</li>'
    . '<li><code>categories</code> (id, name)</li>'
    . '<li><code>products</code> (id, category_id, name, price, stock_qty, description)</li>'
    . '<li><code>reviews</code> (id, product_id, user_id, rating, comment, created_at)</li>'
    . '<li><code>site_visits</code> (id, page, visited_at)</li>'
    . '</ul>';

echo '<h2>Securitate</h2>';
echo '<p>Autentificare bazată pe sesiune cu hash-uire a parolelor, token CSRF la fiecare '
    . 'formular, escapare a output-ului împotriva XSS, interogări pregătite împotriva '
    . 'SQL injection și CAPTCHA pe formularele publice de contact și înregistrare.</p>';

echo render_footer();
