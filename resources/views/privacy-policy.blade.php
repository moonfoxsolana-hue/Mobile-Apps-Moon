@extends('layouts.app')

@section('content')
  <style>
    body {
      background: #0f0f1a;
      color: #f0e6d2;
      font-family: 'Segoe UI', sans-serif;
      line-height: 1.6;
      padding: 4rem 2rem;
      max-width: 700px;
      margin: auto;
    }
    h1, h2, h3 {
      color: #d4af37;
      font-size: clamp(1.5rem, 2.5vw, 2.5rem);
    }
    h1 {
      text-align: center;
      margin-bottom: 2rem;
    }
    section {
      margin-bottom: 3rem;
      font-size : clamp(0.8rem, 1.4vw, 1rem);

    }
    a {
      color: #5ddcff;
    }
  </style>

  <h1>Mystic Nusa Indonesia</h1>

  <section>
<div class="privacy-policy-container">
    <h2>Privacy Policy - Mystic Nusa</h2>
    <p>Last Updated: December 23, 2025</p>

    <p>Mystic Nusa ("kami") menghormati privasi pengguna kami. Kebijakan Privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi informasi Anda saat Anda menggunakan layanan kami, termasuk integrasi dengan API pihak ketiga seperti Google dan TikTok.</p>

    <hr>

    <h3>1. Informasi yang Kami Kumpulkan via Google API</h3>
    <p>Saat Anda menggunakan fitur unggah otomatis ke YouTube di Mystic Nusa, kami meminta izin melalui Google OAuth untuk mengakses data berikut:</p>
    <ul>
        <li><strong>Alamat Email:</strong> Untuk mengidentifikasi akun Anda.</li>
        <li><strong>YouTube Scope (youtube.upload):</strong> Untuk mengunggah video yang telah Anda buat di Mystic Nusa secara langsung ke saluran YouTube Anda.</li>
        <li><strong>Profil Publik:</strong> Nama profil untuk personalisasi akun di aplikasi kami.</li>
    </ul>

    <h3>2. Penggunaan Data (Data Usage)</h3>
    <p>Kami menggunakan informasi yang diperoleh melalui Google API secara eksklusif untuk fungsi-fungsi berikut:</p>
    <ul>
        <li>Mengautentikasi pengguna ke aplikasi Mystic Nusa.</li>
        <li>Melakukan proses pengunggahan video (YouTube Upload) atas instruksi dan persetujuan eksplisit dari pengguna.</li>
        <li>Kami <strong>TIDAK</strong> menggunakan data ini untuk tujuan periklanan, pemasaran, atau pelacakan di luar aplikasi kami.</li>
    </ul>

    <div style="background-color: #363131ff; padding: 15px; border-left: 5px solid #4285f4;">
        <h4>Kepatuhan Kebijakan Data Google (Google API Disclosure)</h4>
        <p>Penggunaan informasi yang diterima dari Google API oleh Mystic Nusa akan mematuhi <a href="https://developers.google.com/terms/api-services-user-data-policy" target="_blank">Google API Services User Data Policy</a>, termasuk persyaratan <strong>Limited Use</strong> (Penggunaan Terbatas).</p>
    </div>

    <h3>3. Penyimpanan dan Keamanan Data (Data Storage & Security)</h3>
    <p>Data akses (Access Tokens) disimpan secara aman di server kami menggunakan enkripsi standar industri. Kami hanya menyimpan token selama diperlukan untuk menjalankan fungsi aplikasi. Kami tidak menyimpan salinan data pribadi Anda secara permanen kecuali jika diperlukan untuk operasional akun Anda.</p>

    <h3>4. Berbagi Data dengan Pihak Ketiga (Data Sharing)</h3>
    <p>Mystic Nusa tidak menjual, menyewakan, atau membagikan data pengguna Google Anda kepada pihak ketiga. Data hanya ditransfer ke YouTube (Google) sebagai bagian dari fungsionalitas utama aplikasi yaitu pengunggahan video.</p>

    <h3>5. Penghapusan Data dan Akses (Data Deletion)</h3>
    <p>Anda dapat mencabut akses Mystic Nusa kapan saja melalui halaman <a href="https://myaccount.google.com/permissions" target="_blank">Google Security Settings</a>. Jika Anda ingin menghapus akun Mystic Nusa beserta seluruh data terkait, silakan hubungi kami melalui menu kontak di aplikasi atau email ke: <strong>mysticnusa@gmail.com</strong>.</p>

    <hr>

    <h3>English Version (For Google Verification Purposes)</h3>
    
    <h4>Information Obtained from Google API Services</h4>
    <p><strong>Data Access:</strong> Our application accesses your Google user data (specifically your email address and YouTube account access via the <code>youtube.upload</code> scope) to facilitate video creation and direct uploading to your YouTube channel.</p>
    
    <p><strong>Data Usage:</strong> We use this data strictly to provide and improve the features of Mystic Nusa, specifically for authenticating users and performing video uploads as requested by the user. We do not use this information for advertising or any other purpose not explicitly stated.</p>
    
    <p><strong>Limited Use Disclosure:</strong> Mystic Nusa's use and transfer to any other app of information received from Google APIs will adhere to the <a href="https://developers.google.com/terms/api-services-user-data-policy">Google API Services User Data Policy</a>, including the Limited Use requirements.</p>

    <p><strong>Contact Us:</strong> If you have questions regarding this policy, contact us at: <strong>mysticnusa@gmail.com</strong></p>
</div>
  </section>
@endsection
