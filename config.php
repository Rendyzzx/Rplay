<?php
// Connect to database (MySql)
$hostname = 'db4free.net';
$username = 'rendy0';
$password = 'a1s2d3f4g5';
$dbname = 'playbosku';

// SMTP
$SMTPHostname = 'smtp.zoho.com';
$SMTPUsername = 'noreply@danitechid.com';
$SMTPPassword = 'gT1kKg1nUtny';
$SMTPPort = '587';
$SMTPAuth = true;
$SMTPSecure = 'tls';
$SMTPOptions = array(
  'ssl' => array(
    'verify_peer' => false,
    'verify_peer_name' => false,
    'allow_self_signed' => true
  )
);
$SMTPSetFrom = 'NoReply - Rplay';

// Web Settings
$fileName = true; // 'true' to add '.php' in URL, and 'false' to remove '.php' in URL
$displayForAds = 'none'; // 'block' to enable ads area, and 'none' to disable ads area
$verifiedAccount = ['RendyZzx', 'NanaXD'];
$ceoAccount = 'RendyZzx';
$title = 'Rplay';
$description = 'Rplay adalah platform inovatif berbagi video dengan insentif blockchain, memungkinkan pengguna menikmati konten berkualitas tinggi dalam lingkungan ramah dan interaktif.';
$keywords = 'Rplay, Platform Berbagi Video, Video Berkualitas Tinggi, Berbagi Video Cepat, Antarmuka Intuitif, Insentif Blockchain, Privasi Konten, Hak Cipta Video, Fitur Sosial, Interaksi Pengguna, Reward Pembuat Konten, Token Platform, Kreativitas Pengguna, Inspirasi Video, Komunitas Pengguna, Konten Multibahasa, Media Sosial, Pengalaman Menonton Video, Lingkungan Ramah, Konten Bermanfaat';
$customerServiceURL = 'https://support.yourdomain.com';
$customerServiceEmail = 'mailto:support@Rendyzzx.com';
$customerServiceWhatsAppPhoneNumber = '6281249578370';
$donateURL = 'https://trakteer.id/Rendyzzx';

// Content Delivery Network (CDN)
$tailwindCSS = 'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css';
$fontAwesome = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css';
$fontAwesomeBeta = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css';
$jQuery = 'https://code.jquery.com/jquery-3.6.0.min.js';
?>
