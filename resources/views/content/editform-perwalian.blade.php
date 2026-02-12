@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Perwalian')

{{-- @include('_partials.alert') --}}

@section('content')
    <div class="row g-4">
        <div class="col-12 col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="col-xl-6">
                        <h5>Kartu Hasil Studi</h5>
                    </div>
                    @if ($perwalian->khs)
                        <div class="card mt-3">
                            <div class="card-body p-0">
                                <div class="card mt-3">
                                    <div class="card-body p-2">
                                        <div id="pdf-wrapper" style="position:relative; width:100%; overflow:auto;">

                                            <!-- PDF -->
                                            <canvas id="pdf-canvas" style="width:100%;"></canvas>

                                            <!-- PNG Signature -->
                                            <img id="signature" src="{{ $eSign['data']['qr_code_base64'] }}"
                                                class="signature-box"
                                                style="
                                                position:absolute;
                                                top:20px;
                                                left:20px;
                                                width:120px;
                                                max-width:75px;
                                                min-width:75px;
                                                cursor:move;
                                                z-index:10;
                                            ">
                                        </div>

                                        <button id="save-position" class="btn btn-sm btn-primary mt-2">
                                            Simpan Posisi TTD
                                        </button>
                                    </div>
                                </div>


                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning mt-3">
                            KHS belum diupload
                        </div>
                    @endif
                </div>


            </div>
        </div>

        <!-- Form Edit -->
        <div class="col-12 col-xl-6">
            <div class="card mb-8">
                <div class="card-header">
                    <div class="row">
                        <h5>Data Perwalian</h5>
                    </div>
                    <div>
                        <form action="{{ route('perwalian.update', $perwalian->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="esign_x" id="esign_x">
                            <input type="hidden" name="esign_y" id="esign_y">

                            <div class="mb-4">
                                <label class="form-label">Nama Mahasiswa</label>
                                <input type="text" class="form-control" value="{{ $perwalian->student->name }}" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">IPK</label>
                                <input type="text" class="form-control" value="{{ $perwalian->ipk }}" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Keluhan</label>
                                <textarea class="form-control" rows="3" readonly>{{ $perwalian->keluhan }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Masukan Dosen Wali</label>
                                <textarea name="masukan" class="form-control" rows="3" required>{{ old('masukan', $perwalian->masukan) }}</textarea>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('dataperwaliandosen') }}" class="btn btn-secondary">
                                    Kembali
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    Diterima
                                </button>
                            </div>

                        </form>
                    </div>


                </div>
            </div>

        </div>
    </div>
@endsection

@push('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            const url = "{{ $fullUrlFile }}";

            const pdf = await window.pdfjsLib.getDocument(url).promise;
            const page = await pdf.getPage(1);

            const scale = 1.3;
            window.__PDF_SCALE__ = scale;

            const viewport = page.getViewport({
                scale
            });

            const canvas = document.getElementById('pdf-canvas');
            const ctx = canvas.getContext('2d');

            canvas.width = viewport.width;
            canvas.height = viewport.height;

            await page.render({
                canvasContext: ctx,
                viewport
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            // Init Draggable
            interact('#signature').draggable({
                listeners: {
                    move(event) {
                        const target = event.target;
                        // Simpan data translasi (geser)
                        const x = (parseFloat(target.dataset.x) || 0) + event.dx;
                        const y = (parseFloat(target.dataset.y) || 0) + event.dy;

                        // Update visual elemen
                        target.style.transform = `translate(${x}px, ${y}px)`;
                        target.dataset.x = x;
                        target.dataset.y = y;
                    }
                }
            });

            // Logic saat form akan disubmit
            const form = document.querySelector(
                'form[action="{{ route('perwalian.update', $perwalian->id) }}"]');

            form.addEventListener('submit', function(e) {
                // Ambil elemen
                const signature = document.getElementById('signature');
                const canvas = document.getElementById('pdf-canvas');

                // Dapatkan posisi kotak visual (Bounding Client Rect)
                // Ini otomatis menghitung scale CSS, scroll, dan transform
                const sigRect = signature.getBoundingClientRect();
                const canvRect = canvas.getBoundingClientRect();

                // Hitung posisi relatif signature terhadap canvas
                const relativeX = sigRect.left - canvRect.left;
                const relativeY = sigRect.top - canvRect.top;

                // Konversi ke Rasio (0.0 sampai 1.0)
                // Contoh: Jika canvas lebar 1000px dan posisi di 500px, xRatio = 0.5
                const xRatio = relativeX / canvRect.width;
                const yRatio = relativeY / canvRect.height;

                // Masukkan ke input hidden
                document.getElementById('esign_x').value = xRatio;
                document.getElementById('esign_y').value = yRatio;

                // Debugging (Opsional, bisa dihapus)
                console.log("X Ratio:", xRatio, "Y Ratio:", yRatio);
            });
        });
    </script>
@endpush
