<template>
    <header class="flex items-center justify-between pt-8 px-8 border-b-2 border-gray-700 pb-4">
        <h1 class="text-4xl">{{ page }}</h1>
        <div class="gap-4 bg-gray-800 rounded-full p-4 hover:bg-gray-900 hover:cursor-pointer transition duration-300 relative min-w-12 min-h-12">
            <box-icon :name="'bell'" :color="'#FFFFFF'" class="w-7 h-7 absolute top-2.5 left-2.5"></box-icon>
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
