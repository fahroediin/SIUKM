<?php
require_once 'koneksi.php';
require_once 'session.php';
require_once 'headernav.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Warung Sembako Soraya Berkah</title>
    <link rel="stylesheet" href="assets/plugins/icons/feather/feather.css">
    <link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css">
</head>
<body>
    <div class="main-wrapper">
        <div class="page-wrapper">
            <div class="content container-fluid">
                <div class="page-header invoices-page-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <ul class="breadcrumb invoices-breadcrumb">
                                <li class="breadcrumb-item invoices-breadcrumb-item">
                                    <a href="transaksi.php">
                                        <i class="fe fe-chevron-left"></i> Kembali ke Daftar Transaksi
                                    </a>
                                </li>
                            </ul>
                        </div>
                        
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card invoices-add-card">
                            <div class="card-body">
                                <div class="col-xl-3 col-md-6 col-sm-12 col-12">
                                    <div class="invoice-details-box">
                                        <div class="invoice-inner-footer">
                                            <div class="invoice-inner-date">
                                                <span>Tanggal transaksi <input class="form-control datetimepicker" id="tanggal-transaksi" type="text" placeholder="15/02/2022"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <script>
                                    // Tambahkan event listener untuk menangkap saat halaman selesai dimuat
                                    document.addEventListener("DOMContentLoaded", function () {
                                        // Dapatkan elemen input tanggal
                                        const tanggalTransaksiInput = document.getElementById("tanggal_transaksi");

                                        // Dapatkan tanggal saat ini dalam format yyyy-mm-dd
                                        const today = new Date().toISOString().slice(0, 10);

                                        // Set nilai input tanggal dengan tanggal saat ini
                                        tanggalTransaksiInput.value = today;
                                    });
                                </script>

                                <br>
                                <form action="proses_transaksi.php" method="post" class="invoices-form" name="transaksi-form">
                                    <div class="invoices-main-form">
                                        <div class="row">

                                            <!-- Add id attribute to the select element -->
                                            <div class="col-xl-4 col-md-6 col-sm-12 col-12">
                                                <div class="form-group">
                                                    <label>Nama Barang</label>
                                                    <div class="multipleSelection">
                                                        <div class="selectbox">
                                                            <p class="mb-0" id="pilih-barang">Pilih Barang</p>
                                                            <span class="down-icon"><i data-feather="chevron-down"></i></span>
                                                        </div>
                                                            <div id="checkboxes-one">
                                                                <p class="checkbox-title">Cari Barang</p>
                                                                <div class="form-custom">
                                                                    <input type="text" class="form-control bg-grey" id="search-barang" placeholder="Cari Nama Barang">
                                                                </div>
                                                                <div class="selectbox-cont" id="barang-container">
                                                                    <?php
                                                                    // Query untuk mendapatkan data barang dari tabel "barang"
                                                                    $query = "SELECT nama FROM barang";
                                                                    $result = mysqli_query($koneksi, $query);
                                                                    // Tampilkan data barang dalam bentuk checkbox dengan label terpisah
                                                                    while ($row = mysqli_fetch_assoc($result)) {
                                                                        echo "<label class='custom_check w-100'>";
                                                                        echo "<input type='checkbox' class='barang-checkbox' name='barang[]' value='" . $row['nama'] . "'>";
                                                                        echo "<span class='checkmark'></span><span class='nama-barang'>" . $row['nama'] . "</span>";
                                                                        echo "</label>";
                                                                    }
                                                                    // Tutup koneksi
                                                                    mysqli_close($koneksi);
                                                                    ?>
                                                                </div>
                                                                <button type="button" id="bersihkan" class="btn w-100 btn-grey">Bersihkan</button>
                                                            </div>

                                                            <script>
                                                                // Ambil input elemen berdasarkan ID
                                                                const searchBarang = document.getElementById('search-barang');
                                                                const namaBarang = document.getElementsByClassName('nama-barang');

                                                                // Fungsi untuk mengatur tampilan elemen berdasarkan teks yang cocok
                                                                function filterBarang() {
                                                                    const keyword = searchBarang.value.toLowerCase();
                                                                    for (let i = 0; i < namaBarang.length; i++) {
                                                                        const nama = namaBarang[i].innerText.toLowerCase();
                                                                        const parentLabel = namaBarang[i].parentElement;
                                                                        if (nama.includes(keyword)) {
                                                                            parentLabel.style.display = 'block';
                                                                        } else {
                                                                            parentLabel.style.display = 'none';
                                                                        }
                                                                    }
                                                                }

                                                                // Tambahkan event listener untuk memanggil fungsi filter saat pengguna mengisi kolom cari
                                                                searchBarang.addEventListener('input', filterBarang);

                                                                // Fungsi untuk membersihkan kolom cari dan menampilkan semua barang kembali
                                                                document.getElementById('bersihkan').addEventListener('click', function () {
                                                                    searchBarang.value = '';
                                                                    for (let i = 0; i < namaBarang.length; i++) {
                                                                        namaBarang[i].parentElement.style.display = 'block';
                                                                    }
                                                                });
                                                            </script>

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-xl-4 col-md-6 col-sm-12 col-12">
                                                <div class="form-group">
                                                    <label>Harga Satuan</label>
                                                    <input class="form-control" type="text" id="harga-satuan" placeholder="Harga Satuan" readonly>
                                                </div>
                                            </div>

                                            <script>
                                                // Assuming you have included jQuery for simplicity
                                                $(document).ready(function() {
                                                    // Tambahkan event listener pada setiap checkbox
                                                    const barangCheckboxes = document.querySelectorAll(".barang-checkbox");
                                                    barangCheckboxes.forEach(function (checkbox) {
                                                        checkbox.addEventListener("change", function () {
                                                            // Uncheck semua checkbox
                                                            barangCheckboxes.forEach(function (cb) {
                                                                if (cb !== checkbox) {
                                                                    cb.checked = false;
                                                                }
                                                            });

                                                            // Set nilai "Pilih Barang" sesuai dengan checkbox yang dicentang
                                                            const pilihBarang = document.querySelector(".selectbox p");
                                                            if (checkbox.checked) {
                                                                pilihBarang.textContent = "Pilih Barang: " + checkbox.value;
                                                            } else {
                                                                pilihBarang.textContent = "Pilih Barang";
                                                            }

                                                            var selectedBarang = checkbox.value;
                                                            if (selectedBarang !== '') {
                                                                $.ajax({
                                                                    url: 'get_harga_jual.php',
                                                                    method: 'POST',
                                                                    data: { barang: selectedBarang },
                                                                    success: function (response) {
                                                                        $('#harga-satuan').val(response);
                                                                    },
                                                                    error: function () {
                                                                        // Handle the error if needed
                                                                        alert('Failed to fetch harga-jual from the database.');
                                                                    }
                                                                });
                                                            } else {
                                                                // If no barang is selected, clear the input field
                                                                $('#harga-satuan').val('');
                                                            }
                                                            $('#bersihkan').click(function() {
                                                                // Hapus pilihan barang
                                                                $('.barang-checkbox').prop('checked', false);
                                                                $('#pilih-barang').text('Pilih Barang');

                                                                // Bersihkan harga satuan
                                                                $('#harga-satuan').val('');
                                                            });
                                                        });
                                                    });
                                                });
                                            </script>

                                            <div class="col-xl-4 col-md-6 col-sm-12 col-12">
                                                <div class="form-group">
                                                    <label>Jumlah Barang</label>
                                                    <input class="form-control" type="text" id="jumlah-barang" placeholder="Jumlah Barang">
                                                </div>
                                            </div>
                                            <div class="upload-sign">
                                                <div class="form-group float-end mb-0">
                                                    <button class="btn btn-primary" type="button" id="tambah-barang">Tambah</button>
                                                </div>
                                            </div>
                                            </div>
                                            </div>

                                            <div class="invoice-add-table">
                                                <h4>Detail Barang</h4>
                                                <div class="table-responsive">
                                                    <input type="hidden" id="tableData" name="tableData">
                                                    <table class="table table-center add-table-items">
                                                        <thead>
                                                            <tr>
                                                                <th>Nama Barang</th>
                                                                <th>Harga Barang</th>
                                                                <th>Jumlah Barang</th>
                                                                <th>Total Harga</th>
                                                                <th>Aksi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="table-body">
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <script>
                                                // Ambil tombol "Tambah"
                                                const tambahBarangBtn = document.getElementById('tambah-barang');

                                                // Fungsi untuk menambahkan baris ke dalam tabel
                                                function addRowToTable(nama, harga, jumlah, totalHarga) {
                                                    const tableBody = document.getElementById('table-body');
                                                    const newRow = document.createElement('tr');
                                                    const rowId = Date.now(); // Atribut unik untuk setiap baris

                                                    newRow.innerHTML = `
                                                        <td>${nama}</td>
                                                        <td>${harga}</td>
                                                        <td>${jumlah}</td>
                                                        <td>${totalHarga}</td>
                                                        <td class="add-remove text-end">
                                                            <a href="" class="remove-btn" data-id="${rowId}"><i class="fe fe-trash-2"></i></a>
                                                        </td>
                                                    `;

                                                    tableBody.appendChild(newRow);
                                                }

                                                // Tambahkan event listener untuk tombol "Tambah"
                                                tambahBarangBtn.addEventListener('click', function () {
                                                    const namaBarangInput = document.getElementById('pilih-barang').textContent.trim();

                                                    // Cek apakah input tidak kosong dan hilangkan awalan "Pilih Barang:"
                                                    const namaBarang = namaBarangInput.replace('Pilih Barang:', '').trim();
                                                    const hargaBarangInput = document.getElementById('harga-satuan').value.trim();
                                                    const jumlahBarangInput = document.getElementById('jumlah-barang').value.trim();

                                                    // Cek apakah input tidak kosong
                                                    if (namaBarang === '' || hargaBarangInput === '' || jumlahBarangInput === '') {
                                                        alert('Harap isi semua kolom sebelum menambahkan barang.');
                                                        return;
                                                    }

                                                    // Konversi jumlah barang menjadi tipe data numerik (float)
                                                    const jumlahBarang = parseFloat(jumlahBarangInput);

                                                    // Cek apakah jumlah barang valid (positif dan bukan NaN)
                                                    if (isNaN(jumlahBarang) || jumlahBarang <= 0) {
                                                        alert('Jumlah barang harus berupa angka positif.');
                                                        return;
                                                    }

                                                    // Hitung total harga
                                                    const totalHarga = parseFloat(hargaBarangInput) * jumlahBarang;

                                                    // Tambahkan baris ke dalam tabel
                                                    addRowToTable(namaBarang, hargaBarangInput, jumlahBarang, totalHarga);

                                                    // Kosongkan input setelah ditambahkan ke dalam tabel
                                                    document.getElementById('pilih-barang').textContent = 'Pilih Barang';
                                                    document.getElementById('harga-satuan').value = '';
                                                    document.getElementById('jumlah-barang').value = '';
                                                });

                                                // Fungsi untuk menghapus baris dari tabel
                                                function removeRowFromTable(event) {
                                                        event.preventDefault(); // Tambahkan ini untuk mencegah halaman berpindah saat tombol "Hapus" diklik

                                                        const removeButton = event.target.closest('.remove-btn');
                                                        if (removeButton) {
                                                            const rowId = removeButton.dataset.id;
                                                            const rowToRemove = document.querySelector(`[data-id="${rowId}"]`);
                                                            if (rowToRemove) {
                                                                rowToRemove.closest('tr').remove();
                                                            }
                                                        }
                                                    }

                                                    // Tambahkan event listener untuk tombol "Hapus"
                                                    const tableBody = document.getElementById('table-body');
                                                    tableBody.addEventListener('click', removeRowFromTable); // Ubah ini agar event listener hanya memanggil fungsi removeRowFromTable
                                            </script>

                                    <div class="row justify-content-end">
                                        <div class="col-lg-5 col-md-6">
                                            <div class="invoice-total-card">
                                                <h4 class="invoice-total-title">Input Pembayaran</h4>
                                                <div class="invoice-total-box">
                                                    <div class="invoice-total-inner">
                                                        <p>Total Harga <span id="total-harga-placeholder">Rp. 0.00</span></p>
                                                        <p>Bayar<span><input type="number" id="input-bayar" placeholder="Masukkan jumlah pembayaran" oninput="hitungKembalian()" ></span></p>
                                                    </div>
                                                    <div class="invoice-total-footer">
                                                        <h4>Kembalian <span id="kembalian">Rp. 0.00</span></h4>
                                                    </div>
                                                </div>
                                                <div class="upload-sign">
                                                    <div class="form-group float-end mb-0">
                                                    <button type="submit" name="simpan-transaksi" value="<?php echo isset($_GET['simpan']); ?>" class="btn btn-primary">Simpan Transaksi</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <script>
                                        // Fungsi untuk menghitung total harga dari seluruh barang di tabel
                                        function calculateTotalHarga() {
                                            const tableRows = document.querySelectorAll('#table-body tr');
                                            let totalHarga = 0;

                                            tableRows.forEach((row) => {
                                                const totalHargaCell = row.querySelector('td:nth-child(4)');
                                                totalHarga += parseFloat(totalHargaCell.textContent);
                                            });

                                            return totalHarga;
                                        }

                                        // Fungsi untuk memperbarui total harga pada "Input Pembayaran" dan "Detail Barang"
                                        function updateTotalHarga() {
                                            const totalHargaPlaceholder = document.getElementById('total-harga-placeholder');
                                            totalHargaPlaceholder.textContent = 'Rp. ' + calculateTotalHarga().toFixed(2);
                                        }

                                        // Fungsi untuk menghitung kembalian berdasarkan jumlah pembayaran
                                        function hitungKembalian() {
                                            const inputBayar = document.getElementById('input-bayar');
                                            const kembalianSpan = document.getElementById('kembalian');

                                            const totalHarga = calculateTotalHarga();
                                            const jumlahBayar = parseFloat(inputBayar.value);

                                            if (isNaN(jumlahBayar)) {
                                                kembalianSpan.textContent = 'Rp. 0.00';
                                            } else {
                                                const kembalian = jumlahBayar - totalHarga;
                                                kembalianSpan.textContent = 'Rp. ' + kembalian.toFixed(2);
                                            }
                                        }

                                        // Fungsi untuk menghapus baris dari tabel "Detail Barang"
                                        function removeRowFromTable(rowId) {
                                            const rowToRemove = document.querySelector(`[data-id="${rowId}"]`);
                                            if (rowToRemove) {
                                                rowToRemove.closest('tr').remove();
                                                updateTotalHarga(); // Perbarui total harga setelah menghapus baris
                                                hitungKembalian(); // Perbarui kembalian setelah menghapus baris
                                            }
                                        }

                                        // Fungsi untuk menambahkan baris ke dalam tabel
                                        function addRowToTable(nama, harga, jumlah, totalHarga) {
                                            const tableBody = document.getElementById('table-body');
                                            const newRow = document.createElement('tr');
                                            const rowId = Date.now(); // Atribut unik untuk setiap baris

                                            newRow.innerHTML = `
                                                <td>${nama}</td>
                                                <td>${harga}</td>
                                                <td>${jumlah}</td>
                                                <td>${totalHarga}</td>
                                                <td class="add-remove text-end">
                                                    <a href="#" class="remove-btn" data-id="${rowId}" onclick="removeRowFromTable(${rowId})"><i class="fe fe-trash-2"></i></a>
                                                </td>
                                            `;

                                            tableBody.appendChild(newRow);

                                            updateTotalHarga(); // Perbarui total harga setelah menambahkan baris
                                            hitungKembalian(); // Perbarui kembalian setelah menambahkan baris
                                        }

                                        // Panggil fungsi updateTotalHarga dan hitungKembalian saat halaman dimuat untuk pertama kali
                                        updateTotalHarga();
                                        // Fungsi untuk mendapatkan data tabel sebagai array dari objek
                                        <script>
    // Fungsi untuk mendapatkan data tabel sebagai array dari objek
    function getTableData() {
        const tableRows = document.querySelectorAll('#table-body tr');
        const tableData = [];

        tableRows.forEach(row => {
            const columns = row.getElementsByTagName('td');
            const rowData = {
                nama: columns[0].textContent,
                harga: parseFloat(columns[1].textContent),
                jumlah: parseFloat(columns[2].textContent),
                totalHarga: parseFloat(columns[3].textContent),
            };
            tableData.push(rowData);
        });

        return tableData;
    }

    // Tambahkan event listener untuk tombol "Simpan Transaksi"
    document.querySelector('.btn-primary').addEventListener('click', function () {
        const tanggalTransaksi = document.getElementById('tanggal-transaksi').value.trim();
        // Tambahkan data formulir lainnya seperti yang diperlukan...

        // Dapatkan data tabel
        const tableData = getTableData();

        // Simpan data tabel sebagai JSON dalam input field tersembunyi
        document.getElementById('tableData').value = JSON.stringify(tableData);

        // Kirimkan formulir
        document.querySelector('.invoices-form').submit();
    });
</script>




                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        // Ketika button Bersihkan diklik
        $(".btn-grey").click(function() {
            // Hilangkan centang pada semua checkbox
            $(".barang-checkbox").prop("checked", false);
        });
    });
</script>

<script src="assets/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<script src="assets/plugins/select2/js/select2.min.js"></script>
<script src="assets/plugins/moment/moment.min.js"></script>
<script src="assets/js/bootstrap-datetimepicker.min.js"></script>
<script src="assets/js/script.js"></script>
</body>
</html>