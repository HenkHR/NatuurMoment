<div>
    <div>
        <h1>Meedoen met het spel</h1>
        
        <!-- form hoort bij de join functie in JoinGame.php -- livewire gekkeheid -->
        <form wire:submit="join">
            <div>
                <label for="pin">Game PIN</label>
                <input type="text" id="pin" wire:model="pin" maxlength="6" placeholder="Voer hier de game PIN in">
                @error('pin') <span>{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label for="name">Gebruikersnaam</label>
                <input type="text" id="name" wire:model="name" maxlength="20" placeholder="Voer hier je gebruikersnaam in">
                @error('name') <span>{{ $message }}</span> @enderror
            </div>
            
            <button type="submit">
                Meedoen met het spel
            </button>
        </form>
    </div>
</div>