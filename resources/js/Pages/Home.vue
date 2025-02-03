<script setup>
import { ref, onMounted } from 'vue';

const text = ref('');
const fullText = 'Gestiona tus proyectos de forma eficiente';
const words = ['eficiente', 'sencilla', 'segura', 'rápida', 'ordenada'];
let currentWordIndex = 0;

onMounted(() => {
    let index = 0;
    const interval = setInterval(() => {
        if (index < fullText.length) {
            text.value += fullText[index];
            index++;
        } else {
            clearInterval(interval);
            setTimeout(replaceLastWord, 750);
        }
    }, 75);
});

function replaceLastWord() {
    const baseText = 'Gestiona tus proyectos de forma ';
    let currentWord = words[currentWordIndex];
    let wordIndex = currentWord.length;

    const interval = setInterval(() => {
        if (wordIndex > 0) {
            text.value = baseText + currentWord.slice(0, wordIndex - 1);
            wordIndex--;
        } else {
            currentWordIndex = (currentWordIndex + 1) % words.length;
            currentWord = words[currentWordIndex];
            wordIndex = currentWord.length;
            setTimeout(() => {
                const addInterval = setInterval(() => {
                    if (wordIndex > 0) {
                        text.value = baseText + currentWord.slice(0, currentWord.length - wordIndex + 1);
                        wordIndex--;
                    } else {
                        clearInterval(addInterval);
                        setTimeout(replaceLastWord, 750);
                    }
                }, 75);
            }, 0);
            clearInterval(interval);
        }
    }, 100);
}
</script>

<template>
    <div class="flex items-center justify-center h-screen">
        <h1 class="text-7xl font-apple flex items-center">{{ text }}<span class="blinking-cursor font-thin">|</span></h1>
    </div>
</template>

<style>
.blinking-cursor {
    display: inline-block;
    animation: blink 1s step-end infinite;
}

@keyframes blink {
    from, to {
        display: inline-block;
    }
    50% {
        display: none;
    }
}
</style>
