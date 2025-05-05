<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if (session('success'))
                    <div 
                    x-data="{ show: true }" 
                    x-init="setTimeout(() => show = false, 1300)" 
                    x-show="show" 
                    class="mt-4 p-2 bg-green-100 text-green-700 rounded"
                >
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                    <div 
                    x-data="{ show: true }" 
                    x-init="setTimeout(() => show = false, 3000)" 
                    x-show="show" 
                    class="mt-4 p-2 bg-red-100 text-red-700 rounded"
                >
                            {{ session('error') }}
                        </div>
                    @endif

                    @if(isset($limit))
                        <p>Limite do Cartão: R$ {{ number_format($limit, 2, ',', '.') }}</p>
                    @else
                        <p class="text-lg font-semibold">Limite não disponível</p>
                    @endif


                    @if(!$hasCreditCard)
                        <div class="mt-4">
                        <form action="{{ route('creditCard.create') }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Criar Cartão de Crédito
                        </button>
                        </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
