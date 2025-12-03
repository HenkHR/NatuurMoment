<div>
    <div>
        <h1>Meedoen met het spel</h1>
        
        @if($step === 1)
            <form wire:submit="checkPin">
                <div>
                    <label for="pin">Game PIN</label>
                    <input 
                        type="text" 
                        id="pin" 
                        wire:model="pin" 
                        maxlength="6" 
                        placeholder="Voer hier de game PIN in"
                        autofocus
                    >
                    @error('pin') <span>{{ $message }}</span> @enderror
                </div>
                
                <button type="submit">
                    Volgende
                </button>
            </form>
        @endif

        @if($step === 2)
            <div>
                <p>Game PIN: <strong>{{ $pin }}</strong></p>
                <button wire:click="backToPin" type="button">
                    Andere PIN gebruiken
                </button>
            </div>

            <form wire:submit="join">
                <div>
                    <label for="name">Gebruikersnaam</label>
                    <input 
                        type="text" 
                        id="name" 
                        wire:model="name" 
                        maxlength="20" 
                        placeholder="Voer hier je gebruikersnaam in"
                        autofocus
                    >
                    @error('name') <span>{{ $message }}</span> @enderror
                </div>
                
                <button type="submit">
                    Meedoen met het spel
                </button>
            </form>
        @endif

        @if(session()->has('error'))
            <div>{{ session('error') }}</div>
        @endif
    </div>
</div>