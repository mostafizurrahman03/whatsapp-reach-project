<a href="{{ asset('storage/sample_files/recipients_sample.csv') }}" 
   class="text-green-600 hover:text-green-800 underline transition-colors duration-200"
   target="_blank"
   download="recipients_sample.csv">
    Download Sample CSV
</a>






<!-- <div x-data wire:ignore>
    <button
        type="button"
        @click="$dispatch('download-sample-csv')"
        class="text-green-600 hover:text-green-800 underline transition-colors duration-200"
    >
        Download Sample CSV
    </button>
</div>

@once
@push('scripts')
<script>
    document.addEventListener('download-sample-csv', function () {
        const csvData = `number
880100000001
880100000002
880100000003`;

        const blob = new Blob([csvData], { type: 'text/csv' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'recipients_sample.csv';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(link.href);
    });
</script>
@endpush
@endonce -->





