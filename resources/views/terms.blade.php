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
    <h2>Terms & Conditions</h2>
    <p>Dengan menggunakan layanan Mystic Nusa, pengguna setuju untuk mematuhi ketentuan yang berlaku.</p>
    <p>Mystic Nusa menyediakan layanan berbasis AI dan integrasi media sosial untuk hiburan dan eksplorasi spiritualitas.</p>
    <p>Kami tidak bertanggung jawab atas kerugian yang timbul dari penggunaan konten atau fitur aplikasi.</p>
    <p>Pengguna dilarang menyalahgunakan layanan untuk tujuan ilegal atau merugikan pihak lain.</p>
    <p>Kami dapat memperbarui atau menghentikan layanan sewaktu-waktu tanpa pemberitahuan sebelumnya.</p>
  </section>
@endsection