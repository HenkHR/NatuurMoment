@props(['timerEndsAt' => null])

@if($timerEndsAt)
<div
    x-data="{
        timerEndsAt: new Date('{{ $timerEndsAt }}').getTime(),
        timeRemaining: 0,
        interval: null,

        init() {
            this.updateTime();
            this.interval = setInterval(() => {
                this.updateTime();
            }, 1000);
        },

        updateTime() {
            const now = Date.now();
            this.timeRemaining = Math.max(0, Math.floor((this.timerEndsAt - now) / 1000));

            if (this.timeRemaining <= 0) {
                clearInterval(this.interval);
            }
        },

        destroy() {
            if (this.interval) {
                clearInterval(this.interval);
            }
        },

        formatTime() {
            const hours = Math.floor(this.timeRemaining / 3600);
            const minutes = Math.floor((this.timeRemaining % 3600) / 60);
            const seconds = this.timeRemaining % 60;

            if (hours > 0) {
                return `${hours}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            }
            return `${minutes}:${seconds.toString().padStart(2, '0')}`;
        },

        getColorClass() {
            if (this.timeRemaining <= 60) {
                return 'text-red-600 animate-pulse';
            } else if (this.timeRemaining <= 300) {
                return 'text-action-500';
            }
            return 'text-white';
        }
    }"
    x-on:destroy.window="destroy()"
    {{ $attributes->merge(['class' => 'flex items-center gap-2']) }}>

    <!-- Timer Icon -->
    <svg class="w-5 h-5" :class="getColorClass()" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>

    <!-- Timer Display -->
    <span
        class="font-bold text-lg tabular-nums"
        :class="getColorClass()"
        x-text="formatTime()">
    </span>
</div>
@endif
