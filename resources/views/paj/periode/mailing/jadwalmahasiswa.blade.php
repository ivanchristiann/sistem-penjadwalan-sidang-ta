<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=\, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- Core CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <title>Jadwal Sidang untuk Mahasiswa</title>
    <style>
        body {
            font-family: 'sans-serif' !important;
        }
        table, th, td{
            border: 1px solid black;
            border-collapse: collapse;
        }
    </style>
</head>

<body>
    <div>
        <p class="text-center" style="font-family:'sans-serif'; font-size:28pt; color: #000088;"><strong>JADWAL SIDANG PERIODE {{ $periodeBulanTahun }}</strong></p>
        <br><br>
        <table class="table">
            <thead>
                <th>No</th>
                <th>NRP</th>
                <th>Nama</th>
                <th>Hari</th>
                <th>Tanggal</th>
                <th>Jam</th>
                <th>Ruang</th>
                <th>Pembimbing 1</th>
                <th>Pembimbing 2</th>
            </thead>
            <tbody>
                @foreach ($sidangs as $key => $sidang)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $sidang->mahasiswa->nrp }}</td>
                        <td>{{ $sidang->mahasiswa->nama }}</td>
                        <td>{{ $sidang->hari }}</td>
                        <td>{{ $sidang->formattanggal }}</td>
                        <td>{{ $sidang->formatslot }}</td>
                        <td>{{ $sidang->ruangan->nama }}</td>
                        <td>{{ $sidang->pembimbingsatu->nama }}</td>
                        <td>{{ $sidang->pembimbingdua->nama }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <p class="fs-5 fw-bold">File ini dibuat pada tanggal: {{ date('d-M-Y H:i:s') }}</p>
        <br><br><br>
        <p class="text-center">Teknik Informatika - Universitas Surabaya</p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous">
    </script>
</body>

</html>
