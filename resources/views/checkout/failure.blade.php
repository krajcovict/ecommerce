<x-app-layout>
    <div class="p-5">
        <div class="w-[400px] mx-auto bg-red-500 py-2 px-3 text-white rounded">
            <h1>Payment wasn't successful.</h1>
            <p>{{$message ?? ''}}</p>
        </div>
    </div>
</x-app-layout>
