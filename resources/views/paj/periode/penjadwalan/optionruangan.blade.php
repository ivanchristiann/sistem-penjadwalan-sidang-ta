@if ($ruangans==null)
    <option selected disabled>Tidak Ada Ruangan Tersedia</option>
@else
    <option selected disabled>Pilih Ruangan</option>
    @foreach ($ruangans as $ruangan)
        <option value="{{ $ruangan->id }}">
            {{ $ruangan->nama }}</option>
    @endforeach
@endif
