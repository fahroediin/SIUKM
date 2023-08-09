<?php
// Mendata semua question dlu, dan di sisi server supaya tidak diinspect element data-correctnya
// type a = soal biasa
// type b = soal tabel
$questions =[
   [
        'type' => 'a',
        'correct_answer' => 'c', 
        'prompt' => 'gitar : ... ≈ ... : pukul',
        'options' =>[
            'a' => 'bernyanyi tukang',
            'b' => 'kayu besi',
            'c' => 'petik jimbe',
            'd' => 'musik paku',
            'e' => 'senar gendang'
       ]
   ],
   [
        'type' => 'a',
        'correct_answer' => 'c',
        'prompt' => 'hard disk : ... ≈ ... : uang',
        'options' =>[
            'a' => 'piringan logam',
            'b' => 'data dompet',
            'c' => 'piringan kertas',
            'd' => 'disket barter',
            'e' => 'komputer penghasilan'
       ]
   ],
   [
        'type' => 'a',
        'correct_answer' => 'b',
        'prompt' => '<p>Pilihlah jawaban yang paling tepat berdasarkan fakta atau informasi yang disajikan dalam setiap teks!
        <p>TEKS 1</p> (untuk menjawab soal nomor 28 sampai dengan nomor 31). </p>
        <p>Di suatu pertemuan ada 4 orang pria dewasa, 4 wanita dewasa, dan 4 anak-anak. Keempat pria dewasa itu bernama Santo, Markam,Gunawan, dan Saiful. Keempat wanita dewasaitu bernama Ria, Gina, Dewi, dan Hesti.Keempat anak itu bernama Hadi, Putra, Bobbydan Soleh. Sebenarnya mereka berasal dari 4keluarga yang setiap keluarga terdiri dariseorang ayah, seorang ibu dan satu oranganak, namun tidak diketahui yang mana yangmenjadi ayah, dan mana yang menjadi ibu,dan mana yang menjadi anak dari masing-masing keluarga itu, kecuali beberapa halsebagai berikut:(1) Ibu Ria adalah ibu dari Soleh(2) Pak Santo adalah ayah dari Hadi(3) Pak Saiful adalah suami dari Ibu Dewi,tetapi bukan ayah dari Bobby(4) Pak Gunawan adalah suami Ibu Hesti.</p><p>Putra adalah ....</p>',
        'options' =>[
            'a' => 'Anak dari Pak Markam',
            'b' => 'Anak dari Pak Saiful',
            'c' => 'Anak dari Pak Santo',
            'd' => 'Anak dari Pak Gunawan',
            'e' => 'Anak dari Ibu Ria'
       ]
   ],
   [
        'type' => 'a',
        'correct_answer' => 'd',
        'prompt' => 'Pecahan yang nilainya terletak antara \(\frac{3}{5}\) dan \(\frac{9}{10}\) adalah ....',
        'options' =>[
            'a' => '\(\frac{3}{8}\)',
            'b' => '\(\frac{1}{2}\)',
            'c' => '(\frac{4}{7}\)',
            'd' => '\(\frac{3}{4}\)',
            'e' => '\(\frac{5}{11}\)'
       ]
   ],
   [
        'type' => 'b',
        'correct_answer' => 'd',
        'table_caption' => 'Tabel berikut menunjukan hasil dua kali tes matematika.',
        'table_header' => ['Nama', 'Tes 1', 'Tes 2'],
        'table_content' => [
            ['Ahmad', 80, 75],
            ['Beny', 80, 96],
            ['Citra', 80, 84],
            ['Dinda', 80, 100],
            ['Eka', 80, 90],
        ],
        'prompt' => 'Peserta yang nilainya meningkat 20% pada tes kedua jika dibandingkan tes pertama adalah ....',
        'options' =>[
            'a' => 'Ahmad',
            'b' => 'Beny',
            'c' => 'Citra',
            'd' => 'Dinda',
            'e' => 'Eka'
       ]
   ],
];

?>