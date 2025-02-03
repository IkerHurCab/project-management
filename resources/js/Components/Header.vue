<template>
    <header class="flex items-center justify-between pt-8 px-8 border-b-2 border-gray-800 pb-4 bg-black">
        <h1 class="text-4xl">{{ page }}</h1>
        <div class="flex gap-4 items-center">
            <div class="flex items-center min-h-12">
                <label for="theme-switch" class="flex items-center cursor-pointer">
                    <div class="relative">
                        <input id="theme-switch" type="checkbox" class="sr-only" />
                        <div class="block bg-gray-800 w-24 min-h-12 rounded-full"></div>
                        <div class="absolute text-white flex items-center gap-4 top-3 left-4">
                            <box-icon name="moon" color="#FFFFFF"></box-icon>
                            <box-icon name="sun" color="#FFFFFF"></box-icon>
                        </div>
                    </div>
                </label>
            </div>

            <div
                class="p-4 hover:bg-gray-900 hover:border-b hover:border-b border-white hover:cursor-pointer transition duration-300 relative min-w-12 min-h-12">
                <box-icon :name="'bell'" :color="isHovered ? 'white' : 'gray'"
                    class="w-7 h-7 absolute top-2.5 left-2.5"></box-icon>
            </div>
            <div
                class="border-2 border-green-500 rounded-full hover:bg-gray-900 hover:border-b hover:cursor-pointer transition duration-300 relative min-w-15 min-h-15">
                <box-icon :name="'user'" :color="'gray'" class="w-8 h-8 absolute top-3 left-3"></box-icon>
                <div class="absolute bg-green-500 min-h-4 min-w-4 rounded-full top-10 right-0 border-2 border-white"></div>
            </div>
        </div>
    </header>

</template>

<script>
import 'boxicons'
export default {
    props: {
        page: String
    }
}
</script>
