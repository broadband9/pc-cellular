<x-dynamic-component
    :component="$getFieldWrapperView()"
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    <div
        x-data="{
            signaturePad: null,
            init() {
                this.signaturePad = new SignaturePad($refs.canvas);
                // Use Alpine's $watch to watch Livewire data changes
                $watch('$wire.entangle(\'' + '{{ $getStatePath() }}' + '\')', value => {
                    if (value) {
                        this.signaturePad.fromDataURL(value);
                    } else {
                        this.signaturePad.clear();
                    }
                });
            },
            clear() {
                this.signaturePad.clear();
                $wire.set('{{ $getStatePath() }}', null);
            },
           save() {
    if (this.signaturePad.isEmpty()) {
        return;
    }
    const dataURL = this.signaturePad.toDataURL();
    console.log('Signature Data URL:', dataURL);  // Log the data URL to ensure it's correct
    $wire.set('{{ $getStatePath() }}', dataURL);
}


        }"
        x-init="init"
        x-on:signature-saved.window="save"
    >
        <canvas x-ref="canvas" class="border border-gray-300 rounded-lg"></canvas>
        <div class="mt-2">
            <button type="button" x-on:click="clear" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Clear
            </button>
            <button type="button" x-on:click="save" class="px-4 py-2 text-sm font-medium text-dark bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Save Signature
            </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
</x-dynamic-component>
