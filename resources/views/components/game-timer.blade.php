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
            // Show only minutes (rounded up)
            const minutes = Math.ceil(this.timeRemaining / 60);
            return `${minutes}`;
        },

        getColorClass() {
            if (this.timeRemaining <= 60) {
                return 'text-red-600 animate-pulse';
            } else if (this.timeRemaining <= 300) {
                return 'text-orange-600';
            }
            return 'text-black';
        }
    }"
    x-on:destroy.window="destroy()"
    {{ $attributes->merge(['class' => 'flex items-center justify-center']) }}>

    <!-- Timer Display with Circular Background -->
    <div class="w-12 h-12 rounded-full bg-gray-100 bg-opacity-90 flex items-center justify-center shadow-lg">
        <span
            class="font-bold text-lg tabular-num text-black text-center"
            :class="getColorClass()"
            x-text="formatTime()">
        </span>
    </div>
</div>
@endif
