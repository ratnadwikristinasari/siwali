<?php

// ponytail: daftar NIM demo yang bypass cek JTIFORM evados. Hapus file ini
// (dan blok bypass di CPerwalian@store) setelah demo selesai.
// Jika pernah jalankan `php artisan config:cache`, jalankan ulang setelah edit file ini.

return [
    'jtiform_bypass_nims' => [
        // TIF 1
        'E41260269', 'E41262993', 'E41262520',
        // TIF 3
        'E41251276', 'E41250074', 'E41251649', 'E41250039',
        // TIF 5
        'E41240137', 'E41241610', 'E41242359', 'E41240238',
        // MIF 1
        'E31260638', 'E31261878', 'E31262797',
        // MIF 3
        'E31251456', 'E31252459', 'E31250836',
        // TKK 1
        'E32261679', 'E32263118', 'E32260026',
        // TKK 3
        'E32251401', 'E32252691', 'E32250692', 'E32250702',
        // TKK 5
        'E32240124',
        // TRK 1
        'E43260541', 'E43261795', 'E43262338',
        // TRK 3
        'E43252017', 'E43253046', 'E43252794', 'E43252280', 'E43253134',
        // TRPL 1
        'E44262340', 'E44263144', 'E44261364',
        // TRPL 3
        'E44253263', 'E44253382', 'E44253234',
        // TIF PSDKU Nganjuk 1
        'E41260799', 'E41250631', 'E41262927',
        // TIF PSDKU Nganjuk 3
        'E41250604', 'E41252590', 'E41253009', 'E41250030',
        // TIF PSDKU Nganjuk 5
        'E41240664', 'E41242093', 'E41241244', 'E41240591',
        // TIF PSDKU SDA 1
        'E41261500', 'E41263153', 'E41262448',
        // TIF PSDKU SDA 3
        'E41253265', 'E41251173', 'E41251541', 'E41250581',
        // TIF PSDKU SDA 5
        'E41240461', 'E41241910', 'E41242430', 'E41241205',
    ],
];
