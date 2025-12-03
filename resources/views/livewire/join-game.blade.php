<div class="container mx-auto">
    <div class="flex flex-col gap-2 justify-center items-center w-full">
        <h1 class="text-center text-2xl font-bold bg-forest-500 text-pure-white px-4 py-8 mb-8 w-full">Meedoen met het spel</h1>
        
        @if($step === 1)
            <form wire:submit="checkPin" class="flex flex-col gap-2 bg-forest-500 rounded-card p-4  justify-center items-center">
                <div class="flex flex-col gap-2 content-center items-center text-white">
                    <label for="pin">Game PIN</label>
                    <input 
                        type="text" 
                        id="pin" 
                        wire:model="pin" 
                        maxlength="6" 
                        placeholder="Voer hier de game PIN in"
                        autofocus
                        class="bg-forest-300 text-white rounded-card p-2 w-full placeholder:text-white border-white"
                    >
                    @error('pin') <span>{{ $message }}</span> @enderror
                </div>
                
                <button type="submit" class="bg-action-500 text-pure-white font-semibold text-small py-2 px-3 rounded-button text-center shadow-card transition">
                    Volgende
                </button>
            </form>
        @endif

        @if($step === 2)
            <div class="flex flex-col bg-forest-500 rounded-card p-4 justify-center items-center text-white">
                <p>Game PIN: <strong>{{ $pin }}</strong></p>
                <button wire:click="backToPin" type="button" class="bg-action-500 text-pure-white font-semibold text-small py-2 px-3 rounded-button text-center shadow-card transition">
                    Andere PIN gebruiken
                </button>
            </div>

            <form wire:submit="join" class="flex flex-col gap-2 bg-forest-500 rounded-card p-4 justify-center items-center">
                <div class="flex flex-col gap-2 content-center items-center text-white">
                    <label for="name">Gebruikersnaam</label>
                    <input 
                        type="text" 
                        id="name" 
                        wire:model="name" 
                        maxlength="20" 
                        placeholder="Voer hier je gebruikersnaam in"
                        autofocus
                        class="bg-forest-300 text-white rounded-card p-2 w-full placeholder:text-white border-white"
                        >
                    @error('name') <span>{{ $message }}</span> @enderror
                </div>
                
                <button type="submit" class="bg-action-500 text-pure-white font-semibold text-small py-2 px-3 rounded-button text-center shadow-card transition">
                    Meedoen met het spel
                </button>
            </form>
        @endif

        @if(session()->has('error'))
            <div>{{ session('error') }}</div>
        @endif
    </div>
</div>