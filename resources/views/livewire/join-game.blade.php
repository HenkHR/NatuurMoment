<div class="min-h-screen flex flex-col justify-between">

    <x-nav.join-nav/>

    <div class="flex flex-col gap-2 justify-center items-center container mx-auto">
        
        @if($step === 1)
            <form wire:submit="checkPin" class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 flex flex-col gap-6 bg-sky-500 rounded-card p-4 h-fit w-full max-w-72 justify-center items-center">
                <div class="flex flex-col content-center items-center text-white">

                    <label for="pin" class="text-center text-xl font-bold">Roomcode</label>
                    <p class="w-full text-center text-sm">Vul hier de roomcode in die je van de organisator hebt gekregen</p>

                    <input 
                        type="text" 
                        id="pin" 
                        wire:model="pin" 
                        maxlength="6"
                        autocomplete="off"  
                        placeholder="6-cijferige code"
                        autofocus
                        inputmode="numeric"
                        class="bg-sky-300 text-white rounded-card p-2 mt-4 w-full placeholder:text-white border-white"
                    >

                    @error('pin') <span>{{ $message }}</span> @enderror
                </div>
                
                <button type="submit" class="bg-white text-sky-500 font-semibold text-small py-2 px-3 w-1/2 rounded-button text-center shadow-card transition">
                    Volgende
                </button>
            </form>
        @endif

        @if($step === 2)
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 flex flex-col justify-center items-center content-center gap-8 w-full">
            <div class="flex flex-col bg-sky-500 rounded-card p-4 gap-2 justify-center items-center text-white max-w-72 w-full">
                <p>Game PIN: <strong>{{ $pin }}</strong></p>
                <button wire:click="backToPin" type="button" class="bg-white text-sky-500 font-semibold text-small py-2 px-3 rounded-button text-center shadow-card transition">
                    Andere PIN gebruiken
                </button>
            </div>

            <form wire:submit="join" class="flex flex-col gap-2 bg-sky-500 rounded-card p-4 justify-center max-w-72 w-full items-center">
                <div class="flex flex-col gap-2 content-center items-center text-white">
                    <label for="name" class="text-center text-xl font-bold">Gebruikersnaam</label>
                    <p class="w-full text-center text-sm">Voer hier je gebruikersnaam in</p>
                    <input 
                        type="text" 
                        id="name"
                        wire:model="name" 
                        maxlength="20" 
                        placeholder="Gebruikersnaam"
                        inputmode="text"
                        autocomplete="name" 
                        autofocus
                        class="bg-sky-300 text-white rounded-card p-2 w-full placeholder:text-white border-white"
                        >
                    @error('name') <span>{{ $message }}</span> @enderror
                </div>
                
                <button type="submit" class="bg-white text-sky-500 font-semibold text-small py-2 px-3 mt-4 rounded-button text-center shadow-card transition">
                    Meedoen met het spel
                </button>
            </form>

        </div>
        @endif

        @if(session()->has('error'))
            <div>{{ session('error') }}</div>
        @endif
    </div>

    <x-homeFooter/>
</div>