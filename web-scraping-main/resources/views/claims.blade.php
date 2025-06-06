<x-app-layout>
    

    <div class="p-6">
        <div class="bg-white overflow-hidden shadow-md sm:rounded-lg">

            
                {{-- <div class="px-6 pt-6 pb-8">
                    <button
                        onclick="Livewire.dispatch('openModal', { component: 'form-nota-fiscal-modal'})"
                        class="rounded-md bg-kblue text-slate-50 font-bold px-10 py-2">
                        Nova NF
                    </button>
                </div> --}}
           

            
                <div class="p-6">
                    <livewire:claims-table/>
                </div>
            

        </div>
    </div>
</x-app-layout>