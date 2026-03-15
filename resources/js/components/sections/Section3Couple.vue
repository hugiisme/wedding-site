<template>
    <section
        data-section
        class="relative flex min-h-screen w-full shrink-0 snap-start snap-always items-center justify-center overflow-hidden bg-wedding-cream px-4 py-12"
    >
        <div
            class="grid w-full max-w-5xl grid-cols-1 gap-8 md:grid-cols-2 md:gap-12"
        >
            <!-- Chú rể - trái / dưới trên mobile -->
            <div
                class="reveal order-2 flex flex-col items-center md:order-1 md:items-end"
            >
                <div
                    class="relative h-72 w-56 overflow-hidden rounded-lg shadow-lg md:h-80 md:w-64"
                >
                    <img
                        src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&q=80"
                        alt="Chú rể"
                        class="h-full w-full object-cover"
                    />
                </div>
                <p
                    class="mt-4 font-serif text-xl font-semibold text-wedding-brown"
                >
                    Thành
                </p>
                <p class="text-sm text-wedding-brown/80">Chú rể</p>
            </div>
            <!-- Cô dâu - phải / trên trên mobile -->
            <div
                class="reveal order-1 flex flex-col items-center md:order-2 md:items-start"
            >
                <div
                    class="relative h-72 w-56 overflow-hidden rounded-lg shadow-lg md:h-80 md:w-64"
                >
                    <img
                        src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&q=80"
                        alt="Cô dâu"
                        class="h-full w-full object-cover"
                    />
                </div>
                <p
                    class="mt-4 font-serif text-xl font-semibold text-wedding-brown"
                >
                    Thu
                </p>
                <p class="text-sm text-wedding-brown/80">Cô dâu</p>
            </div>
        </div>
    </section>
</template>

<script setup></script>
